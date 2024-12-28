<?php

namespace App\Service;

use App\Entity\Contact;
use Symfony\Component\Security\Core\User\UserInterface;

class ContactShareService extends ContactService
{
    public function shareContact(array $sharedData, UserInterface $owner): Contact
    {
        $contact = $this->findContact($sharedData, $owner);

        $user = $this->userRepository->findBy(['email' => $sharedData['email']], [], 1);

        if (!empty($user)) {
            $user = $user[0];
            $contact->addSharedWith($user);
            $this->contactRepository->save($contact, true);
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
            $this->contactRepository->save($contact, true);
        } else {
            throw new \Exception('User not found');
        }

        return $contact;
    }
}
