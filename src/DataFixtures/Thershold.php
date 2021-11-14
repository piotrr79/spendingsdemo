<?php
declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use App\DataFixtures\User as UserFixtures;
use App\Entity\Thershold as ThersholdEntity;

class Thershold extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $thershold = new ThersholdEntity();
        $thershold->setUserId($this->getReference(UserFixtures::USER_REFERENCE));
        $thershold->setThershold('200');
        $manager->persist($thershold);
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 2;
    }
}
