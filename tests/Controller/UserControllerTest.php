<?php

namespace App\Test\Controller;

use App\Entity\User;
use App\Entity\userRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private userRepository $repository;
    private string $path = '/user/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(User::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('User index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'user[nomuser]' => 'Testing',
            'user[prenomuser]' => 'Testing',
            'user[numtel]' => 'Testing',
            'user[email]' => 'Testing',
            'user[pwd]' => 'Testing',
            'user[typeuser]' => 'Testing',
        ]);

        self::assertResponseRedirects('/user/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setNomuser('My Title');
        $fixture->setPrenomuser('My Title');
        $fixture->setNumtel('My Title');
        $fixture->setEmail('My Title');
        $fixture->setPwd('My Title');
        $fixture->setTypeuser('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('User');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setNomuser('My Title');
        $fixture->setPrenomuser('My Title');
        $fixture->setNumtel('My Title');
        $fixture->setEmail('My Title');
        $fixture->setPwd('My Title');
        $fixture->setTypeuser('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'user[nomuser]' => 'Something New',
            'user[prenomuser]' => 'Something New',
            'user[numtel]' => 'Something New',
            'user[email]' => 'Something New',
            'user[pwd]' => 'Something New',
            'user[typeuser]' => 'Something New',
        ]);

        self::assertResponseRedirects('/user/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getNomuser());
        self::assertSame('Something New', $fixture[0]->getPrenomuser());
        self::assertSame('Something New', $fixture[0]->getNumtel());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getPwd());
        self::assertSame('Something New', $fixture[0]->getTypeuser());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new User();
        $fixture->setNomuser('My Title');
        $fixture->setPrenomuser('My Title');
        $fixture->setNumtel('My Title');
        $fixture->setEmail('My Title');
        $fixture->setPwd('My Title');
        $fixture->setTypeuser('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/user/');
    }
}
