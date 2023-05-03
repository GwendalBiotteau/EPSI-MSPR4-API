<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthTest extends WebTestCase
{
    public function testMissingToken(): void
    {
        $client = static::createClient();
        $routes = $client->getContainer()->get('router')->getRouteCollection();
        foreach ($routes as $key => $route) {
            if (!in_array($key, ['api_login_check'])) {
                $path = $route->getPath();
                $method = $route->getMethods();

                if (!in_array('GET', $method)) {
                    continue;
                }

                $client->request('GET', $path);

                $client->request('GET', $path);
                $this->assertResponseStatusCodeSame(401);
            }
        }
    }
}
