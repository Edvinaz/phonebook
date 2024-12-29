<?php

namespace App\Tests;

use App\Entity\Contact;
use App\Entity\User;
use App\Service\ContactShareService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContactShareServiceIntegrationTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private ContactShareService $contactShareService;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->contactShareService = self::getContainer()->get(ContactShareService::class);

        // Apply migrations or load schema if using an in-memory database
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata); // Clear schema
        $schemaTool->createSchema($metadata); // Create schema
    }

    public function testShareContactSuccessfully(): void
    {
        // Set up test data
        $owner = new User();
        $owner->setEmail('owner@example.com');
        $owner->setPassword('password');
        $this->entityManager->persist($owner);

        $contact = new Contact();
        $contact->setOwner($owner);
        $contact->setName('Test Contact');
        $contact->setPhone('1234567890');
        $this->entityManager->persist($contact);

        $sharedUser = new User();
        $sharedUser->setEmail('shared@example.com');
        $sharedUser->setPassword('password');
        $this->entityManager->persist($sharedUser);

        $this->entityManager->flush();

        $sharedData = [
            'id' => $contact->getId(),
            'email' => 'shared@example.com',
        ];

        $this->contactShareService->shareContact($sharedData, $owner);

        $updatedContact = $this->entityManager->getRepository(Contact::class)->find($contact->getId());
        $sharedWith = $updatedContact->getSharedWith();

        $this->assertCount(1, $sharedWith);
        $this->assertEquals('shared@example.com', $sharedWith[0]->getEmail());
    }

    public function testShareContactUnsuccessfully(): void
    {
        $owner = new User();
        $owner->setEmail('owner@example.com');
        $owner->setPassword('password');
        $this->entityManager->persist($owner);

        $contact = new Contact();
        $contact->setOwner($owner);
        $contact->setName('Test Contact');
        $contact->setPhone('1234567890');
        $this->entityManager->persist($contact);

        $this->entityManager->flush();

        $sharedData = [
            'id' => $contact->getId(),
            'email' => 'nonexistent@example.com',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User with given email not found');

        $this->contactShareService->shareContact($sharedData, $owner);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}
