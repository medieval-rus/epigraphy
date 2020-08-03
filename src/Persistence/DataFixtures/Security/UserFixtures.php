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

namespace App\Persistence\DataFixtures\Security;

use App\Persistence\Entity\Security\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class UserFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public static function getGroups(): array
    {
        return ['security'];
    }

    public function load(ObjectManager $manager): void
    {
        $this->createAndPersistUser($manager, 'admin', 'admin', ['ROLE_USER', 'ROLE_ADMIN']);

        $this->createAndPersistUser($manager, 'user', 'user', ['ROLE_USER']);

        $manager->flush();
    }

    private function createAndPersistUser(
        ObjectManager $manager,
        string $username,
        string $password,
        array $roles
    ): void {
        $user = new User();

        $user->setUsername($username);
        $user->setFullName(sprintf('User %s', $username));
        $user->setRoles($roles);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

        $manager->persist($user);
    }
}
