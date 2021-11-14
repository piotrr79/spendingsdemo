<?php
declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use App\DataFixtures\User as UserFixtures;
use App\Entity\Credit as CreditEntity;

class Credit extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $credit = new CreditEntity();
        $credit->setUserId($this->getReference(UserFixtures::USER_REFERENCE));
        $credit->setCredit('300');
        $manager->persist($credit);
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 5;
    }
}
