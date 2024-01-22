<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HTTPFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $this->user = $this->userRepository->findOneByEmail('admin@todolist.com');
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
        $this->client->loginUser($this->user);
    }

    public function testUserListPageIfAdmin(): void
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testUserListPageIfNotAdmin(): void
    {
        $this->user = $this->userRepository->findOneByEmail('user0@todolist.com');
        $this->client->loginUser($this->user);

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testCreateUserPage(): void
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_create'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testEditUserPageIfAdmin(): void
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_edit', ['id' => $this->user->getId()]));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testEditUserPageIfNotAdmin(): void
    {
        $this->user = $this->userRepository->findOneByEmail('user0@todolist.com');
        $this->client->loginUser($this->user);

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_edit', ['id' => $this->user->getId()]));
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}