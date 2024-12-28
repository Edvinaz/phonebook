<?php

namespace App\Service;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ContactService
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected ContactRepository $contactRepository,
        protected UserRepository $userRepository,
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

    protected function findContact(array $contactData, UserInterface $user): Contact
    {
        $contact = $this->contactRepository->findBy(['id' => $contactData['id'], 'owner' => $user->getId()], [], 1);

        if (!empty($contact)) {
            $contact = $contact[0];
        } else {
            throw new \Exception('Contact not found');
        }

        return $contact;
    }
}
