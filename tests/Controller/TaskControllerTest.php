<?php

namespace Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\DataFixtures\AppFixtures;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HTTPFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private AbstractDatabaseTool $databaseTool;
    private Task $task;

    public function setUp(): void
    {
        $this->client = static::createClient();
        // $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class);
        // $this->fixtures = $this->databaseTool->loadFixtures([AppFixtures::class])->getReferenceRepository();
        // $user = $this->fixtures->getReference('user');
    
        $this->userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $this->user = $this->userRepository->findOneByEmail('user0@todolist.com');
        $this->client->loginUser($this->user);
        $taskRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $this->task = $taskRepository->findOneBy(['title' => 'Task0']);
    }

    public function testTaskListPage(): void
    {
        $crawler = $this->client->request('GET', '/tasks');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(40, $crawler->filter('div.thumbnail'));

        $newUserButton = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($newUserButton);
        $this->assertCount(1, $crawler->filter('html:contains("Tapez le mot de passe à nouveau")'));
        $crawler = $this->client->request('GET', '/tasks');

        $newTaskButton = $crawler->selectLink('Créer une tâche')->link();
        $crawler = $this->client->click($newTaskButton);
        $this->assertCount(1, $crawler->filter('html:contains("Retour à la liste des tâches")'));
        $crawler = $this->client->request('GET', '/tasks');

        // $logoutButton = $crawler->selectLink('Se déconnecter')->link();
        // $crawler = $this->client->click($logoutButton);
        // $this->assertCount(1, $crawler->filter('html:contains("Se connecter")'));
    }

    public function testCreateTaskPage(): void
    {
        $crawler = $this->client->request('GET', 'tasks/create');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(1, $crawler->filter('html:contains("Retour à la liste des tâches")'));

        $newUserButton = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($newUserButton);
        $this->assertCount(1, $crawler->filter('html:contains("Tapez le mot de passe à nouveau")'));
        $crawler = $this->client->request('GET', 'tasks/create');
    

        // $this->assertCount(1, $crawler->filter('html:contains("Ajouter")'));
        // $form = $crawler->selectButton('Ajouter')->form();
        // $form['task_title'] = 'Title test';
        // $form['task_content'] = 'Content test';
        // $this->client->submit($form);
        // $this->client->followRedirect();   
        // $this->assertSelectorTextContains('div.alert.alert-success','Superbe ! La tâche a été bien été ajoutée.');

        // $logoutButton = $crawler->selectLink('Se déconnecter')->link();
        // $crawler = $this->client->click($logoutButton);
        // $this->assertCount(1, $crawler->filter('html:contains("Se connecter")'));
    }

    public function testEditTaskPageIfUserValid(): void
    {
        $crawler = $this->client->request('GET', '/tasks/51/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(1, $crawler->filter('html:contains("Modifier")'));

        $newUserButton = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($newUserButton);
        $this->assertCount(1, $crawler->filter('html:contains("Tapez le mot de passe à nouveau")'));
        $crawler = $this->client->request('GET', '/tasks/51/edit');

        // $form = $crawler->selectButton('Modifier')->form();
        // $form['task_title'] = 'Title test';
        // $form['task_content'] = 'Content test';
        // $this->client->submit($form);
        // $this->client->followRedirect();
        // $this->assertSelectorTextContains('div.alert.alert-success','Superbe ! La tâche a été bien été modifiée.');

        // $logoutButton = $crawler->selectLink('Se déconnecter')->link();
        // $crawler = $this->client->click($logoutButton);
        // $this->assertCount(1, $crawler->filter('html:contains("Se connecter")'));
    }

    public function testEditTaskPageIfUserInvalid(): void
    {
        $crawler = $this->client->request('GET', '/tasks/52/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testToggleTask(): void
    {
        $crawler = $this->client->request('GET', '/tasks');
        $this->assertCount(1, $crawler->filter('html:contains("Marquer comme faite")')->first());

        $toggleButton = $crawler->selectButton('Marquer comme faite')->form();
        $this->client->submit($toggleButton);
        $this->assertCount(1, $crawler->filter('html:contains("Marquer non terminée")'));
    }

    public function testTaskIsDoneListPage(): void
    {
        $crawler = $this->client->request('GET', '/tasks/done');
        //tester que la tâche que l'on vient de toggle dans la méthode précédente se trouve ici
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        

        $newUserButton = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($newUserButton);
        $this->assertCount(1, $crawler->filter('html:contains("Tapez le mot de passe à nouveau")'));
        $crawler = $this->client->request('GET', '/tasks');

        $newTaskButton = $crawler->selectLink('Créer une tâche')->link();
        $crawler = $this->client->click($newTaskButton);
        $this->assertCount(1, $crawler->filter('html:contains("Retour à la liste des tâches")'));
        $crawler = $this->client->request('GET', '/tasks');

        // $logoutButton = $crawler->selectLink('Se déconnecter')->link();
        // $crawler = $this->client->click($logoutButton);
        // $this->assertCount(1, $crawler->filter('html:contains("Se connecter")'));
    }

    public function testDeleteTask(): void
    {
        $crawler = $this->client->request('GET', '/tasks');
        $this->assertCount(1, $crawler->filter('html:contains("Supprimer")')->first());

        $deleteButton = $crawler->selectButton('Marquer comme faite')->form();
        $this->client->submit($deleteButton);
        $this->assertCount(0, $crawler->filter('html:contains("Task10")'));
    }

    public function tearDown(): void
    {
        unset($this->databaseTool);
    }
}
