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
use FOS\CKEditorBundle\Form\Type\CKEditorType;

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
                ->add('comment', CKEditorType::class, $this->createFormOptions('comment', ['autoload' => false, 'required' => false]))
            ->end()
            ->with($this->getSectionLabel('materialAspect'), ['class' => 'col-md-6'])
                ->add('placeOnCarrier', CKEditorType::class, $this->createZeroRowPartOptions('placeOnCarrier', ['autoload' => false]))
                // ->add('writingTypes', null, $this->createZeroRowPartOptions('writingTypes'))
                ->add('writingMethods', null, $this->createZeroRowPartOptions('writingMethods'))
                ->add('preservationStates', null, $this->createZeroRowPartOptions('preservationStates'))
                // ->add('materials', null, $this->createZeroRowPartOptions('materials'))
            ->end()
            ->with($this->getSectionLabel('linguisticAspect'), ['class' => 'col-md-6'])
                ->add('alphabets', null, $this->createZeroRowPartOptions('alphabets'))
                ->add('text', CKEditorType::class, $this->createZeroRowPartOptions('text', ['autoload' => false, 'config_name' => 'textconfig']))
                ->add('interpretationComment', CKEditorType::class, $this->createZeroRowPartOptions('interpretationComment', ['autoload' => false, 'required' => false]))
                ->add('transliteration', CKEditorType::class, $this->createZeroRowPartOptions('transliteration', ['autoload' => false, 'config_name' => 'textconfig']))
                ->add('reconstruction', CKEditorType::class, $this->createZeroRowPartOptions('reconstruction', ['autoload' => false, 'config_name' => 'textconfig']))
                ->add('normalization', CKEditorType::class, $this->createZeroRowPartOptions('normalization', ['autoload' => false, 'config_name' => 'textconfig']))
                ->add('translation', CKEditorType::class, $this->createZeroRowPartOptions('translation', ['autoload' => false]))
                ->add('contentCategories', null, $this->createZeroRowPartOptions('contentCategories'))
                ->add('description', CKEditorType::class, $this->createZeroRowPartOptions('description', ['autoload' => false, 'config_name' => 'textconfig']))
            ->end()
            ->with($this->getSectionLabel('historicalAspect'), ['class' => 'col-md-6'])
                ->add('dateInText', CKEditorType::class, $this->createZeroRowPartOptions('dateInText', ['autoload' => false]))
                ->add('nonStratigraphicalDate', CKEditorType::class, $this->createZeroRowPartOptions('nonStratigraphicalDate', ['autoload' => false]))
                ->add('historicalDate', CKEditorType::class, $this->createZeroRowPartOptions('historicalDate', ['autoload' => false]))
                ->add('origin', CKEditorType::class, $this->createZeroRowPartOptions('origin', ['autoload' => false]))
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
