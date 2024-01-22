<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HTTPFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $this->user = $this->userRepository->findOneByEmail('user0@todolist.com');
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
        $this->client->loginUser($this->user);
    }

    public function testHomepage()
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('homepage'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('home'));
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}
