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

namespace Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\Table\RadioOption;

use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\Column\ColumnMetadataInterface;

final class RadioOption implements RadioOptionInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $label;

    /**
     * @var ColumnMetadataInterface
     */
    private $columnMetadata;

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return RadioOption
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return RadioOption
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getColumnMetadata(): ColumnMetadataInterface
    {
        return $this->columnMetadata;
    }

    /**
     * @return RadioOption
     */
    public function setColumnMetadata(ColumnMetadataInterface $columnMetadata): self
    {
        $this->columnMetadata = $columnMetadata;

        return $this;
    }
}
