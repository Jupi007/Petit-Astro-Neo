<?php

declare(strict_types=1);

namespace App\Components;

use Symfony\Component\OptionsResolver\OptionsResolver;

trait ComponentVariantTrait
{
    use ComponentTrait {
        configureOptions as private parentConfigureOptions;
    }

    public ?string $variant = null;

    /** @param mixed[] $data */
    public function configureOptions(OptionsResolver $resolver, array $data): void
    {
        $this->parentConfigureOptions($resolver, $data);

        $resolver
            ->setDefault('variant', null)
            ->setAllowedValues('variant', [
                null,
                'info',
                'success',
                'warning',
                'error',
            ]);
    }
}
