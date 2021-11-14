<?php
declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use App\DataFixtures\User as UserFixtures;
use App\Entity\Debit as DebitEntity;

class Debit extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $debit = new DebitEntity();
        $debit->setUserId($this->getReference(UserFixtures::USER_REFERENCE));
        $debit->setDebit('100');
        $manager->persist($debit);
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 4;
    }
}
