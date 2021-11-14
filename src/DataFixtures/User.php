<?php
declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use App\Entity\User as UserEntity;

class User extends Fixture implements OrderedFixtureInterface
{
    public const USER_REFERENCE = 'default-user';

    public function load(ObjectManager $manager): void
    {
        $user = new UserEntity();
        $user->setUserId('0b1b6ca9-d178-11e4-8e3d-a0b3cce9bb7e');
        $manager->persist($user);
        $manager->flush();
        $this->addReference(self::USER_REFERENCE, $user);    
    }

    public function getOrder()
    {
        return 1;
    }
}

