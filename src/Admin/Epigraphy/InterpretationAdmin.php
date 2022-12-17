<?php

declare(strict_types=1);

/*
 * This file is part of «Epigraphy of Medieval Rus» database.
 *
 * Copyright (c) National Research University Higher School of Economics
 *
 * «Epigraphy of Medieval Rus» database is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, version 3.
 *
 * «Epigraphy of Medieval Rus» database is distributed
 * in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. If you have not received
 * a copy of the GNU General Public License along with
 * «Epigraphy of Medieval Rus» database,
 * see <http://www.gnu.org/licenses/>.
 */

namespace App\Admin\Epigraphy;

use App\Admin\AbstractEntityAdmin;
use App\DataStorage\DataStorageManagerInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

final class InterpretationAdmin extends AbstractEntityAdmin
{
    protected $baseRouteName = 'epigraphy_interpretation';

    protected $baseRoutePattern = 'epigraphy/interpretation';

    private DataStorageManagerInterface $dataStorageManager;

    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        DataStorageManagerInterface $dataStorageManager
    ) {
        parent::__construct($code, $class, $baseControllerName);

        $this->dataStorageManager = $dataStorageManager;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with($this->getSectionLabel('identification'), ['class' => 'col-md-6'])
                ->add(
                    'id',
                    HiddenType::class,
                    ['attr' => ['data-interpretation-id' => $this->getSubject()->getId()]]
                )
                ->add('source', null, $this->createFormOptions('source'))
                ->add('pageNumbersInSource', null, $this->createFormOptions('pageNumbersInSource'))
                ->add('numberInSource', null, $this->createFormOptions('numberInSource'))
                ->add('comment', null, $this->createFormOptions('comment'))
            ->end()
            ->with($this->getSectionLabel('materialAspect'), ['class' => 'col-md-6'])
                ->add('placeOnCarrier', null, $this->createZeroRowPartOptions('placeOnCarrier'))
                // ->add('writingTypes', null, $this->createZeroRowPartOptions('writingTypes'))
                ->add('writingMethods', null, $this->createZeroRowPartOptions('writingMethods'))
                ->add('preservationStates', null, $this->createZeroRowPartOptions('preservationStates'))
                // ->add('materials', null, $this->createZeroRowPartOptions('materials'))
            ->end()
            ->with($this->getSectionLabel('linguisticAspect'), ['class' => 'col-md-6'])
                ->add('alphabets', null, $this->createZeroRowPartOptions('alphabets'))
                ->add(
                    'text',
                    null,
                    $this->createZeroRowPartOptions('text', ['attr' => ['data-virtual-keyboard' => true]])
                )
                ->add('transliteration', null, $this->createZeroRowPartOptions('transliteration'))
                ->add('reconstruction', null, $this->createZeroRowPartOptions('reconstruction'))
                ->add('normalization', null, $this->createZeroRowPartOptions('normalization'))
                ->add('translation', null, $this->createZeroRowPartOptions('translation'))
                ->add('contentCategories', null, $this->createZeroRowPartOptions('contentCategories'))
                ->add('description', null, $this->createZeroRowPartOptions('description'))
            ->end()
            ->with($this->getSectionLabel('historicalAspect'), ['class' => 'col-md-6'])
                ->add('origin', null, $this->createZeroRowPartOptions('origin'))
                ->add('dateInText', null, $this->createZeroRowPartOptions('dateInText'))
                ->add('nonStratigraphicalDate', null, $this->createZeroRowPartOptions('nonStratigraphicalDate'))
                ->add('historicalDate', null, $this->createZeroRowPartOptions('historicalDate'))
            ->end()
            ->with($this->getSectionLabel('media'), ['class' => 'col-md-6'])
                ->add(
                    'photos',
                    null,
                    $this->createZeroRowPartOptions(
                        'photos',
                        [
                            'choice_filter' => $this->dataStorageManager->getFolderFilter('photo'),
                            'query_builder' => $this->dataStorageManager->getQueryBuilder(),
                        ]
                    )
                )
                ->add(
                    'drawings',
                    null,
                    $this->createZeroRowPartOptions(
                        'drawings',
                        [
                            'choice_filter' => $this->dataStorageManager->getFolderFilter('drawing'),
                            'query_builder' => $this->dataStorageManager->getQueryBuilder(),
                        ]
                    )
                )
                ->add(
                    'textImages',
                    null,
                    $this->createZeroRowPartOptions(
                        'textImages',
                        [
                            'choice_filter' => $this->dataStorageManager->getFolderFilter('text'),
                            'query_builder' => $this->dataStorageManager->getQueryBuilder(),
                        ]
                    )
                )
            ->end()
        ;
    }

    private function createZeroRowPartOptions(
        string $label,
        array $options = []
    ): array {
        $options['attr'] = array_merge(
            [
                'data-zero-row-part' => $label.'References',
            ],
            \array_key_exists('attr', $options) ? $options['attr'] : []
        );

        return $this->createFormOptions($label, array_merge(['required' => false], $options));
    }
}
