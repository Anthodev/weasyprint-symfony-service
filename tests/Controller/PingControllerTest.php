<?php

declare(strict_types=1);

namespace Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PingControllerTest extends WebTestCase
{
    public function testPingRoute(): void
    {
        $client = static::createClient();
        $client->request('GET', '/ping');
        self::assertResponseIsSuccessful();
        self::assertJsonStringEqualsJsonString('"pong"', $client->getResponse()->getContent());
    }
}
