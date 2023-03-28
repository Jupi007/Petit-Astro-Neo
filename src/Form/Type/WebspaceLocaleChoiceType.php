<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WebspaceLocaleChoiceType extends AbstractType
{
    public function __construct(
        /** @var string[] */
        #[Autowire('%sulu_core.translated_locales%')]
        private readonly array $translatedLocales,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => \array_keys($this->translatedLocales),
            'choice_label' => fn ($locale) => $this->translatedLocales[$locale],
            'group_by' => fn () => null,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
