<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Services\Search\FullTextSearchFactory;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;

final class FullTextSearchTest extends TestCase
{
    public function testIndexerAndReaderUseTheSamePrivatePathIndependentOfWorkingDirectory(): void
    {
        $root = sys_get_temp_dir().'/epigraphy-search-'.bin2hex(random_bytes(8));
        $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $connection->executeStatement('CREATE TABLE documents (id INTEGER PRIMARY KEY, content TEXT)');
        $connection->insert('documents', ['id' => 1, 'content' => 'надпись на кресте']);
        $connection->insert('documents', ['id' => 2, 'content' => 'другая надпись']);
        $factory = new FullTextSearchFactory($connection, $root.'/private/fulltext.sqlite');
        $cwd = getcwd();
        try {
            chdir(sys_get_temp_dir());
            $indexer = $factory->createIndexer();
            $indexer->query('SELECT id, content FROM documents');
            $indexer->run();
            self::assertFileExists($root.'/private/fulltext.sqlite');
            $reader = $factory->createReader();
            $result = $reader->search('кресте', 10);
            self::assertSame([1], array_values($result['ids']));
            self::assertSame($root.'/private/fulltext.sqlite', $factory->getIndexPath());
        } finally {
            chdir($cwd);
            $connection->close();
            foreach (glob($root.'/private/*') ?: [] as $file) { unlink($file); }
            if (is_dir($root.'/private')) { rmdir($root.'/private'); }
            if (is_dir($root)) { rmdir($root); }
        }
    }
}
