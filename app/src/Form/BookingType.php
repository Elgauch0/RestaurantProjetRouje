<?php

namespace App\Form;

use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('datetime', null, [
                'widget' => 'single_text',
                'label' => 'Date et heure de votre venue',
            ])
            ->add('guest_count', null, [
                'label' => 'nombre de convives :'
            ])
            ->add('allergies', null, [
                'label' => 'Allergies :',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Précisez les allergies éventuelles (ex: gluten, arachides...)',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Réserver ma table',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
