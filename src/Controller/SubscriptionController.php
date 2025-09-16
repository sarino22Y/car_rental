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

    #[Route('/client/subscriptions/{id}/edit', name: 'app_subscription_edit')]
    #[IsGranted('ROLE_CLIENT')]
    public function edit(Request $request, Subscription $subscription, EntityManagerInterface $entityManager): Response
    {
        if ($subscription->getClient() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cet abonnement.');
        }

        $form = $this->createForm(SubscriptionType::class, $subscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_subscription_index');
        }

        return $this->render('subscription/edit.html.twig', [
            'form' => $form->createView(),
            'subscription' => $subscription,
        ]);
    }

    #[Route('/client/subscriptions/{id}/delete', name: 'app_subscription_delete', methods: ['POST'])]
    #[IsGranted('ROLE_CLIENT')]
    public function delete(Request $request, Subscription $subscription, EntityManagerInterface $entityManager): Response
    {
        if ($subscription->getClient() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer cet abonnement.');
        }

        if ($this->isCsrfTokenValid('delete'.$subscription->getId(), $request->request->get('_token'))) {
            $entityManager->remove($subscription);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_subscription_index');
    }
}
