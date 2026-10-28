<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SiteTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (!str_contains($_ENV['DATABASE_URL'] ?? '', '/epigraphy_php84_local?')) {
            self::markTestSkipped('Functional tests require the isolated local restored database.');
        }
    }

    #[DataProvider('pages')]
    public function testPublicPagesAndInvalidRequests(string $path, int $status): void
    {
        $client = static::createClient(['environment' => 'test']);
        $database = static::getContainer()->get('doctrine')->getConnection();
        self::assertSame('epigraphy_php84_local_test', $database->getDatabase());
        $database->executeStatement('SET SESSION TRANSACTION READ ONLY');
        $database->beginTransaction();
        try {
            $client->request('GET', 'https://epigraphica.ru'.$path);
            self::assertResponseStatusCodeSame($status);
            if ($status === 200 && str_contains($path, 'conventionalDateExactMatch=1')) {
                self::assertCount(1, $client->getCrawler()->filter('input[name="conventionalDateExactMatch"]'));
                self::assertSelectorExists('input[name="conventionalDateExactMatch"][checked]');
            }
        } finally {
            if ($database->isTransactionActive()) { $database->rollBack(); }
        }
    }

    public static function pages(): array
    {
        return [
            'root' => ['/', 301],
            'home' => ['/epigraphy/', 200],
            'catalog' => ['/epigraphy/inscription/longlist', 200],
            'inscription' => ['/epigraphy/inscription/show/1', 200],
            'unknown inscription' => ['/epigraphy/inscription/show/999999999', 404],
            'invalid inscription' => ['/epigraphy/inscription/show/not-a-number', 404],
            'bibliography' => ['/epigraphy/bibliography/record/list', 200],
            'login' => ['/epigraphy/login', 200],
            'about' => ['/epigraphy/about-site', 200],
            'news' => ['/epigraphy/news', 200],
            'legend' => ['/epigraphy/legend', 200],
            'api metadata' => ['/api/v1/corpus/metadata/', 200],
            'api plain-texts' => ['/api/v1/corpus/plain-texts/', 200],
            'api xml-texts' => ['/api/v1/corpus/xml-texts/', 200],
            'api texts' => ['/api/v1/corpus/texts/', 200],
            'api statistics' => ['/api/v1/corpus/statistics/', 200],
            'full text' => ['/epigraphy/inscription/longlist?fulltext='.rawurlencode('крест'), 200],
            'translation text' => ['/epigraphy/inscription/longlist?translation='.rawurlencode('рабу'), 200],
            'inscription text' => ['/epigraphy/inscription/longlist?text='.rawurlencode('рабу'), 200],
            'combined translation and text' => ['/epigraphy/inscription/longlist?translation='.rawurlencode('рабу').'&text='.rawurlencode('рабу'), 200],
            'valid date range' => ['/epigraphy/inscription/longlist?conventionalDateInitialYear=1100&conventionalDateFinalYear=1200', 200],
            'exact date range' => ['/epigraphy/inscription/longlist?conventionalDateInitialYear=1100&conventionalDateFinalYear=1200&conventionalDateExactMatch=1', 200],
            'browser date form' => ['/epigraphy/inscription/longlist?city=&number-in-source=&translation=&text=&conventionalDateExactMatch=1&conventionalDateInitialYear=949&conventionalDateFinalYear=1109&fulltext=&sortBy=id&sortOrder=desc&page=1&requestId=', 200],
            'unknown filter rejected' => ['/epigraphy/inscription/longlist?unexpectedFilter=1', 400],
            'malformed date checkbox rejected' => ['/epigraphy/inscription/longlist?conventionalDateInitialYear=949&conventionalDateFinalYear=1109&conventionalDateExactMatch[]=1', 400],
            'unsafe sort field' => ['/epigraphy/inscription/longlist?sortBy='.rawurlencode('id DESC, (SELECT 1)'), 400],
            'unsafe sort order' => ['/epigraphy/inscription/longlist?sortOrder='.rawurlencode('desc, (SELECT 1)'), 400],
            'negative page' => ['/epigraphy/inscription/longlist?page=-1', 400],
        ];
    }
}
