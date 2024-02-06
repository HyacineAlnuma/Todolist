<?php

namespace Tests\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HTTPFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testLoginPage(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(1, $crawler->filter('html:contains("Please sign in")'));

        $this->assertCount(1, $crawler->filter('html:contains("Sign in")'));
        $form = $crawler->selectButton('Sign in')->form();
        $form['username'] = 'user1';
        $form['password'] = 'password';
        $this->client->submit($form);

        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect(); 
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    }

    public function testLoginPageWithBadCredentials(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(1, $crawler->filter('html:contains("Please sign in")'));

        $this->assertCount(1, $crawler->filter('html:contains("Sign in")'));
        $form = $crawler->selectButton('Sign in')->form();
        $form['username'] = 'invalid';
        $form['password'] = 'invalid';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect(); 
        $this->assertCount(1, $crawler->filter('html:contains("Invalid credentials.")'));
    }
}