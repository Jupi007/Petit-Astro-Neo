<?php

declare(strict_types=1);

namespace App\Sulu\Community\Form;

use App\Form\Type\WebspaceLocaleChoiceType;
use Sulu\Bundle\SecurityBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
                'label' => 'app.community.profile.avatar_label',
                'mapped' => false,
                'property_path' => 'contact.avatar',
                'required' => false,
            ])
            ->add('firstName', TextType::class, [
                'label' => 'app.community.profile.first_name_label',
                'property_path' => 'contact.firstName',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'app.community.profile.last_name_label',
                'property_path' => 'contact.lastName',
            ])
            ->add('username', TextType::class, [
                'label' => 'app.community.profile.username_label',
            ])
            ->add('mainEmail', EmailType::class, [
                'label' => 'app.community.profile.email_label',
                'property_path' => 'contact.mainEmail',
            ])
            ->add('newsletter', CheckboxType::class, [
                'label' => 'app.community.registration.opt_in_newsletter',
                'property_path' => 'contact.newsletter',
                'required' => false,
            ])
            ->add('locale', WebspaceLocaleChoiceType::class, [
                'label' => 'app.community.profile.prefered_language',
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
                'required' => false,
            ]);
        // ->add('note', TextareaType::class, [
        //     'label' => 'app.community.profile.note_label',
        //     'property_path' => 'contact.note',
        //     'required' => false,
        // ])
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
