<?php

namespace App\Controller;

use App\Repository\ContactRepository;
use App\Service\ContactService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ContactController extends AbstractController
{

    public function __construct(
        private Security $security,
        private ContactService $service,
        private SerializerInterface $serializer
    )
    {
        $this->security = $security;
    }

    #[Route('/api/phonebook', name: 'app_phonebook')]
    public function index(ContactRepository $phonebook): JsonResponse
    {
        $user = $this->security->getUser();
        $own = $user->getContacts();
        $shared = $user->getShares();
        $allContacts = array_merge($own->toArray(), $shared->toArray());
        $data = $this->serializer->serialize($allContacts, 'json', ['groups' => 'phonebook_read']);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/api/add-contact', name: 'add_contact', methods: ['POST','PUT', 'DELETE'])]
    public function addContact(Request $request): JsonResponse
    {
        $user = $this->security->getUser();
        $newContact = json_decode($request->getContent(), true);
        $method = $request->getMethod();

        try {
            switch ($method) {
                case 'POST':
                    $contact = $this->service->saveContact($newContact, $user);
                    break;
                case 'PUT':
                    $contact = $this->service->updateContact($newContact, $user);
                    break;
                case 'DELETE':
                    $contact = $this->service->deleteContact($newContact, $user);
                    break;
            }
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
        $data = $this->serializer->serialize($contact, 'json', ['groups' => 'phonebook_read']);

        return new JsonResponse($data, 200, [], true);
    }
}
