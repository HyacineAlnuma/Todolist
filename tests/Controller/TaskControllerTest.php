<?php

namespace Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\DataFixtures\AppFixtures;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HTTPFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private AbstractDatabaseTool $databaseTool;
    private ReferenceRepository $fixtures;

    public function setUp(): void
    {
        $this->client = static::createClient();

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->fixtures = $this->databaseTool->loadFixtures([AppFixtures::class])->getReferenceRepository();
        $user = $this->fixtures->getReference('user-test', User::class);
        $this->client->loginUser($user);
    }

    public function testTaskListPage(): void
    {
        $crawler = $this->client->request('GET', '/tasks');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(40, $crawler->filter('div.thumbnail'));
    }

    public function testCreateTask(): void
    {
        $crawler = $this->client->request('GET', 'tasks/create');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(1, $crawler->filter('html:contains("Retour à la liste des tâches")'));

        $this->assertCount(1, $crawler->filter('html:contains("Ajouter")'));
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'Title test';
        $form['task[content]'] = 'Content test';
        $this->client->submit($form);
        $this->client->followRedirect();   
        $this->assertSelectorTextContains('div.alert.alert-success','Superbe ! La tâche a été bien été ajoutée.');

        $crawler = $this->client->request('GET', '/tasks');
        $this->assertCount(41, $crawler->filter('div.thumbnail'));
    }

    public function testEditTaskIfUserValid(): void
    {
        $taskId = $this->fixtures->getReference('owned-task', Task::class)->getId();
        $crawler = $this->client->request('GET', '/tasks/edit/' . $taskId);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(1, $crawler->filter('html:contains("Modifier")'));

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'Title test modified';
        $form['task[content]'] = 'Content test';
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert.alert-success','Superbe ! La tâche a bien été modifiée.');

        $crawler = $this->client->request('GET', '/tasks');
        $this->assertCount(1, $crawler->filter('html:contains("Title test modified")'));
    }

    public function testEditTaskIfUserInvalid(): void
    {
        $taskId = $this->fixtures->getReference('unowned-task', Task::class)->getId();
        $crawler = $this->client->request('GET', '/tasks/edit/' . $taskId);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testToggleTask(): void
    {
        $taskId = $this->fixtures->getReference('owned-task', Task::class)->getId();
        $crawler = $this->client->request('GET', '/tasks/toggle/' . $taskId);
        $crawler = $this->client->request('GET', '/tasks/done');

        $this->assertCount(1, $crawler->filter('html:contains("Task10")'));

        $crawler = $this->client->request('GET', '/tasks/toggle/' . $taskId);
        $crawler = $this->client->request('GET', '/tasks/done');

        $this->assertCount(0, $crawler->filter('html:contains("Task0")'));
    }

    public function testTaskIsDoneListPage(): void
    {
        $crawler = $this->client->request('GET', '/tasks/done');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testDeleteTask(): void
    {
        $taskId = $this->fixtures->getReference('owned-task', Task::class)->getId();
        $crawler = $this->client->request('GET', '/tasks/delete/' . $taskId);
        $crawler = $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert.alert-success','Superbe ! La tâche a bien été supprimée.');
        $this->assertCount(0, $crawler->filter('html:contains("Task10")'));
    }

    public function testDeleteTaskInvalidUser(): void
    {
        $taskId = $this->fixtures->getReference('unowned-task', Task::class)->getId();
        $crawler = $this->client->request('GET', '/tasks/delete/' . $taskId);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $crawler = $this->client->request('GET', '/tasks');
        $this->assertCount(1, $crawler->filter('html:contains("Task10")'));
    }

    public function testDeleteAnonymousTaskIfAdmin(): void
    {
        $user = $this->fixtures->getReference('admin-test', User::class);
        $this->client->loginUser($user);

        $taskId = $this->fixtures->getReference('anonymous-task', Task::class)->getId();
        $crawler = $this->client->request('GET', '/tasks/delete/' . $taskId);
        $crawler = $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert.alert-success','Superbe ! La tâche a bien été supprimée.');
        $this->assertCount(0, $crawler->filter('html:contains("Task0")'));
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }
}
