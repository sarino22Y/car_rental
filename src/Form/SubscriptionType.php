<?php

namespace App\Form;

use App\Entity\Car;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class, ['label' => 'Date de début', 'widget' => 'single_text'])
            ->add('endDate', DateType::class, ['label' => 'Date de fin', 'widget' => 'single_text', 'required' => false])
            ->add('car', EntityType::class, [
                'class' => Car::class,
                'choice_label' => 'name',
                'label' => 'Voiture',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
