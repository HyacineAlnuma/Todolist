<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TaskTest extends KernelTestCase
{
    private ContainerInterface $container;
    private \DateTimeImmutable $datetime;
    private User $user;

    public function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
        $this->dateTime = new \DateTimeImmutable();
        $this->user = new User();
    }

    public function getTask(): Task
    {
        return (new Task())
            ->setCreatedAt($this->dateTime)
            ->setTitle('Tâche #1')
            ->setContent('Contenu #1')
            ->setUser($this->user)
        ;
    }

    public function testIsTaskValid(): void
    {
        $task = $this->getTask();
        
        $errors = $this->container->get('validator')->validate($task);
        $this->assertCount(0, $errors);
        $this->assertEmpty($task->getId());
        $this->assertTrue($task->getTitle() === 'Tâche #1');
        $this->assertTrue($task->getContent() === 'Contenu #1');
        $this->assertTrue($task->getCreatedAt() === $this->dateTime);
        $this->assertTrue($task->getUser() === $this->user);
        $this->assertTrue($task->isDone() === false);

        $task->toggle(!$task->isDone());

        $this->assertTrue($task->isDone() === true);
    }

    public function testIsTaskInvalid(): void
    {
        $task = $this->getTask();

        $this->assertFalse($task->getTitle() === 'false');
        $this->assertFalse($task->getContent() === 'false');
        $this->assertFalse($task->getCreatedAt() === new \DateTimeImmutable());
        $this->assertFalse($task->getUser() === new User());
    }

    public function testBlankFields(): void
    {
        $task = $this->getTask();
        $task
            ->setTitle('')
            ->setContent('')
            ;
        
        $errors = $this->container->get('validator')->validate($task);
        $this->assertCount(2, $errors);
    }
}
