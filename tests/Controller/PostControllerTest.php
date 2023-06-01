<?php

namespace App\Tests\Controller;

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

    /*----------------------------------------CLUB DATA & RAND-------------------------------------*/

    private function createClubData(): array
    {
        return [
            "name" => chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)),
            "budget" => rand(50, 60) * 500,
            "email" => "club" . rand(1, 999) . "@email.com",
            "phone" => "125656" . rand(100, 999)];
    }

    private function CreateRandClub(): int
    {
        $client = self::$client;
        $client->request('GET', '//league.localhost/club');
        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $ids = [];
        foreach ($data["CLUBS"] as $club) {
            $ids[] = $club["id"];
        }
        return $ids[array_rand($ids)];
    }

    /*----------------------------------------PLAYER DATA & RAND-------------------------------------*/

    private function createPlayerData(): array
    {
        return [
            "name" => chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)),
            "surname" => chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)),
            "email" => "player" . rand(1, 999) . "@email.com",
            "dni" => "12345" . rand(100, 990) . chr(rand(65, 90)),
            "salary" => rand(30, 35) * 50,
            "phone" => "125656" . rand(100, 999)];
    }


    private function createRandPlayer(): int
    {
        $client = self::$client;
        $client->request('GET', '//league.localhost/player');
        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $ids = [];
        foreach ($data["PLAYERS"] as $player) {
            $ids[] = $player["id"];
        }
        return $ids[array_rand($ids)];

    }

    /*----------------------------------------TRAINER DATA & RAND-------------------------------------*/

    private function createTrainerData(): array
    {
        return [
            "name" => chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)),
            "surname" => chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)),
            "email" => "trainer" . rand(1, 999) . "@email.com",
            "dni" => "12345" . rand(100, 999) . chr(rand(65, 90)),
            "salary" => rand(40, 60) * 50,
            "phone" => "125656" . rand(100, 999)];
    }

    private function createRandTrainer(): int
    {
        $client = self::$client;
        $client->request('GET', '//league.localhost/trainer');
        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $ids = [];
        foreach ($data["TRAINERS"] as $trainer) {
            $ids[] = $trainer["id"];
        }
        return $ids[array_rand($ids)];

    }

    /*-----------------------------CLUB---------------------------------------*/
    public function testClubIndex(): void
    {
        $client = self::$client;
        $client->request('GET', '//league.localhost/club');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // Get the response content
        $responseContent = $client->getResponse()->getContent();
        // Display the response content
        echo $responseContent . PHP_EOL;
    }

    public function testResponseIsJsonClub(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/club');
        $this->assertResponseFormatSame("json");
    }

    public function testResponseHasClub(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/club');
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        json_decode($response->getContent(), true);
    }

    public function testResponseExceptionClub(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/club');
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
    }

    /*----------------------------------------CREATE CLUB-------------------------------------*/
    public function testResponseSuccessfulCreateClub(): void
    {
        $client = static::createClient();
        $client->request('POST', '//league.localhost/club', $this->createClubData());
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;
    }

    public function testResponseIsJsonCreateClub(): void
    {
        $client = static::createClient();
        $client->request('POST', '//league.localhost/club', $this->createClubData());
        $this->assertResponseFormatSame("json");
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;
    }

    public function testResponseHasCreateClub(): void
    {
        $client = static::createClient();
        $client->request('POST', '//league.localhost/club', $this->createClubData());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        json_decode($response->getContent(), true);
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;
    }

    public function testResponseExceptionCreateClub(): void
    {
        $client = static::createClient();
        $client->request('POST', '//league.localhost/club', $this->createClubData());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;
    }

    /*----------------------------------------SHOW CLUB-------------------------------------*/
    public function testResponseSuccessfulShowClub(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/club/' . $this->CreateRandClub());
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandClub() . $responseContent . PHP_EOL;
    }

    public function testResponseIsJsonShowClub(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/club/' . $this->CreateRandClub());
        $this->assertResponseFormatSame("json");
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandClub() . $responseContent . PHP_EOL;
    }

    public function testResponseHasShowClub(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/club/' . $this->CreateRandClub());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        json_decode($response->getContent(), true);
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandClub() . $responseContent . PHP_EOL;
    }

    public function testResponseExceptionShowClub(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/club/' . $this->CreateRandClub());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandClub() . $responseContent . PHP_EOL;
    }

    /*----------------------------------------UPDATE CLUB-------------------------------------*/
    public function testResponseSuccessfulUpdateClub(): void
    {
        $client = static::createClient();
        $client->request('PUT', '//league.localhost/club/' . $this->CreateRandClub(), $this->createClubData());
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandClub() . $responseContent . PHP_EOL;
    }

    public function testResponseIsJsonUpdateClub(): void
    {
        $client = static::createClient();
        $client->request('PUT', '//league.localhost/club/' . $this->CreateRandClub(), $this->createClubData());
        $this->assertResponseFormatSame("json");
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandClub() . $responseContent . PHP_EOL;
    }

    public function testResponseHasUpdateClub(): void
    {
        $client = static::createClient();
        $client->request('PUT', '//league.localhost/club/' . $this->CreateRandClub(), $this->createClubData());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        json_decode($response->getContent(), true);
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandClub() . $responseContent . PHP_EOL;
    }

    public function testResponseExceptionUpdateClub(): void
    {
        $client = static::createClient();
        $client->request('PUT', '//league.localhost/club/' . $this->CreateRandClub(), $this->createClubData());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandClub() . $responseContent . PHP_EOL;
    }

    /*----------------------------------------DELETE CLUB-------------------------------------*/
    public function testResponseSuccessfulDeleteClub(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '//league.localhost/club/' . $this->createRandClub());
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandPlayer() . $responseContent . PHP_EOL;

    }

    public function testResponseIsJsonDeleteClub(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '//league.localhost/club/' . $this->CreateRandClub());
        $this->assertResponseFormatSame("json");
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandPlayer() . $responseContent . PHP_EOL;
    }

    public function testResponseHasDeleteClub(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '//league.localhost/club/' . $this->CreateRandClub());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        json_decode($response->getContent(), true);
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandPlayer() . $responseContent . PHP_EOL;
    }

    public function testResponseExceptionDeleteClub(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '//league.localhost/club/' . $this->CreateRandClub());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandPlayer() . $responseContent . PHP_EOL;
    }


    /*----------------------------------------CLUB CREATE PLAYER-------------------------------------*/
    public function testResponseSuccessfulClubCreatePlayer(): void
    {
        $client = static::createClient();
        $client->request('POST', '//league.localhost/club/' . $this->CreateRandClub() . '/player', $this->createPlayerData());
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;

    }

    public function testResponseIsJsonClubCreatePlayer(): void
    {
        $client = static::createClient();
        $client->request('POST', '//league.localhost/club/' . $this->CreateRandClub() . '/player', $this->createPlayerData());
        $this->assertResponseFormatSame("json");
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;
    }

    public function testResponseHasClubCreatePlayer(): void
    {
        $client = static::createClient();
        $client->request('POST', '//league.localhost/club/' . $this->CreateRandClub() . '/player', $this->createPlayerData());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        json_decode($response->getContent(), true);
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;
    }

    public function testResponseExceptionClubCreatePlayer(): void
    {
        $client = static::createClient();
        $client->request('POST', '//league.localhost/club/' . $this->CreateRandClub() . '/player', $this->createPlayerData());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;
    }

    /*----------------------------------------CLUB LIST PLAYER-------------------------------------*/
    public function testResponseSuccessfulClubListPlayer(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/club/' . $this->CreateRandClub() . '/player');
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandPlayer() . $responseContent . PHP_EOL;
    }

    public function testResponseIsJsonClubListPlayer(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/club/' . $this->CreateRandClub() . '/player');
        $this->assertResponseFormatSame("json");
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandPlayer() . $responseContent . PHP_EOL;
    }

    public function testResponseHasClubListPlayer(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/club/' . $this->CreateRandClub() . '/player');
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        json_decode($response->getContent(), true);
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandPlayer() . $responseContent . PHP_EOL;
    }

    public function testResponseExceptionClubListPlayer(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/club/' . $this->CreateRandClub() . '/player');
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandPlayer() . $responseContent . PHP_EOL;
    }

    /*----------------------------------------CLUB DELETE PLAYER-------------------------------------*/
//    public function testResponseSuccessfulClubDeletePlayer(): void
//    {
//        $client = static::createClient();
//        $client->request('DELETE', '//league.localhost/club/' . $this->CreateRandClub() . '/player/' . $this->createRandPlayer());
//        $this->assertResponseIsSuccessful();
//        $responseContent = $client->getResponse()->getContent();
//        echo $this->createRandPlayer . $responseContent . PHP_EOL;
//    }
//
//    public function testResponseIsJsonClubDeletePlayer(): void
//    {
//        $client = static::createClient();
//        $client->request('DELETE', '//league.localhost/club/' . $this->CreateRandClub() . '/player/', $this->createPlayerData());
//        $this->assertResponseFormatSame("json");
//        $responseContent = $client->getResponse()->getContent();
//        echo $this->createRandPlayer . $responseContent . PHP_EOL;
//    }
//
//    public function testResponseHasClubDeletePlayer(): void
//    {
//        $client = static::createClient();
//        $client->request('DELETE', '//league.localhost/club/' . $this->CreateRandClub() . '/player/', $this->createPlayerData());
//        $response = $client->getResponse();
//        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
//        $this->assertJson($response->getContent());
//        json_decode($response->getContent(), true);
//        $responseContent = $client->getResponse()->getContent();
//        echo $this->createRandPlayer . $responseContent . PHP_EOL;
//    }
//
//    public function testResponseExceptionClubDeletePlayer(): void
//    {
//        $client = static::createClient();
//        $client->request('DELETE', '//league.localhost/club/' . $this->CreateRandClub() . '/player/', $this->createPlayerData());
//        $response = $client->getResponse();
//        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
//        $this->assertJson($response->getContent());
//        $client->catchExceptions(false);
//        $responseContent = $client->getResponse()->getContent();
//        echo $this->createRandPlayer . $responseContent . PHP_EOL;
//    }

    /*----------------------------------------CLUB CREATE TRAINER-------------------------------------*/
    public function testResponseSuccessfulClubCreateTrainer(): void
    {
        $client = static::createClient();
        $client->request('POST', '//league.localhost/club/' . $this->CreateRandClub() . '/trainer', $this->createTrainerData());
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;


    }

    public function testResponseIsJsonClubCreateTrainer(): void
    {
        $client = static::createClient();
        $client->request('POST', '//league.localhost/club/' . $this->CreateRandClub() . '/trainer', $this->createTrainerData());
        $this->assertResponseFormatSame("json");
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;
    }

    public function testResponseHasClubCreateTrainer(): void
    {
        $client = static::createClient();
        $client->request('POST', '//league.localhost/club/' . $this->CreateRandClub() . '/trainer', $this->createTrainerData());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        json_decode($response->getContent(), true);
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;
    }

    public function testResponseExceptionClubCreateTrainer(): void
    {
        $client = static::createClient();
        $client->request('POST', '//league.localhost/club/' . $this->CreateRandClub() . '/trainer', $this->createTrainerData());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;
    }

    /*----------------------------------------CLUB DELETE TRAINER-------------------------------------*/
//    public function testResponseSuccessfulClubDeleteTrainer(): void
//    {
//        $client = static::createClient();
//        $client->request('DELETE', '//league.localhost/club/' . $this->CreateRandClub() . '/trainer/' . $this->createRandTrainer());
//        $this->assertResponseIsSuccessful();
//        $responseContent = $client->getResponse()->getContent();
//        echo $this->createRandTrainer() . $responseContent . PHP_EOL;
//    }
//
//    public function testResponseIsJsonClubDeleteTrainer(): void
//    {
//        $client = static::createClient();
//        $client->request('DELETE', '//league.localhost/club/' . $this->CreateRandClub() . '/trainer/', $this->createPlayerData());
//        $this->assertResponseFormatSame("json");
//        $responseContent = $client->getResponse()->getContent();
//        echo $this->createRandTrainer() . $responseContent . PHP_EOL;
//    }
//
//    public function testResponseHasClubDeleteTrainer(): void
//    {
//        $client = static::createClient();
//        $client->request('DELETE', '//league.localhost/club/' . $this->CreateRandClub() . '/trainer/', $this->createPlayerData());
//        $response = $client->getResponse();
//        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
//        $this->assertJson($response->getContent());
//        json_decode($response->getContent(), true);
//        $responseContent = $client->getResponse()->getContent();
//        echo $this->createRandTrainer() . $responseContent . PHP_EOL;
//    }
//
//    public function testResponseExceptionClubDeleteTrainer(): void
//    {
//        $client = static::createClient();
//        $client->request('DELETE', '//league.localhost/club/' . $this->CreateRandClub() . '/trainer/', $this->createPlayerData());
//        $response = $client->getResponse();
//        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
//        $this->assertJson($response->getContent());
//        $client->catchExceptions(false);
//        $responseContent = $client->getResponse()->getContent();
//        echo $this->createRandTrainer() . $responseContent . PHP_EOL;
//    }

    /*-----------------------------PLAYER---------------------------------------*/

    public function testPlayerIndex(): void
    {
        $client = self::$client;
        $client->request('GET', '//league.localhost/player');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;
    }

    public function testResponseIsJsonPlayer(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/player');
        $this->assertResponseFormatSame("json");

    }

    public function testResponseHasPlayer(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/player');
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        json_decode($response->getContent(), true);

    }

    public function testResponseExceptionPlayer(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/player');
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
    }

    /*----------------------------------------CREATE PLAYER-------------------------------------*/
    public function testResponseSuccessfulCreatePlayer(): void
    {
        $client = static::createClient();
        $client->request('POST', '//league.localhost/player', $this->createPlayerData());
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;
    }

    public function testResponseIsJsonCreatePlayer(): void
    {
        $client = static::createClient();
        $client->request('POST', '//league.localhost/player', $this->createPlayerData());
        $this->assertResponseFormatSame("json");
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;
    }

    public function testResponseHasCreatePlayer(): void
    {
        $client = static::createClient();
        $client->request('POST', '//league.localhost/player', $this->createPlayerData());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        json_decode($response->getContent(), true);
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;
    }

    public function testResponseExceptionCreatePlayer(): void
    {
        $client = static::createClient();
        $client->request('POST', '//league.localhost/player', $this->createPlayerData());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;
    }

    /*----------------------------------------SHOW PLAYER-------------------------------------*/
    public function testResponseSuccessfulShowPlayer(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/player/' . $this->createRandPlayer());
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandPlayer() . $responseContent . PHP_EOL;

    }

    public function testResponseIsJsonShowPlayer(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/player/' . $this->createRandPlayer());
        $this->assertResponseFormatSame("json");
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandPlayer() . $responseContent . PHP_EOL;
    }

    public function testResponseHasShowPlayer(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/player/' . $this->createRandPlayer());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandPlayer() . $responseContent . PHP_EOL;
    }

    public function testResponseExceptionShowPlayer(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/player/' . $this->createRandPlayer());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandPlayer() . $responseContent . PHP_EOL;
    }

    /*----------------------------------------UPDATE PLAYER-------------------------------------*/
    public function testResponseSuccessfulUpdatePlayer(): void
    {
        $client = static::createClient();
        $client->request('PUT', '//league.localhost/player/' . $this->createRandPlayer(), $this->createPlayerData());
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandPlayer() . $responseContent . PHP_EOL;

    }

    public function testResponseIsJsonUpdatePlayer(): void
    {
        $client = static::createClient();
        $client->request('PUT', '//league.localhost/player/' . $this->createRandPlayer(), $this->createPlayerData());
        $this->assertResponseFormatSame("json");
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandPlayer() . $responseContent . PHP_EOL;
    }

    public function testResponseHasUpdatePlayer(): void
    {
        $client = static::createClient();
        $client->request('PUT', '//league.localhost/player/' . $this->createRandPlayer(), $this->createPlayerData());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandPlayer() . $responseContent . PHP_EOL;
    }

    public function testResponseExceptionUpdatePlayer(): void
    {
        $client = static::createClient();
        $client->request('PUT', '//league.localhost/player/' . $this->createRandPlayer(), $this->createPlayerData());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandPlayer() . $responseContent . PHP_EOL;
    }

    /*----------------------------------------DELETE PLAYER-------------------------------------*/
    public function testResponseSuccessfulDeletePlayer(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '//league.localhost/player/' . $this->createRandPlayer());
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandPlayer() . $responseContent . PHP_EOL;

    }

    public function testResponseIsJsonDeletePlayer(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '//league.localhost/player/' . $this->createRandPlayer());
        $this->assertResponseFormatSame("json");
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandPlayer() . $responseContent . PHP_EOL;
    }

    public function testResponseHasDeletePlayer(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '//league.localhost/player/' . $this->createRandPlayer());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandPlayer() . $responseContent . PHP_EOL;
    }

    public function testResponseExceptionDeletePlayer(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '//league.localhost/player/' . $this->createRandPlayer());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandPlayer() . $responseContent . PHP_EOL;
    }

    /*------------------------------TRAINER--------------------------------------*/
    public function testTrainerIndex(): void
    {
        $client = self::$client;
        $client->request('GET', '//league.localhost/trainer');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;
    }

    public function testResponseIsJsonTrainer(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/trainer');
        $this->assertResponseFormatSame("json");

    }

    public function testResponseHasTrainer(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/trainer');
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        json_decode($response->getContent(), true);

    }

    public function testResponseExceptionTrainer(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/trainer');
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);

    }

    /*----------------------------------------CREATE TRAINER-------------------------------------*/
    public function testResponseSuccessfulCreateTrainer(): void
    {
        $client = static::createClient();
        $client->request('POST', '//league.localhost/trainer', $this->createTrainerData());
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;
    }

    public function testResponseIsJsonCreateTrainer(): void
    {
        $client = static::createClient();
        $client->request('POST', '//league.localhost/trainer', $this->createTrainerData());
        $this->assertResponseFormatSame("json");
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;
    }

    public function testResponseHasCreateTrainer(): void
    {
        $client = static::createClient();
        $client->request('POST', '//league.localhost/trainer', $this->createTrainerData());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        json_decode($response->getContent(), true);
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;
    }

    public function testResponseExceptionCreateTrainer(): void
    {
        $client = static::createClient();
        $client->request('POST', '//league.localhost/trainer', $this->createTrainerData());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
        $responseContent = $client->getResponse()->getContent();
        echo $responseContent . PHP_EOL;
    }

    /*----------------------------------------SHOW TRAINER-------------------------------------*/
    public function testResponseSuccessfulShowTrainer(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/trainer/' . $this->CreateRandTrainer());
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandTrainer() . $responseContent . PHP_EOL;
    }

    public function testResponseIsJsonShowTrainer(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/trainer/' . $this->CreateRandTrainer());
        $this->assertResponseFormatSame("json");
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandTrainer() . $responseContent . PHP_EOL;
    }

    public function testResponseHasShowTrainer(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/trainer/' . $this->CreateRandTrainer());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        json_decode($response->getContent(), true);
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandTrainer() . $responseContent . PHP_EOL;
    }

    public function testResponseExceptionShowTrainer(): void
    {
        $client = static::createClient();
        $client->request('GET', '//league.localhost/trainer/' . $this->CreateRandTrainer());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandTrainer() . $responseContent . PHP_EOL;
    }


    /*----------------------------------------UPDATE TRAINER-------------------------------------*/
    public function testResponseSuccessfulUpdateTrainer(): void
    {
        $client = static::createClient();
        $client->request('PUT', '//league.localhost/trainer/' . $this->createRandTrainer(), $this->createTrainerData());
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandTrainer() . $responseContent . PHP_EOL;

    }

    public function testResponseIsJsonUpdateTrainer(): void
    {
        $client = static::createClient();
        $client->request('PUT', '//league.localhost/trainer/' . $this->createRandTrainer(), $this->createTrainerData());
        $this->assertResponseFormatSame("json");
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandTrainer() . $responseContent . PHP_EOL;
    }

    public function testResponseHasUpdateTrainer(): void
    {
        $client = static::createClient();
        $client->request('PUT', '//league.localhost/trainer/' . $this->createRandTrainer(), $this->createTrainerData());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandTrainer() . $responseContent . PHP_EOL;
    }

    public function testResponseExceptionUpdateTrainer(): void
    {
        $client = static::createClient();
        $client->request('PUT', '//league.localhost/trainer/' . $this->createRandTrainer(), $this->createTrainerData());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandTrainer() . $responseContent . PHP_EOL;
    }

    /*----------------------------------------DELETE TRAINER-------------------------------------*/
    public function testResponseSuccessfulDeleteTrainer(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '//league.localhost/trainer/' . $this->createRandTrainer());
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandTrainer() . $responseContent . PHP_EOL;

    }

    public function testResponseIsJsonDeleteTrainer(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '//league.localhost/trainer/' . $this->createRandTrainer());
        $this->assertResponseFormatSame("json");
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandTrainer() . $responseContent . PHP_EOL;
    }

    public function testResponseHasDeleteTrainer(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '//league.localhost/trainer/' . $this->createRandTrainer());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandTrainer() . $responseContent . PHP_EOL;
    }

    public function testResponseExceptionDeleteTrainer()
    {
        $client = static::createClient();
        $client->request('DELETE', '//league.localhost/trainer/' . $this->createRandTrainer());
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $client->catchExceptions(false);
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        echo $this->createRandTrainer() . $responseContent . PHP_EOL;
    }


}
