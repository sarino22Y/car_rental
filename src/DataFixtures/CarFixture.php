<?php

namespace App\DataFixtures;

use App\Entity\Car;
use App\Entity\Client;
use App\Entity\Owner;
use App\Entity\Subscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CarFixture extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $owner = new Owner();
        $owner->setEmail('owner@example.com');
        $owner->setRoles(['ROLE_USER']);
        $owner->setPassword($this->passwordHasher->hashPassword($owner, 'password123'));
        $manager->persist($owner);

        $client = new Client();
        $client->setEmail('client@example.com');
        $client->setRoles(['ROLE_CLIENT']);
        $client->setPassword($this->passwordHasher->hashPassword($client, 'client123'));
        $manager->persist($client);

        $car1 = new Car();
        $car1->setName('Toyota Corolla');
        $car1->setOffer('standard');
        $car1->setDescription('Voiture économique.');
        $car1->setOwner($owner);
        $manager->persist($car1);

        $car2 = new Car();
        $car2->setName('BMW X5');
        $car2->setOffer('premium');
        $car2->setDescription('SUV de luxe.');
        $car2->setOwner($owner);
        $manager->persist($car2);

        $subscription = new Subscription();
        $subscription->setClient($client);
        $subscription->setCar($car1);
        $subscription->setStartDate(new \DateTime('2025-09-10'));
        $subscription->setEndDate(new \DateTime('2025-10-10'));
        $manager->persist($subscription);

        $manager->flush();
    }
}
