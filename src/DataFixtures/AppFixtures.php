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
        $this->loadAnonymousTasks($manager);
        $this->loadUserAdmin($manager);
        $this->loadUsersAndTasks($manager);

        $manager->flush();
    }

    private function loadUserAdmin(ObjectManager $manager): void 
    {
        //admin
        $userAdmin = new User();
        $userAdmin
            ->setUsername("admin")
            ->setEmail("admin@todolist.com")
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword($this->userPasswordHasher->hashPassword($userAdmin, 'password'))
        ;
        $this->addReference('admin', $userAdmin);
        $manager->persist($userAdmin);
    }

    private function loadAnonymousTasks(ObjectManager $manager): void
    {
        //tasks linked to anonymous user
        for ($i = 0; $i < 10; $i++) {
            $task = (new Task())
                ->setTitle("Task" . $i)
                ->setContent("Content" . $i)
                ->setUser(null)
            ;
            $manager->persist($task);
        }
    }

    private function loadUsersAndTasks(ObjectManager $manager): void
    {
        //users
        $usersList = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user
                ->setUsername("user" . $i)
                ->setEmail("user" . $i . "@todolist.com")
                ->setRoles(["ROLE_USER"])
                ->setPassword($this->userPasswordHasher->hashPassword($user, 'password'))
            ;
            if($i === 0){
                $this->addReference('user', $user);;
            }
            $manager->persist($user);

            $usersList[] = $user;
        }

        //tasks linked to user
        for ($i = 10; $i < 40; $i++) {
            $task = (new Task())
                ->setTitle("Task" . $i)
                ->setContent("Content" . $i)
                ->setUser($usersList[array_rand($usersList)])
            ;
            $manager->persist($task);
        }
    }
}