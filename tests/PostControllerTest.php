<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    private static $client;

    public function setUp(): void
    {
        parent::setUp();

        if (null === self::$client) {
            self::$client = static::createClient();
        }
    }


    private function createPlayerData(): array
    {
        return [
            "name" => chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)),
            "surname" => chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)),
            "email" => "test" . rand(10, 50) . "@email.com",
            "dni" => "213045" . rand(10, 99) . "X",
            "salary" => rand(30, 35) * 50,
            "phone" => "125656" . rand(100, 999)];
    }

    private function createRandPlayer(): int
    {
        $client = self::$client;
        $crawler = $client->request('GET', '//league.localhost/player');
        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $ids = [];
        foreach ($data["PLAYERS"] as $player) {
            $ids[] = $player["id"];
        }
        return $ids[array_rand($ids)];

    }

    public function testResponseSuccessfulCreatePlayer(): void
    {
        $client = self::$client;
        $crawler = $client->request('POST', '//league.localhost/player', $this->createPlayerData());
        echo("Player CREATED");
        $this->assertResponseIsSuccessful();
    }


    public function testResponseSuccessfulDeletePlayer(): void
    {
        $client = self::$client;
        $crawler = $client->request('DELETE', '//league.localhost/player/' . $this->createRandPlayer());
        echo("Player: " . $this->createRandPlayer() . " DELETED");
        $this->assertResponseIsSuccessful();

    }


}
