<?php

declare(strict_types=1);

namespace App\Form;

use Sulu\Bundle\ContactBundle\Entity\Contact;
use Sulu\Bundle\SecurityBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'app.community.profile.first_name_label',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'app.community.profile.last_name_label',
            ])
            ->add('username', TextType::class, [
                'label' => 'app.community.profile.username_label',
            ])
            ->add('email', EmailType::class, [
                'label' => 'app.community.profile.email_label',
            ])
            ->add('newsletter', CheckboxType::class, [
                'label' => 'app.community.registration.opt_in_newsletter',
                'property_path' => 'contact.newsletter',
                'required' => false,
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'app.form.password_label',
                ],
                'second_options' => [
                    'label' => 'app.form.password_repeat_label',
                ],
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'app.submit',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
                'validation_groups' => ['registration'],
                'empty_data' => function (FormInterface $form) {
                    $user = new User();
                    $user->setContact(new Contact());

                    return $user;
                },
            ],
        );
    }
}
