<?php

namespace Tests\Controller;

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
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(1, $crawler->filter('html:contains("Bienvenue sur Todo List")'));

        $newUserButton = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($newUserButton);
        $this->assertCount(1, $crawler->filter('html:contains("Tapez le mot de passe à nouveau")'));
        $crawler = $this->client->request('GET', '/');

        $newTaskButton = $crawler->selectLink('Créer une nouvelle tâche')->link();
        $crawler = $this->client->click($newTaskButton);
        $this->assertCount(1, $crawler->filter('html:contains("Retour à la liste des tâches")'));
        $crawler = $this->client->request('GET', '/');

        $taskListButton = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($taskListButton);
        $this->assertCount(1, $crawler->filter('html:contains("Task10")'));
        $crawler = $this->client->request('GET', '/');

        $taskListButton = $crawler->selectLink('Consulter la liste des tâches terminées')->link();
        $crawler = $this->client->click($taskListButton);
        $this->assertCount(1, $crawler->filter('html:contains("Liste des tâches terminées")'));
        $crawler = $this->client->request('GET', '/');

        // $logoutButton = $crawler->selectLink('Se déconnecter')->link();
        // $crawler = $this->client->click($logoutButton);
        // $this->assertCount(1, $crawler->filter('html:contains("Se connecter")'));
    }
}
