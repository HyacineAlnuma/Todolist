<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HTTPFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private Task $task;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $this->user = $this->userRepository->findOneByEmail('user0@todolist.com');
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
        $this->client->loginUser($this->user);
        $taskRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $this->task = $taskRepository->findOneBy(['title' => 'Task0']);
    }

    public function testTaskListPage(): void
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testCreateTaskPage(): void
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_create'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testEditTaskPage(): void
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_edit', ['id' => $this->task->getId()]));
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testToggleTask(): void
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_toggle', ['id' => $this->task->getId()]));
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testDeleteTask(): void
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_delete', ['id' => $this->task->getId()]));
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}
