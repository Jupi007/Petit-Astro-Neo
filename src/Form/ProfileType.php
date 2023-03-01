<?php

declare(strict_types=1);

namespace App\Form;

use Sulu\Bundle\SecurityBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('avatar', FileType::class, [
                'mapped' => false,
                'property_path' => 'contact.avatar',
                'required' => false,
            ])
            ->add('firstName', TextType::class, [
                'property_path' => 'contact.firstName',
            ])
            ->add('lastName', TextType::class, [
                'property_path' => 'contact.lastName',
            ])
            ->add('username', TextType::class)
            ->add('mainEmail', EmailType::class, [
                'property_path' => 'contact.mainEmail',
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
                'mapped' => false,
                'required' => false,
            ])
            ->add('note', TextareaType::class, [
                'property_path' => 'contact.note',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
                'validation_groups' => ['profile'],
            ],
        );
    }
}
