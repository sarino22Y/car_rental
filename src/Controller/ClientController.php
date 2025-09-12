<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ClientController extends AbstractController
{
    #[Route('/client/dashboard', name: 'app_client_dashboard')]
    #[IsGranted('ROLE_CLIENT')]
    public function index(ClientRepository $clientRepository): Response
    {
        $client = $this->getUser();
        return $this->render('client/index.html.twig', [
            'client' => $client,
            'subscriptions' => $client->getSubscriptions(),
        ]);
    }
}
