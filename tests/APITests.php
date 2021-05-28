<?php

namespace Tests;

use PhormDev\LSVT\API;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Mockery;

class APITests extends TestCase
{

    /**
     * @var Client
     */
    private Client $client;
    /**
     */
    private $curlHistory = [];
    /**
     * @var MockHandler
     */
    private MockHandler $httpMock;
    /**
     * @var API|mixed
     */
    private $apiClient;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        config([
            'config.lsvt.username' => 'whatever',
            'config.lsvt.password' => 'whatever',
            'config.lsvt.url' => 'https://webservices.lightspeedvt.net/REST/v1/',
        ]);

        $this->createHttpHandler();
        $this->apiClient = app()->make(API::class);
    }

    private function createHttpHandler()
    {
        // Create a mock and queue response
        $this->httpMock = new MockHandler();

        $handlerStack = HandlerStack::create($this->httpMock);
        $handlerStack->push(Middleware::history($this->curlHistory));

        $this->client = new Client(['handler' => $handlerStack]);

        app()->instance(Client::class, $this->client);
    }

    /**
     * Test api can get a user by their LSVT username.
     *
     * @return void
     */
    public function test_lsvt_api_can_get_user_by_username()
    {
        $email = 'test@gmail.com';

        $this->httpMock->append(new Response(200, ['Content-Type' => 'application/json'], json_encode($this->getUserEmailResponse($email))));

        $test = $this->apiClient->getUserByUsername($email);

        $this->assertCount(1, $this->curlHistory);
        $this->assertEquals('https://webservices.lightspeedvt.net/REST/v1//users?username=test@gmail.com', (string)$this->curlHistory[0]['request']->getUri());

        $this->assertIsArray($test);
        $this->assertEquals($email, $test['email']);
    }

    /**
     * Test api can handles an error getting an LSVT username.
     *
     * @return void
     */
    public function test_lsvt_api_returns_null_when_user_is_not_found()
    {
        $email = 'test@gmail.com';

        $this->httpMock->append(new Response(404, ['Content-Type' => 'text/html'], 'Not found.'));

        $test = $this->apiClient->getUserByUsername($email);

        $this->assertCount(1, $this->curlHistory);
        $this->assertEquals('https://webservices.lightspeedvt.net/REST/v1//users?username=test@gmail.com', (string)$this->curlHistory[0]['request']->getUri());

        $this->assertIsArray($test);
        $this->assertEquals($email, $test['email']);
    }

    /**
     * Test that the lsvt api can add points to a lsvt user.
     *
     * @return void
     */
    public function test_lsvt_api_can_add_points_to_an_lsvt_user()
    {
        $userId = 123456;
        $points = 20;

        $mock = Mockery::mock(API::class);

        $mock->shouldReceive('addPoints')
            ->with($userId, $points)
            ->andReturn($this->addPointsResponse($userId, $points));

        $test = $mock->addPoints($userId, $points);

        $this->assertIsArray($test);
        $this->assertEquals($userId, $test['userId']);
        $this->assertEquals('add', $test['action']);
        $this->assertEquals($points, $test['points']);

        $client = $this->createHttpHandler(json_encode($this->addPointsResponse($userId, $points)));
        $clientInstance = app()->instance(Client::class, $client);

        $response = $clientInstance->request('GET', "https://webservices.lightspeedvt.net/REST/v1/users/$userId/leaderboard/points/$points");

        $body = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('add', $body['action']);
        $this->assertEquals($points, $body['points']);
        $this->assertEquals($userId, $body['userId']);
    }

    /**
     * Test that the lsvt api can deduct points from a lsvt user.
     *
     * @return void
     */
    public function test_lsvt_api_can_deduct_points_from_a_lsvt_user()
    {
        $userId = 123456;
        $points = 20;

        $mock = Mockery::mock(API::class);

        $mock->shouldReceive('deductPoints')
            ->with($userId, $points)
            ->andReturn($this->deductPointsResponse($userId, $points));

        $test = $mock->deductPoints($userId, $points);

        $this->assertIsArray($test);
        $this->assertEquals($userId, $test['userId']);

        $client = $this->createHttpHandler(json_encode($this->deductPointsResponse($userId, $points)));
        $clientInstance = app()->instance(Client::class, $client);

        $response = $clientInstance->request('DELETE', "https://webservices.lightspeedvt.net/REST/v1/users/$userId/leaderboard/points/$points");

        $body = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($userId, $body['userId']);
        $this->assertEquals($points, $body['points']);
        $this->assertEquals('deduct', $body['action']);
    }

    /**
     * Test that the lsvt api can bulk add points to multiple users.
     *
     * @return void
     */
    public function lsvt_api_can_bulk_add_points_to_multiple_users_in_one_call()
    {
        //
    }

    /**
     * Test that the lsvt api can bulk deduct points from multiple users.
     *
     * @return void
     */
    public function lsvt_api_can_bulk_deduct_points_from_multiple_users_in_one_call()
    {
        //
    }

    protected function addPointsResponse($userId, $points)
    {
        return [
            "total" => 595,
            "userId" => $userId,
            "points" => $points,
            "action" => "add"
        ];
    }

    protected function deductPointsResponse($userId, $points)
    {
        return [
            "total" => 595,
            "userId" => $userId,
            "points" => $points,
            "action" => "deduct"
        ];
    }

    protected function getUserEmailResponse($email)
    {
        return [
              "DOB" => null,
              "aboutMe" => "",
              "accessLevel" => "",
              "accessLevelName" => "Legionnaire",
              "address1" => "",
              "address2" => "",
              "affiliateId" => "",
              "city" => "",
              "companyName" => "",
              "contentRole" => [],
              "country" => "",
              "email" => $email,
              "expireDate" => null,
              "facebook" => "",
              "firstName" => "Test",
              "gender" => "",
              "handle" => "",
              "hireDate" => null,
              "hometown" => "",
              "instagram" => "",
              "isActive" => true,
              "jobPosition" => "",
              "jobPositionId" => null,
              "language" => "en-us",
              "lastAccessDate" => "2020-10-09T05:28:48",
              "lastName" => "",
              "linkedin" => "",
              "locationId" => "",
              "locationName" => "Legionnaire",
              "lockUsername" => true,
              "lockUsernamePassword" => false,
              "manageUsers" => false,
              "middleName" => "",
              "misc1" => "",
              "misc2" => "",
              "phone1" => "",
              "phone2" => "",
              "promoCode" => "",
              "releaseDate" => null,
              "startDate" => "2020-09-26T19:09:43",
              "state" => "",
              "systemId" => "",
              "team" => "",
              "teamId" => "",
              "tiktok" => "",
              "title" => "",
              "twitter" => "",
              "updateMyProfile" => true,
              "userId" => "",
              "username" => "",
              "vendorId" => "",
              "youtube" => "",
              "zip" => ""
            ];
    }
}