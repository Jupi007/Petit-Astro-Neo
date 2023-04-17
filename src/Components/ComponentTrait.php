<?php

declare(strict_types=1);

namespace App\Components;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\PreMount;

trait ComponentTrait
{
    /**
     * @param mixed[] $data
     *
     * @return mixed[]
     */
    #[PreMount]
    public function preMount(array $data): array
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver, $data);

        return $resolver->resolve($data);
    }

    /** @param mixed[] $data */
    public function configureOptions(OptionsResolver $resolver, array $data): void
    {
        $resolver->setDefined(\array_keys($data));
    }
}
