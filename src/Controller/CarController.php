<?php

namespace App\Controller;

use App\Entity\Car;
use App\Form\CarType;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CarController extends AbstractController
{
    #[Route('/cars', name: 'app_cars_public')]
    public function publicIndex(CarRepository $carRepository): Response
    {
        return $this->render('car/public_index.html.twig', [
            'cars' => $carRepository->findAll(),
        ]);
    }

    #[Route('/admin/cars', name: 'app_admin_cars')]
    #[IsGranted('ROLE_OWNER')]
    public function index(CarRepository $carRepository): Response
    {
        $owner = $this->getUser();
        if (!$owner) {
            return $this->redirectToRoute('app_login');
        }
        $cars = $carRepository->findBy(['owner' => $owner]);
        return $this->render('car/index.html.twig', ['cars' => $cars]);
    }

    #[Route('/admin/car/new', name: 'app_car_new')]
    #[IsGranted('ROLE_OWNER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $car->setOwner($this->getUser());
            $entityManager->persist($car);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_cars');
        }

        return $this->render('car/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/cars/{id}/edit', name: 'app_car_edit')]
    #[IsGranted('ROLE_OWNER')]
    public function edit(Request $request, Car $car, EntityManagerInterface $entityManager): Response
    {
        if ($car->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette voiture.');
        }

        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_cars');
        }

        return $this->render('car/edit.html.twig', [
            'form' => $form->createView(),
            'car' => $car,
        ]);
    }

    #[Route('/admin/cars/{id}/delete', name: 'app_car_delete', methods: ['POST'])]
    #[IsGranted('ROLE_OWNER')]
    public function delete(Request $request, Car $car, EntityManagerInterface $entityManager): Response
    {
        if ($car->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer cette voiture.');
        }

        if ($this->isCsrfTokenValid('delete'.$car->getId(), $request->request->get('_token'))) {
            $entityManager->remove($car);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_cars');
    }
}
