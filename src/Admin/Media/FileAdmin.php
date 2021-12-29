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

namespace App\Admin\Media;

use App\Admin\AbstractEntityAdmin;
use App\DataStorage\DataStorageManagerInterface;
use App\Persistence\Entity\Media\File;
use App\Persistence\Repository\Media\FileRepository;
use App\Services\Media\Thumbnails\ThumbnailsGeneratorInterface;
use RuntimeException;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Context\ExecutionContext;

final class FileAdmin extends AbstractEntityAdmin
{
    protected $baseRouteName = 'media_file';

    protected $baseRoutePattern = 'media/file';

    private FileRepository $fileRepository;

    private DataStorageManagerInterface $dataStorageManager;

    private ThumbnailsGeneratorInterface $thumbnailsGenerator;

    private EventDispatcherInterface $dispatcher;

    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        FileRepository $fileRepository,
        DataStorageManagerInterface $dataStorageManager,
        ThumbnailsGeneratorInterface $thumbnailsGenerator,
        EventDispatcherInterface $dispatcher
    ) {
        parent::__construct($code, $class, $baseControllerName);

        $this->fileRepository = $fileRepository;
        $this->dataStorageManager = $dataStorageManager;
        $this->thumbnailsGenerator = $thumbnailsGenerator;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param File $object
     */
    public function prePersist(object $object): void
    {
        $object->setBinaryContent(null);

        $uploadedFile = $this->getForm()['binaryContent']->getData();

        if (!$uploadedFile instanceof UploadedFile) {
            throw new RuntimeException('Uploaded file is invalid.');
        }

        $this->dataStorageManager->upload(
            $object,
            $uploadedFile->getClientOriginalName(),
            $uploadedFile->getRealPath(),
            $uploadedFile->getMimeType()
        );
    }

    protected function configureBatchActions(array $actions): array
    {
        unset($actions['delete']);

        return $actions;
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->add('regenerateThumbnails', $this->getRouterIdParameter().'/regenerate-thumbnails');
    }

    protected function configureActionButtons(array $buttonList, string $action, ?object $object = null): array
    {
        if ('edit' === $action) {
            $buttonList['regenerateThumbnails'] = ['template' => 'admin/regenerate_thumbnails_button.html.twig'];
        }

        return $buttonList;
    }

    /**
     * @param File $object
     */
    protected function postRemove(object $object): void
    {
        $this->dataStorageManager->delete($object);
    }

    /**
     * @param File $object
     */
    protected function postPersist(object $object): void
    {
        $this->dispatcher->addListener(KernelEvents::TERMINATE, function () use ($object): void {
            $this->thumbnailsGenerator->generateAll($object);
        });
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, $this->createListOptions('id'))
            ->add('fileName', null, $this->createListOptions('fileName'))
            ->add('mediaType', null, $this->createListOptions('mediaType'))
            ->add('description', null, $this->createListOptions('description'))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        if ($this->isCurrentRoute('edit')) {
            $this->buildEditForm($formMapper);
        } elseif ($this->isCurrentRoute('create')) {
            $this->buildCreateForm($formMapper);
        }
    }

    private function buildEditForm(FormMapper $formMapper): void
    {
        /**
         * @var $file File
         */
        $file = $this->getSubject();

        $contentOptions = [
            'required' => false,
            'disabled' => true,
        ];

        if ($this->isImage($file)) {
            $contentOptions['help'] = sprintf(
                '<img src="%s" class="eomr-image-preview"/>',
                $this->thumbnailsGenerator->getThumbnail($file)
            );
            $contentOptions['help_html'] = true;
        }

        $formMapper->add('binaryContent', FileType::class, $this->createFormOptions('binaryContent', $contentOptions));
        $formMapper->add('fileName', null, $this->createFormOptions('fileName', ['disabled' => true]));
        $formMapper->add('mediaType', null, $this->createFormOptions('mediaType', ['disabled' => true]));
        $formMapper->add('url', null, $this->createFormOptions('url', ['disabled' => true]));
        $formMapper->add('description', null, $this->createFormOptions('description'));
    }

    private function buildCreateForm(FormMapper $formMapper): void
    {
        $formMapper
            ->add(
                'binaryContent',
                FileType::class,
                $this->createFormOptions(
                    'binaryContent',
                    [
                        'required' => true,
                        'constraints' => [
                            new NotBlank(),
                            new NotNull(),
                            new Callback(
                                function (UploadedFile $uploadedFile, ExecutionContext $context): void {
                                    $fileName = $uploadedFile->getClientOriginalName();

                                    if (!$this->dataStorageManager->isFileNameValid($fileName)) {
                                        $context
                                            ->buildViolation(sprintf('Uploaded file name "%s" is invalid.', $fileName))
                                            ->addViolation();
                                    }

                                    $pathToUploadedFile = $uploadedFile->getRealPath();

                                    if (is_dir($pathToUploadedFile)) {
                                        $context
                                            ->buildViolation(sprintf('Uploaded file "%s" has wrong format.', $fileName))
                                            ->addViolation();
                                    }

                                    $hash = md5(file_get_contents($pathToUploadedFile));

                                    $result = $this
                                        ->fileRepository
                                        ->createQueryBuilder('file')
                                        ->where('file.fileName = ?1')
                                        ->orWhere('file.hash = ?2')
                                        ->setParameter(1, $fileName)
                                        ->setParameter(2, $hash)
                                        ->getQuery()
                                        ->execute();

                                    /**
                                     * @var $filesWithTheSameName File[]
                                     */
                                    $filesWithTheSameName = array_filter(
                                        $result,
                                        static function (File $file) use ($fileName): bool {
                                            return $file->getFileName() === $fileName;
                                        }
                                    );

                                    /**
                                     * @var $filesWithTheSameContent File[]
                                     */
                                    $filesWithTheSameContent = array_filter(
                                        $result,
                                        static function (File $file) use ($hash): bool {
                                            return $file->getHash() === $hash;
                                        }
                                    );

                                    if (\count($filesWithTheSameName) > 0) {
                                        $context
                                            ->buildViolation(sprintf('File with name "%s" already exists.', $fileName))
                                            ->addViolation();
                                    }

                                    if (\count($filesWithTheSameContent) > 0) {
                                        $existingFileName = $filesWithTheSameContent[0]->getFileName();

                                        $context
                                            ->buildViolation(
                                                sprintf(
                                                    'File with the same content already exists ("%s").',
                                                    $existingFileName
                                                )
                                            )
                                            ->addViolation();
                                    }
                                }
                            ),
                        ],
                    ]
                )
            )
            ->add('description', null, $this->createFormOptions('description'));
    }

    private function isImage(File $file): bool
    {
        return \in_array(
            $file->getMediaType(),
            ['image/gif', 'image/jpeg', 'image/png', 'image/bmp', 'image/x-ms-bmp', 'image/tiff'],
            true
        );
    }
}
