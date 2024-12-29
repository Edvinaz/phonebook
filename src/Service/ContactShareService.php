<?php

namespace App\Service;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ContactShareService extends ContactService
{
    public function __construct(EntityManagerInterface $entityManager, ContactRepository $contactRepository, UserRepository $userRepository)
    {
        parent::__construct($entityManager, $contactRepository, $userRepository);
    }

    public function shareContact(array $sharedData, UserInterface $owner): Contact
    {
        $contact = $this->contactRepository->findContactByIdAndOwner($sharedData, $owner);
        $user = $this->userRepository->findUserByEmail($sharedData['email']);

        if ($user) {
            $contact->addSharedWith($user);
            $this->contactRepository->save($contact, true);
        } else {
            throw new \Exception('User with given email not found');
        }

        return $contact;
    }

    public function unshareContact(array $sharedData, UserInterface $owner): Contact
    {
        $contact = $this->contactRepository->findContactByIdAndOwner($sharedData, $owner);
        $user = $this->userRepository->findUserByEmail($sharedData['email']);

        if ($user) {
            $contact->removeSharedWith($user);
            $this->contactRepository->save($contact, true);
        } else {
            throw new \Exception('User not found');
        }

        return $contact;
    }
}
