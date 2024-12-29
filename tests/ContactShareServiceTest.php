<?php

namespace App\Tests;

use App\Entity\Contact;
use App\Entity\User;
use App\Repository\ContactRepository;
use App\Repository\UserRepository;
use App\Service\ContactService;
use App\Service\ContactShareService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class ContactShareServiceTest extends TestCase
{
    private $userRepository;
    private $contactRepository;
    private $contactService;
    public function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->contactRepository = $this->createMock(ContactRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->contactService = new ContactShareService(
            $this->entityManager,
            $this->contactRepository,
            $this->userRepository,
        );
    }

    public function testShareContactSuccessfully(): void
    {
        $sharedData = [
            'id' => 1,
            'email' => 'shared@example.com'
        ];
        $owner = $this->createMock(UserInterface::class);
        $contact = $this->createMock(Contact::class);
        // Found user in database
        $user = (new User())->setEmail('shared@example.com');

        $this->userRepository
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with($sharedData['email'])
            ->willReturn($user);

        $contact
            ->expects($this->once())
            ->method('addSharedWith')
            ->with($user);

        $this->contactRepository
            ->expects($this->once())
            ->method('save')
            ->with($contact, true);

        $this->contactRepository
            ->expects($this->once())
            ->method('findContactByIdAndOwner')
            ->with($sharedData, $owner)
            ->willReturn($contact);

        $result = $this->contactService->shareContact($sharedData, $owner);

        $this->assertSame($contact, $result);
    }

    public function testShareContactUnsuccessfully(): void
    {
        $sharedData = [
            'id' => 1,
            'email' => 'shared@example.com'
        ];
        $owner = $this->createMock(UserInterface::class);
        $contact = $this->createMock(Contact::class);
        // No existing user in database
        $user = null;

        $this->userRepository
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with($sharedData['email'])
            ->willReturn($user);

        $this->contactRepository
            ->expects($this->once())
            ->method('findContactByIdAndOwner')
            ->with($sharedData, $owner)
            ->willReturn($contact);

        $this->expectException(\Exception::class);

        $this->contactService->shareContact($sharedData, $owner);
    }
}
