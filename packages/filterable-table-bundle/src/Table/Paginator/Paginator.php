<?php

declare(strict_types=1);

/*
 * This file is part of VyfonyFilterableTableBundle project.
 *
 * (c) Anton Dyshkant <vyshkant@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vyfony\Bundle\FilterableTableBundle\Table\Paginator;

use Vyfony\Bundle\FilterableTableBundle\Table\Paginator\Page\PageInterface;

final class Paginator implements PaginatorInterface
{
    /**
     * @var int
     */
    private $tailLength;

    /**
     * @var int
     */
    private $currentPageIndex;

    /**
     * @var array|PageInterface[]
     */
    private $pages;

    /**
     * @param array|PageInterface[] $pages
     */
    public function __construct(
        int $tailLength,
        int $currentPageIndex,
        array $pages
    ) {
        $this->tailLength = $tailLength;
        $this->currentPageIndex = $currentPageIndex;
        $this->pages = $pages;
    }

    public function getCurrentPageIndex(): int
    {
        return $this->currentPageIndex;
    }

    /**
     * @return array|PageInterface[]
     */
    public function getPages(): array
    {
        return $this->pages;
    }

    /**
     * @return array|PageInterface[]
     */
    public function getVisiblePages(): array
    {
        $pagesCount = \count($this->pages);

        return array_filter($this->pages, function (PageInterface $page) use ($pagesCount) {
            $pageIndex = $page->getIndex();

            $isOneOfFirstPages = $pageIndex <= $this->tailLength;
            $isOneOfLastPages = $pageIndex > $pagesCount - $this->tailLength;
            $isNearCurrentPage =
                $pageIndex < $this->currentPageIndex + $this->tailLength &&
                $pageIndex > $this->currentPageIndex - $this->tailLength;

            return $isOneOfFirstPages || $isOneOfLastPages || $isNearCurrentPage;
        });
    }

    public function isPageCurrent(PageInterface $page): bool
    {
        return $page->getIndex() === $this->currentPageIndex;
    }
}
