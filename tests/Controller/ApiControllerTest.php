<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\BaseTest\BaseUnitTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Controller\ApiController;
use App\Validators\RequestChecker;

/**
 * ApiControllerTest - run with: `php bin/phpunit`
 * @package  Spendings
 * @author   Piotr Rybinski
 */
class ApiControllerTest extends BaseUnitTest
{
    /** @var  ApiController $api */
    private $api;
    /** @var  Request $request */
    private $request;
    /** @var array $routing */
    private $routing;

    /**
     * Set test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->createSchema();
        $this->loadFixtures();

        $this->request = Request::createFromGlobals();
        $requestChecker = $this->getServiceByName(RequestChecker::class);
        $entityManager = $this->getEntityManager();
        $this->api = new ApiController($requestChecker, $entityManager);
        $this->routing = [];
    }

    /**
     * Create thershold along with user
     * @internal Expected result should like asserts
     */
    public function testSetThershold()
    {
        $request = $this->request;
        $request->request->set('user', '0b1b6ca9-d178-11e4-8e3d-a0b3cce9bb7e');
        $request->request->set('threshold', '400');

        $result = $this->api->threshold($request);

        static::assertEquals('"Threshold saved"', $result->getContent());
    }

    /**
     * Set credit for non existing user
     * @internal Expected result should like asserts, credit has to be created along user
     */
    public function testSetCredit()
    {
        $request = $this->request;
        $request->request->set('user', '00805a63-ce26-11e4-8e3d-a0b3cce9bb7e');
        $request->request->set('refund', '100');

        $result = $this->api->credit($request);

        static::assertEquals('"Credit saved"', $result->getContent());
    }

    /**
     * Set debit for non existing user
     * @internal Expected result should like asserts, debit has to be created along user,
     * but since default thershold is set to 0 for every new created user a message
     * about exceeding thershold should be displayed
     */
    public function testSetDebit()
    {
        $request = $this->request;
        $request->request->set('user', '007f13ff-ce26-11e4-8e3d-a0b3cce9bb7e');
        $request->request->set('ammount', '50');

        $result = $this->api->debit($request);

        static::assertEquals('"User Id: 007f13ff-ce26-11e4-8e3d-a0b3cce9bb7e Thershold: 0 Total spendings: 50"', $result->getContent());
    }

    /**
     * Set debit for existing user, above user thershold 
     * @internal Expected result should like asserts, debit has to be created along user
     */
    public function testSetDebitAboveThershold()
    {
        $request = $this->request;
        $request->request->set('user', '0b1b6ca9-d178-11e4-8e3d-a0b3cce9bb7e');
        $request->request->set('ammount', '500');

        $result = $this->api->debit($request);

        static::assertEquals('"User Id: 0b1b6ca9-d178-11e4-8e3d-a0b3cce9bb7e Thershold: 200 Total spendings: 600"', $result->getContent());
    }

    protected function tearDown(): void
    {
        $this->dropSchema();
        parent::tearDown();
    }

    /**
     * Create thershold with missing param
     * @internal Expected result should be exception HttpException
     */
    public function testSetThersholdMissingParam()
    {
        $this->expectException(HttpException::class);

        $request = $this->request;
        $request->request->set('user', '0b1b6ca9-d178-11e4-8e3d-a0b3cce9bb7e');
        $request->request->set('threshold', '');

        $this->api->threshold($request);
    }

    /**
     * Set credit with missing param
     * @internal Expected result should be exception HttpException
     */
    public function testSetCreditMissingParam()
    {
        $this->expectException(HttpException::class);

        $request = $this->request;
        $request->request->set('user', '');
        $request->request->set('refund', '50');

        $this->api->credit($request);
    }

    /**
     * Set debit with missing param
     * @internal Expected result should be exception HttpException
     */
    public function testSetDebitMissingParam()
    {
        $this->expectException(HttpException::class);

        $request = $this->request;
        $request->request->set('user', '');
        $request->request->set('ammount', '100');

        $this->api->debit($request);
    }

    /**
     * Create thershold with db error
     * @internal Expected result should be exception \Exception
     */
    public function testSetThersholdDbError()
    {
        $this->expectException(\Exception::class);

        $conn = $this->getRegistry()->getManager()->getConnection();
        $sql = 'DROP TABLE THERSHOLD';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $request = $this->request;
        $request->request->set('user', '0b1b6ca9-d178-11e4-8e3d-a0b3cce9bb7e');
        $request->request->set('threshold', '100');

        $this->api->threshold($request);
    }

    /**
     * Set credit with db error
     * @internal Expected result should be exception \Exception
     */
    public function testSetCreditDbError()
    {
        $this->expectException(\Exception::class);

        $conn = $this->getRegistry()->getManager()->getConnection();
        $sql = 'DROP TABLE CREDIT';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $request = $this->request;
        $request->request->set('user', '00805a63-ce26-11e4-8e3d-a0b3cce9bb7e');
        $request->request->set('refund', '50');

        $this->api->credit($request);
    }

    /**
     * Set debit with db error
     * @internal Expected result should be exception \Exception
     */
    public function testSetDebitDbError()
    {
        $this->expectException(\Exception::class);

        $conn = $this->getRegistry()->getManager()->getConnection();
        $sql = 'DROP TABLE DEBIT';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $request = $this->request;
        $request->request->set('user', '007f13ff-ce26-11e4-8e3d-a0b3cce9bb7e');
        $request->request->set('ammount', '100');

        $this->api->debit($request);
    }

    /**
     * Test application routes
     * @internal Expected result should be like $expected array
     */
    public function testRoutes()
    {
        /** @var Router $router */
        $router = $this->getRouter();
        $collection = $router->getRouteCollection();

        foreach ($collection->all() as $routeName => $route) {
            $path = $route->getPath();
            $method = $route->getMethods()[0];
            $this->routing[$routeName] = ['path' => $path, 'method' => $method];
        }

        $expected = [
            "threshold" => [
            "path" => "/threshold",
            "method" => "POST"
            ],
            "debit" => [
              "path" => "/debit",
              "method" => "POST"
            ],
            "credit" => [
              "path" => "/credit",
              "method" => "POST"
            ]
            ];

        static::assertEquals($expected, $this->routing);
    }

}
