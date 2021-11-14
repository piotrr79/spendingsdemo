<?php
declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use App\DataFixtures\User as UserFixtures;
use App\Entity\Balance as BalanceEntity;

class Balance extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $balance = new BalanceEntity();
        $balance->setUserId($this->getReference(UserFixtures::USER_REFERENCE));
        $balance->setTotalDebit('100');
        $balance->setTotalCredit('300');
        $balance->setBalance('200');
        $manager->persist($balance);
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 3;
    }
}
