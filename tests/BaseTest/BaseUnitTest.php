<?php
declare(strict_types=1);

namespace App\Tests\BaseTest;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\MockObject\MockBuilder;

/**
 * BaseUnitTest - Base Class for loading UT fixtures
 * @package  Spendings
 * @author   Piotr Rybinski
 */
abstract class BaseUnitTest extends KernelTestCase
{
    use BaseSchemaTrait;
    /**
     * Set up test
     */
    protected function setUp(): void
    {
        parent::setUp();
        static::bootKernel();

        static::$kernel->getContainer();
    }

    /**
     * Get EntityManager from container
     * @return object
     */
    public function getEntityManager()
    {
        return static::$container->get('doctrine.orm.default_entity_manager');
    }

    /**
     * Get Registry
     * @return object
     */
    public function getRegistry()
    {
        return static::$container->get('doctrine');
    }

    /**
     * Get FixturesLoader from container
     * @return object
     */
    private function getFixturesLoader()
    {
        return static::$container->get('doctrine.fixtures.loader');
    }

    /**
     * Get Router
     * @return object
     */
    public function getRouter()
    {
        return static::$container->get('router');
    }

    /**
     * Get Service
     * @return object
     */
    public function getServiceByName($serviceName)
    {
        return static::$container->get($serviceName);
    }

    /**
     * Create LoggerInterface mockup
     * @return mixed
     */
    public function createLoggerMockUp()
    {
        /** @var LoggerInterface|MockBuilder $mock */
        $mock = $this->getMockBuilder(LoggerInterface::class);
        return $mock->getMock();
    }

    /**
     * Tear down
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
