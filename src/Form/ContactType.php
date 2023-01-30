<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', ChoiceType::class, [
                'choices' => [
                    'Mr.' => 'Mr.',
                    'Mrs.' => 'Mrs.',
                    'Miss' => 'Miss',
                ],
            ])
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('birthdate', DateType::class, [
                'years' => range(date('Y')-120, date('Y')),
                ])
            ->add('email', TextType::class)
            ->add('phones', CollectionType::class, [
                'entry_type' => PhoneType::class,
                'entry_options' => [
                    'attr' => ['class' => 'phone-row'],
                    'label' => false
                    ],
            ])
            ->add('Add_phone', SubmitType::class)
            ->add('Save', SubmitType::class)
            ->add('Delete', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
            'required' => false,
        ]);
    }
}
