<?php

declare(strict_types=1);

namespace App\Services\Search;

use Doctrine\DBAL\Connection;
use PDO;
use RuntimeException;
use TeamTNT\TNTSearch\Indexer\TNTIndexer;
use TeamTNT\TNTSearch\Stemmer\RussianStemmer;
use TeamTNT\TNTSearch\TNTSearch;

/** Keeps the mutable search database outside the document root and release source. */
final class FullTextSearchFactory
{
    public function __construct(
        private Connection $connection,
        private string $fullTextIndexPath
    ) {
        if (!str_starts_with($fullTextIndexPath, DIRECTORY_SEPARATOR)) {
            throw new RuntimeException('The full-text index path must be absolute.');
        }
    }

    public function getIndexPath(): string
    {
        return $this->fullTextIndexPath;
    }

    public function createReader(): TNTSearch
    {
        $search = $this->createSearch();
        $search->selectIndex(basename($this->fullTextIndexPath));

        return $search;
    }

    public function createIndexer(): TNTIndexer
    {
        $directory = dirname($this->fullTextIndexPath);
        if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
            throw new RuntimeException('Cannot create the full-text index directory.');
        }

        $search = $this->createSearch();
        $pdo = $this->connection->getNativeConnection();
        if (!$pdo instanceof PDO) {
            throw new RuntimeException('The full-text indexer requires a PDO connection.');
        }
        // Reuse Doctrine's parsed credentials, socket, charset and port.
        $search->setDatabaseHandle($pdo);

        return $search->createIndex(basename($this->fullTextIndexPath), true);
    }

    private function createSearch(): TNTSearch
    {
        $search = new TNTSearch();
        $search->loadConfig([
            'storage' => dirname($this->fullTextIndexPath).DIRECTORY_SEPARATOR,
            'stemmer' => RussianStemmer::class,
        ]);
        $search->fuzziness = true;

        return $search;
    }
}
