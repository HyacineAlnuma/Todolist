<?php

namespace Tests\Controller;

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
        $crawler = $this->client->request('GET', '/users/list');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(1, $crawler->filter('html:contains("Liste des utilisateurs")'));
        $this->assertCount(13, $crawler->filter('tr'));

        // $logoutButton = $crawler->selectLink('Se déconnecter')->link();
        // $crawler = $this->client->click($logoutButton);
        // $this->assertCount(1, $crawler->filter('html:contains("Se connecter")'));
    }

    public function testUserListPageIfNotAdmin(): void
    {
        $this->user = $this->userRepository->findOneByEmail('user0@todolist.com');
        $this->client->loginUser($this->user);

        $crawler = $this->client->request('GET', '/users/list');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testCreateUserPage(): void
    {
        $crawler = $this->client->request('GET', '/users/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(1, $crawler->filter('html:contains("Créer un utilisateur")'));

        // $this->assertCount(1, $crawler->filter('html:contains("Ajouter")'));
        // $form = $crawler->selectButton('Ajouter')->form();
        // $form['user_username'] = 'Username test';
        // $form['user_password_first'] = 'password';
        // $form['user_password_second'] = 'password';
        // $form['user_email'] = 'test@exemple.com';
        // $form['user_roles'] = 'ROLE_USER';
        // $this->client->submit($form);
        // $this->client->followRedirect();   
        // $this->assertSelectorTextContains('div.alert.alert-success','Superbe ! L'utilisateur a bien été ajouté.');

        // $logoutButton = $crawler->selectLink('Se déconnecter')->link();
        // $crawler = $this->client->click($logoutButton);
        // $this->assertCount(1, $crawler->filter('html:contains("Se connecter")'));
    }

    public function testEditUserPageIfAdmin(): void
    {
        $crawler = $this->client->request('GET', '/users/15/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(1, $crawler->filter('html:contains("Modifier un utilisateur")'));

        // $this->assertCount(1, $crawler->filter('html:contains("Modifier")'));
        // $form = $crawler->selectButton('Modifier')->form();
        // $form['user_username'] = 'Username test';
        // $form['user_password_first'] = 'password';
        // $form['user_password_second'] = 'password';
        // $form['user_email'] = 'test@exemple.com';
        // $form['user_roles'] = 'ROLE_USER';
        // $this->client->submit($form);
        // $this->client->followRedirect();   
        // $this->assertSelectorTextContains('div.alert.alert-success','Superbe ! L'utilisateur a bien été modifié.');

        // $logoutButton = $crawler->selectLink('Se déconnecter')->link();
        // $crawler = $this->client->click($logoutButton);
        // $this->assertCount(1, $crawler->filter('html:contains("Se connecter")'));
    }

    public function testEditUserPageIfNotAdmin(): void
    {
        $this->user = $this->userRepository->findOneByEmail('user0@todolist.com');
        $this->client->loginUser($this->user);

        $crawler = $this->client->request('GET', '/users/15/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}