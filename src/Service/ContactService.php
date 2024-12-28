<?php

namespace App\Service;

use App\Entity\Contact;
use App\Entity\User;
use App\Repository\ContactRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ContactService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ContactRepository $contactRepository,
        private UserRepository $userRepository,
    ) {

    }

    public function saveContact(array $contactData, UserInterface $user): Contact
    {
        $newContact = new Contact();
        $newContact->setName($contactData['name']);
        $newContact->setPhone($contactData['phone']);
        $newContact->setOwner($user);

        $this->contactRepository->save($newContact, true);

        return $newContact;
    }

    public function updateContact(array $contactData, UserInterface $user): Contact
    {
        $contact = $this->findContact($contactData, $user);

        $contact->setName($contactData['name']);
        $contact->setPhone($contactData['phone']);

        $this->contactRepository->save($contact, true);

        return $contact;
    }

    public function deleteContact(array $contactData, UserInterface $user): Contact
    {
        $contact = $this->findContact($contactData, $user);
        $this->entityManager->remove($contact);
        $this->entityManager->flush();

        return $contact;
    }

    private function findContact(array $contactData, UserInterface $user): Contact
    {
        $contact = $this->contactRepository->findBy(['id' => $contactData['id'], 'owner' => $user->getId()], [], 1);

        if (!empty($contact)) {
            $contact = $contact[0];
        } else {
            throw new \Exception('Contact not found');
        }

        return $contact;
    }

    public function shareContact(array $sharedData, UserInterface $owner): Contact
    {
        $contact = $this->findContact($sharedData, $owner);

        $user = $this->userRepository->findBy(['email' => $sharedData['email']], [], 1);

        if (!empty($user)) {
            $user = $user[0];
            $contact->addSharedWith($user);
            $this->entityManager->persist($contact);
            $this->entityManager->flush();
        } else {
            throw new \Exception('User not found');
        }

        return $contact;
    }

    public function unshareContact(array $sharedData, UserInterface $owner): Contact
    {
        $contact = $this->findContact($sharedData, $owner);

        $user = $this->userRepository->findBy(['email' => $sharedData['email']], [], 1);

        if (!empty($user)) {
            $user = $user[0];
            $contact->removeSharedWith($user);
            $this->entityManager->persist($contact);
            $this->entityManager->flush();
        } else {
            throw new \Exception('User not found');
        }

        return $contact;
    }
}
