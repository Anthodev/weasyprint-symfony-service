<?php

declare(strict_types=1);

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class PingControllerTest extends WebTestCase
{
    public function testPingHome(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/');
        self::assertResponseIsSuccessful();
    }

    public function testPingRoute(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/ping');
        self::assertResponseIsSuccessful();
        self::assertJsonStringEqualsJsonString('"pong"', $client->getResponse()->getContent());
    }
}
