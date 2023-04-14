<?php

declare(strict_types=1);

namespace App\Form;

use App\Form\Data\ContactRequestTypeData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('object', TextType::class, [
                'label' => 'app.contact_form.object_label',
            ])
            ->add('email', EmailType::class, [
                'label' => 'app.contact_form.email_label',
            ])
            ->add('message', TextareaType::class, [
                'label' => 'app.contact_form.message_label',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'app.submit',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactRequestTypeData::class,
        ]);
    }
}
