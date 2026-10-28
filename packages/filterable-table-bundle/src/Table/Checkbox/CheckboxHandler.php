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

namespace Vyfony\Bundle\FilterableTableBundle\Table\Checkbox;

final class CheckboxHandler implements CheckboxHandlerInterface
{
    /**
     * @var string
     */
    private $routeName;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $emptySelectionErrorText;

    /**
     * @var string[]
     */
    private $classes;

    /**
     * @param string[] $classes
     */
    public function __construct(string $routeName, string $label, string $emptySelectionErrorText, array $classes)
    {
        $this->routeName = $routeName;
        $this->label = $label;
        $this->emptySelectionErrorText = $emptySelectionErrorText;
        $this->classes = $classes;
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getEmptySelectionErrorText(): string
    {
        return $this->emptySelectionErrorText;
    }

    /**
     * @return string[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }
}
