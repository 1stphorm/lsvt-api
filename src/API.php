<?php

namespace PhormDev\LSVT;

use GuzzleHttp\Client;

class API
{
    /** @var Client */
    private $client;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $url;

    public function __construct(Client $client, string $username, string $password, string $url)
    {
        $this->client = $client;
        $this->username = $username;
        $this->password = $password;
        $this->url = $url;
    }

    /**
     * @param string $username
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserByUsername(string $username)
    {
        $response = $this->client->get(
            $this->url.'/users?username='.$username,
            [
                'auth'    => $this->setBasicAuth(),
                'headers' => $this->setHeader(),
            ]
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * This endpoint adds points to a user’s point tally in
     * their leaderboard
     *
     * @inheritDoc
     * @url https://example.com/REST/V1/users/{userId}/leaderboard/points/{points}
     */
    public function addPoints(int $user_id, int $points)
    {
        $response = $this->client->put(
            $this->url.'/users/'.$user_id.'/leaderboard/points/'.$points,
            [
                'auth'    => $this->setBasicAuth(),
                'headers' => $this->setHeader(),
            ]
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * This endpoint deducts points from a user’s point tally in
     * their leaderboard
     *
     * @inheritDoc
     * @url https://example.com/REST/V1/users/{userId}/leaderboard/points/{points}
     */
    public function deductPoints(int $user_id, int $points)
    {
        $response = $this->client->delete(
            $this->url.'/users/'.$user_id.'/leaderboard/points/'.$points,
            [
                'auth'    => $this->setBasicAuth(),
                'headers' => $this->setHeader(),
            ]
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * This endpoint allows you to add or deduct points for
     * multiple users in one call
     *
     * @inheritDoc
     * @url https://example.com/REST/V1/users/leaderboard/points
     */
    public function bulkPoints(array $data = [])
    {
        $response = $this->client->put(
            $this->url.'/users/leaderboard/points',
            [
                'auth'        => $this->setBasicAuth(),
                'headers'     => $this->setHeader(),
                'form_params' => $data,
            ]
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * Generates an array with the credentials for the basic auth.
     *
     * @return array
     */
    protected function setBasicAuth(): array
    {
        return [
            $this->username,
            $this->password,
            'Basic',
        ];
    }

    /**
     * Set's the header for the request.
     *
     * @return string[]
     */
    protected function setHeader(): array
    {
        return [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }
}
