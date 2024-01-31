<?php

namespace Tests\Controller;

use App\Entity\User;
use App\DataFixtures\AppFixtures;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HTTPFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private AbstractDatabaseTool $databaseTool;
    private ReferenceRepository $fixtures;

    public function setUp(): void
    {
        $this->client = static::createClient();

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->fixtures = $this->databaseTool->loadFixtures([AppFixtures::class])->getReferenceRepository();
        $user = $this->fixtures->getReference('admin-test');
        $this->client->loginUser($user);
    }

    public function testUserListPageIfAdmin(): void
    {
        $crawler = $this->client->request('GET', '/users/list');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(1, $crawler->filter('html:contains("Liste des utilisateurs")'));
        $this->assertCount(12, $crawler->filter('tr'));
    }

    public function testUserListPageIfNotAdmin(): void
    {
        $user = $this->fixtures->getReference('user-test');
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/users/list');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testCreateUser(): void
    {
        $crawler = $this->client->request('GET', '/users/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(1, $crawler->filter('html:contains("Créer un utilisateur")'));

        $this->assertCount(1, $crawler->filter('html:contains("Ajouter")'));
        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'Username test';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'test@exemple.com';
        $form['user[roles]'] = 'ROLE_USER';
        $this->client->submit($form);
        $this->client->followRedirect();   
        $this->assertSelectorTextContains("div.alert.alert-success","Superbe ! L'utilisateur a bien été ajouté.");
    }

    public function testCreateInvalidUser(): void
    {
        $crawler = $this->client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'admin';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'test@exemple.com';
        $form['user[roles]'] = 'ROLE_USER';
        $crawler = $this->client->submit($form); 
        $this->assertCount(1, $crawler->filter('html:contains("Il existe déjà un compte avec ce nom d\'utilisateur")'));
    }

    public function testEditUserIfAdmin(): void
    {
        $userId = $this->fixtures->getReference('user-test')->getId();
        $crawler = $this->client->request('GET', '/users/edit/' . $userId);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(1, $crawler->filter('html:contains("Modifier")'));

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'Username test';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'test@exemple.com';
        $form['user[roles]'] = 'ROLE_USER';
        $this->client->submit($form);
        $this->client->followRedirect();   
        $this->assertSelectorTextContains('div.alert.alert-success','Superbe ! L\'utilisateur a bien été modifié');
    }

    public function testEditInvalidUser(): void
    {
        $userId = $this->fixtures->getReference('user-test')->getId();
        $crawler = $this->client->request('GET', '/users/edit/' . $userId);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'admin';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'test@exemple.com';
        $form['user[roles]'] = 'ROLE_USER';
        $crawler = $this->client->submit($form); 
        $this->assertCount(1, $crawler->filter('html:contains("Il existe déjà un compte avec ce nom d\'utilisateur")'));
    }

    public function testEditUserIfNotAdmin(): void
    {
        $user = $this->fixtures->getReference('user-test');
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/users/edit/48');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }
}