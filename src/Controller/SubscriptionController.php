<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Form\SubscriptionType;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class SubscriptionController extends AbstractController
{
   #[Route('/client/subscriptions', name: 'app_subscription_index')]
    #[IsGranted('ROLE_CLIENT')]
    public function index(SubscriptionRepository $subscriptionRepository): Response
    {
        $client = $this->getUser();
       return $this->render('subscription/index.html.twig', [
            'subscriptions' => $subscriptionRepository->findBy(['client' => $client]),
        ]);
    }

    #[Route('/client/subscriptions/new', name: 'app_subscription_new')]
    #[IsGranted('ROLE_CLIENT')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $subscription = new Subscription();
        $form = $this->createForm(SubscriptionType::class, $subscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $subscription->setClient($this->getUser());
            $entityManager->persist($subscription);
            $entityManager->flush();
            return $this->redirectToRoute('app_subscription_index');
        }

        return $this->render('subscription/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
