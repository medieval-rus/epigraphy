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

namespace App\Admin;

use App\Admin\Abstraction\AbstractEntityAdmin;
use App\Persistence\Entity\Epigraphy\File;
use App\Services\Zenodo\ZenodoClientInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Throwable;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class FileAdmin extends AbstractEntityAdmin
{
    /**
     * @var string
     */
    protected $baseRouteName = 'epigraphy_file';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'epigraphy/file';

    /**
     * @var ZenodoClientInterface
     */
    private $zenodoClient;

    /**
     * @var string
     */
    private $zenodoRecordId;

    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        ZenodoClientInterface $zenodoClient,
        string $zenodoRecordId
    ) {
        parent::__construct($code, $class, $baseControllerName);

        $this->zenodoClient = $zenodoClient;
        $this->zenodoRecordId = $zenodoRecordId;
    }

    /**
     * @param File $object
     */
    public function prePersist($object): void
    {
        $object->setBinaryContent(null);

        $uploadedFile = $this->getForm()['binaryContent']->getData();

        if ($uploadedFile instanceof UploadedFile) {
            $depositionId = $this->zenodoClient->getLatestDepositionIdVersion($this->zenodoRecordId);

            $newDepositionVersionId = $this->zenodoClient->newVersion($depositionId);

            try {
                [$zenodoFileUrl, $zenodoFileId] = $this->zenodoClient->saveFile(
                    $uploadedFile->getClientOriginalName(),
                    file_get_contents($uploadedFile->getRealPath()),
                    $newDepositionVersionId
                );
            } catch (Throwable $exception) {
                $this->zenodoClient->deleteVersion($newDepositionVersionId);

                throw $exception;
            }

            $this->zenodoClient->publishDeposition($newDepositionVersionId);

            $object->setFileName($uploadedFile->getClientOriginalName());
            $object->setMediaType($uploadedFile->getMimeType());
            $object->setUrl($zenodoFileUrl);
            $object->setMetadata(
                [
                    'zenodo' => [
                        'id' => $zenodoFileId,
                    ],
                ]
            );
        }
    }

    /**
     * @param File $object
     */
    public function postRemove($object): void
    {
        return;
        // todo: this is draft for delete flow
        // because of Zenodo versioning approach, simple "delete" action is impossible, as it may cause
        // a collision: two different versions with the same content (which is impossible in Zenodo)
        $metadata = $object->getMetadata();

        if (null !== $metadata && \array_key_exists('zenodo', $metadata)) {
            $depositionId = $this->zenodoClient->getLatestDepositionIdVersion($this->zenodoRecordId);

            $newDepositionVersionId = $this->zenodoClient->newVersion($depositionId);
            $fileId = $metadata['zenodo']['id'];

            try {
                $this->zenodoClient->removeFile($fileId, $newDepositionVersionId);
            } catch (Throwable $exception) {
                $this->zenodoClient->deleteVersion($newDepositionVersionId);

                throw $exception;
            }

            $this->zenodoClient->publishDeposition($newDepositionVersionId);
        }
    }

    /**
     * @param File $object
     */
    public function toString($object): string
    {
        return $object->getFileName();
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, $this->createLabeledListOptions('id'))
            ->add('fileName', null, $this->createLabeledListOptions('fileName'))
            ->add('mediaType', null, $this->createLabeledListOptions('mediaType'))
            ->add('description', null, $this->createLabeledListOptions('description'))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $file = $this->hasSubject() ? $this->getSubject() : $this->getNewInstance();

        if (!$file instanceof File) {
            return;
        }

        if ($file->getId()) {
            $this->buildEditForm($formMapper);
        } else {
            $this->buildCreateForm($formMapper);
        }
    }

    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->remove('delete');
    }

    private function buildEditForm(FormMapper $formMapper): void
    {
        $formMapper->add('fileName', null, $this->createLabeledFormOptions('fileName', ['disabled' => true]));
        $formMapper->add('mediaType', null, $this->createLabeledFormOptions('mediaType', ['disabled' => true]));
        $formMapper->add('url', null, $this->createLabeledFormOptions('url', ['disabled' => true]));
        $formMapper->add('description', null, $this->createLabeledFormOptions('description'));
        $formMapper->add(
            'binaryContent',
            FileType::class,
            $this->createLabeledFormOptions(
                'binaryContent',
                [
                    'required' => false,
                    'disabled' => true,
                ]
            )
        );
    }

    private function buildCreateForm(FormMapper $formMapper): void
    {
        $formMapper
            ->add(
                'binaryContent',
                FileType::class,
                $this->createLabeledFormOptions(
                    'binaryContent',
                    [
                        'required' => true,
                        'constraints' => [
                            new NotBlank(),
                            new NotNull(),
                        ],
                    ]
                )
            )
            ->add('description', null, $this->createLabeledFormOptions('description'));
    }
}
