<?php
declare(strict_types=1);

namespace App\Tests\BaseTest;

// Data Fixtures
use Doctrine\Bundle\FixturesBundle\Loader\SymfonyFixturesLoader;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;

/**
 * BaseSchemaTrait - Trait with test schema create, drop and fixtures load
 * @package  Spendings
 * @author   Piotr Rybinski
 */
trait BaseSchemaTrait
{
    /**
     * Create database schema
     */
    public function createSchema()
    {
        /** @var EntityManager $manager */
        $manager = $this->getEntityManager();
        $metadatas = $manager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($manager);
        $schemaTool->updateSchema($metadatas);
    }

    /**
     * Drop database schema
     */
    public function dropSchema()
    {
        /** @var EntityManager $manager */
        $manager = $this->getEntityManager();
        $metadatas = $manager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($manager);
        $schemaTool->dropSchema($metadatas);
    }

    /**
     * Load fixtures
     */
    public function loadFixtures()
    {
        /** @var SymfonyFixturesLoader $fixturesLoader */
        $fixturesLoader = $this->getFixturesLoader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->getEntityManager(), $purger);
        $executor->execute($fixturesLoader->getFixtures());
    }

    /**
     * Truncate DB
     */
    public function truncateEntities()
    {
        $purger = new ORMPurger($this->getEntityManager());
        $purger->purge();
    }
}
