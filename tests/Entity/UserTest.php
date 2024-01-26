<?php

namespace Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserTest extends KernelTestCase
{
    private ContainerInterface $container;
    private Task $task;

    public function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
        $this->task = new Task();
    }

    public function getUser(): User
    {
        return (new User())
            ->setUsername('Username')
            ->setPassword('Password')
            ->setEmail('email@gmail.com')
            ->setRoles(['ROLE_USER'])
            ->addTask($this->task)
        ;
    }

    public function testIsUserValid(): void
    {
        $user = $this->getUser();
        
        $errors = $this->container->get('validator')->validate($user);
        $this->assertCount(0, $errors);
        $this->assertEmpty($user->getId());
        $this->assertTrue($user->getUsername() === 'Username');
        $this->assertTrue($user->getPassword() === 'Password');
        $this->assertTrue($user->getEmail() === 'email@gmail.com');
        $this->assertTrue($user->getRoles() === ['ROLE_USER']);
        $this->assertTrue($user->getTasks()->contains($this->task));

        $user->removeTask($this->task);

        $this->assertEmpty($user->getTasks());
    }

    public function testIsUserInvalid(): void
    {
        $user = $this->getUser();

        $this->assertFalse($user->getUsername() === 'false');
        $this->assertFalse($user->getPassword() === 'false');
        $this->assertFalse($user->getEmail() === 'false@gmail.com');
        $this->assertFalse($user->getRoles() === ['ROLE_ADMIN']);
        $this->assertFalse($user->getTasks()->contains(new Task()));
    }

    public function testBlankFields(): void
    {
        $user = $this->getUser();
        $user
            ->setUsername('')
            ->setPassword('')
            ;
        
        $errors = $this->container->get('validator')->validate($user);
        $this->assertCount(2, $errors);
    }

    public function testInvalidEmail(): void
    {
        $user = $this->getUser();
        $user->setEmail('WrongEmail');
        
        $errors = $this->container->get('validator')->validate($user);
        $this->assertCount(1, $errors);
    }
}
