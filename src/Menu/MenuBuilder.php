<?php

declare(strict_types=1);

/*
 * This file is part of «Epigraphy of Medieval Rus'» database.
 *
 * Copyright (c) National Research University Higher School of Economics
 *
 * «Epigraphy of Medieval Rus'» database is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, version 3.
 *
 * «Epigraphy of Medieval Rus'» database is distributed
 * in the hope  that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. If you have not received
 * a copy of the GNU General Public License along with
 * «Epigraphy of Medieval Rus'» database,
 * see <http://www.gnu.org/licenses/>.
 */

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class MenuBuilder
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param FactoryInterface $factory
     * @param RequestStack     $requestStack
     */
    public function __construct(FactoryInterface $factory, RequestStack $requestStack)
    {
        $this->factory = $factory;
        $this->requestStack = $requestStack;
    }

    /**
     * @param array $options
     *
     * @return ItemInterface
     */
    public function createMainMenu(array $options): ItemInterface
    {
        $currentRoute = $this->requestStack->getCurrentRequest()->get('_route');

        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'nav flex-column eomr-nav')
        ;

        $menu
            ->addChild('page.menu.index', ['route' => 'index'])
        ;

        $menu
            ->addChild('page.menu.dataBase', ['route' => 'inscription__list'])
            ->setCurrent(\in_array($currentRoute, ['inscription__list', 'inscription__show'], true))
        ;

        foreach ($menu->getChildren() as $child) {
            $child
                ->setAttribute('class', 'nav-item eomr-nav-item')
                ->setLinkAttribute('class', 'nav-link eomr-nav-link')
            ;
        }

        return $menu;
    }
}
