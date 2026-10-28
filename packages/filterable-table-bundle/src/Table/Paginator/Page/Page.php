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

namespace Vyfony\Bundle\FilterableTableBundle\Table\Paginator\Page;

final class Page implements PageInterface
{
    /**
     * @var int
     */
    private $index;

    /**
     * @var string
     */
    private $url;

    public function __construct(int $index, string $url)
    {
        $this->index = $index;
        $this->url = $url;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
