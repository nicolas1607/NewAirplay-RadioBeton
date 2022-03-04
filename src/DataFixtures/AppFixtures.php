<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    const FAKE_USERS = [
        ['JSalmon', ['ROLE_SUPERADMIN'], 'superadmin_connexion'],
        ['NMormiche', ['ROLE_ADMIN'], 'admin_connexion'],
        ['ELamy', ['ROLE_ADMIN'], 'admin_connexion'],
        ['LNicolas', ['ROLE_BENEVOLE'], 'benevole_connexion']
    ];
    
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        foreach(self::FAKE_USERS as $fakeUser)
        {
            $user = new User;

            $password = $this->hasher->hashPassword($user, $fakeUser[2]);

            $user->setUsername($fakeUser[0])
                 ->setRoles($fakeUser[1])
                 ->setPassword($password);

            $manager->persist($user);
            $manager->flush();
        }
    }
}
