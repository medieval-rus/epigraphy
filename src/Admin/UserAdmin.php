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

namespace App\Admin;

use App\Admin\Abstraction\AbstractEntityAdmin;
use App\Persistence\Entity\Security\User;
use RuntimeException;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserAdmin extends AbstractEntityAdmin
{
    /**
     * @var string
     */
    protected $baseRouteName = 'security_user';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'security/user';

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        UserPasswordEncoderInterface $passwordEncoder,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        parent::__construct($code, $class, $baseControllerName);

        $this->passwordEncoder = $passwordEncoder;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param User $object
     */
    public function prePersist($object): void
    {
        $this->encodePassword($object);
    }

    /**
     * @param User $object
     */
    public function preUpdate($object): void
    {
        $this->encodePassword($object);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, $this->createLabeledListOptions('id'))
            ->add('username', null, $this->createLabeledListOptions('username'))
            ->add('fullName', null, $this->createLabeledListOptions('fullName'))
            ->add('roles', null, $this->createLabeledListOptions('roles'))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $isEditForm = $this->isEditForm();

        if ($isEditForm) {
            $formMapper
                ->add('id', null, $this->createLabeledFormOptions('id', ['required' => true, 'disabled' => true]))
            ;
        }

        $formMapper
            ->add('username', null, $this->createLabeledFormOptions('username', ['required' => true]))
            ->add('fullName', null, $this->createLabeledFormOptions('fullName', ['required' => true]))
            ->add(
                'roles',
                ChoiceType::class,
                $this->createLabeledFormOptions(
                    'roles',
                    [
                        'required' => true,
                        'multiple' => true,
                        'choices' => [
                            'ROLE_USER' => 'ROLE_USER',
                            'ROLE_ADMIN' => 'ROLE_ADMIN',
                            'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN',
                        ],
                    ]
                )
            )
            ->add(
                'plainPassword',
                TextType::class,
                $this->createLabeledFormOptions('plainPassword', ['required' => !$isEditForm])
            )
        ;
    }

    private function encodePassword(User $user): void
    {
        if (null !== $user->getPlainPassword()) {
            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $user->getPlainPassword())
            );
        }
    }

    private function isEditForm(): bool
    {
        $file = $this->hasSubject() ? $this->getSubject() : $this->getNewInstance();

        if (!$file instanceof User) {
            throw new RuntimeException('%s error: subject is not instance of %s', __CLASS__, User::class);
        }

        return null !== $file->getId();
    }
}
