<?php

namespace App\Controller;

use App\Service\ContactShareService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ContactShareController extends AbstractController
{
    public function __construct(
        private Security $security,
        private ContactShareService $service,
        private SerializerInterface $serializer
    )
    {
        $this->security = $security;
    }

    #[Route('/api/share-contact', name: 'share_contact', methods: ['POST', 'DELETE'])]
    public function shareContact(Request $request): JsonResponse
    {
        $user = $this->security->getUser();
        $shareDate = json_decode($request->getContent(), true);
        $method = $request->getMethod();

        try {
            switch ($method) {
                case 'POST':
                    $contact = $this->service->shareContact($shareDate, $user);
                    break;
                case 'DELETE':
                    $contact = $this->service->unshareContact($shareDate, $user);
                    break;
            }
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
        $data = $this->serializer->serialize($contact, 'json', ['groups' => 'phonebook_read']);

        return new JsonResponse($data, 200, [], true);
    }
}
