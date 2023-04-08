<?php

declare(strict_types=1);

namespace App\Form;

use App\Form\Data\NewsletterRegistrationTypeData;
use App\Form\Type\WebspaceLocaleChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsletterRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'app.community.profile.email_label',
            ])
            ->add('locale', WebspaceLocaleChoiceType::class, [
                'label' => 'app.community.profile.prefered_language',
            ])
            ->add('send', SubmitType::class, [
                'label' => 'app.submit',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NewsletterRegistrationTypeData::class,
        ]);
    }
}
