<?php

declare(strict_types=1);

namespace App\Form;

use Sulu\Bundle\CommunityBundle\Validator\Constraints\Exist;
use Sulu\Bundle\SecurityBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordForgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('email_username', TextType::class, [
            'constraints' => new Exist([
                'columns' => ['email', 'username'],
                'entity' => $options['user_class'],
                'groups' => 'password_forget',
            ]),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'user_class' => User::class,
            'validation_groups' => ['password_forget'],
        ]);
    }
}
