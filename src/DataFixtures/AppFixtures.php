<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Task;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;
    
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadAnonymousUsersAndTasks($manager);
        $this->loadUserAdmin($manager);
        $this->loadUsersAndTasks($manager);

        $manager->flush();
    }

    private function loadUserAdmin(ObjectManager $manager): void 
    {
        //admin
        $userAdmin = new User;
        $userAdmin->setUsername("admin");
        $userAdmin->setEmail("admin@todolist.com");
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, 'password'));
        $manager->persist($userAdmin);
    }

    private function loadAnonymousUsersAndTasks(ObjectManager $manager): void
    {
        //anonymous user
        $userAnonymous = new User;
        $userAnonymous->setUsername("anonymous");
        $userAnonymous->setEmail("anonymous@todolist.com");
        $userAnonymous->setRoles(["ROLE_USER"]);
        $userAnonymous->setPassword($this->userPasswordHasher->hashPassword($userAnonymous, 'password'));
        $manager->persist($userAnonymous);

        //tasks linked to anonymous user
        for ($i = 0; $i < 10; $i++) {
            $task = new Task;
            $task->setTitle("Task" . $i);
            $task->setContent("Content" . $i);
            $task->setUser($userAnonymous);
            $manager->persist($task);
        }
    }

    private function loadUsersAndTasks(ObjectManager $manager): void
    {
        // //users
        $usersList = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User;
            $user->setUsername("user" . $i);
            $user->setEmail("user" . $i . "@todolist.com");
            $user->setRoles(["ROLE_USER"]);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
            $manager->persist($user);

            $usersList[] = $user;
        }

        //tasks linked to user
        for ($i = 10; $i < 40; $i++) {
            $task = new Task;
            $task->setTitle("Task" . $i);
            $task->setContent("Content" . $i);
            $task->setUser($usersList[array_rand($usersList)]);
            $manager->persist($task);
        }
    }
}