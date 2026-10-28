<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Tests\Fixture\SortRecord;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Vyfony\Bundle\FilterableTableBundle\DataCollection\DataCollector;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\FilterConfiguratorInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Sorting\CustomSortConfiguration;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Sorting\DbSortConfiguration;

final class FilterableTableTest extends TestCase
{
    private EntityManager $manager;
    private ArrayAdapter $cache;
    private DataCollector $collector;

    protected function setUp(): void
    {
        $configuration = ORMSetup::createAttributeMetadataConfiguration([__DIR__.'/../Fixture'], true);
        $this->manager = EntityManager::create(['driver' => 'pdo_sqlite', 'memory' => true], $configuration);
        (new SchemaTool($this->manager))->createSchema([$this->manager->getClassMetadata(SortRecord::class)]);
        foreach (['Zulu', 'Alpha', 'Beta'] as $title) {
            $this->manager->persist(new SortRecord($title));
        }
        $this->manager->flush();
        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->willReturn($this->manager->getRepository(SortRecord::class));
        $filter = $this->createMock(FilterConfiguratorInterface::class);
        $filter->method('getFilterRestrictions')->willReturn([]);
        $filter->method('getFilterParameters')->willReturn([]);
        $this->cache = new ArrayAdapter();
        $this->collector = new DataCollector($registry, $this->cache, $filter);
    }

    protected function tearDown(): void
    {
        $this->manager->close();
    }

    private function collect(array $input)
    {
        return $this->collector->getRowDataCollection(
            $input + ['disablePagination' => false], SortRecord::class, static fn ($record) => $record->id,
            new DbSortConfiguration('id', true, 2, 2, 'All', ['title'])
        );
    }

    public function testValidSortingAndPaginationReachDoctrine(): void
    {
        $result = $this->collect(['sortBy' => 'title', 'sortOrder' => 'ASC', 'page' => '1']);
        self::assertSame(3, $result->getDataCount());
        self::assertSame(['Alpha', 'Beta'], array_map(static fn ($record) => $record->title, iterator_to_array($result->getData(), false)));
        $second = $this->collect(['sortBy' => 'title', 'sortOrder' => 'asc', 'page' => '2']);
        self::assertSame(['Zulu'], array_map(static fn ($record) => $record->title, iterator_to_array($second->getData(), false)));
    }

    #[DataProvider('invalidInputs')]
    public function testUnsafeInputIsRejectedBeforeQueryExecution(array $input): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->collect($input);
    }

    public static function invalidInputs(): array
    {
        return [
            'SQL in field' => [['sortBy' => 'id DESC, (SELECT 1)']],
            'unapproved field' => [['sortBy' => 'password']],
            'SQL in direction' => [['sortOrder' => 'desc, (SELECT 1)']],
            'array field' => [['sortBy' => ['id']]],
            'array direction' => [['sortOrder' => ['asc']]],
            'zero page' => [['page' => 0]],
            'negative page' => [['page' => -1]],
            'overflow page' => [['page' => '9999999999999999999999999']],
            'array page' => [['page' => ['1']]],
        ];
    }

    public function testExpiredSearchReferenceReturnsBadRequest(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->collect(['requestId' => str_repeat('a', 32)]);
    }

    public function testCachedSearchCannotLoopForever(): void
    {
        $reference = str_repeat('a', 32);
        $item = $this->cache->getItem($reference);
        $item->set(['requestId' => $reference]);
        $this->cache->save($item);
        $this->expectException(BadRequestHttpException::class);
        $this->collect(['requestId' => $reference]);
    }

    public function testExistingSearchReferenceStillWorks(): void
    {
        $first = $this->collect([]);
        $second = $this->collect(['requestId' => $first->getRequestId()]);
        self::assertSame(3, $second->getDataCount());
    }

    public function testCustomSortingRemainsIndependentOfDbSortInputs(): void
    {
        $result = $this->collector->getRowDataCollection([], SortRecord::class, static fn ($record) => $record->id,
            new CustomSortConfiguration(static function (array $records): array {
                usort($records, static fn ($a, $b) => strcmp($a->title, $b->title));
                return $records;
            }));
        self::assertSame(['Alpha', 'Beta', 'Zulu'], array_map(static fn ($record) => $record->title, $result->getData()));
    }
}
