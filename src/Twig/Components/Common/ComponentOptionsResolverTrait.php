<?php

declare(strict_types=1);

namespace App\Twig\Components\Common;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\PreMount;

trait ComponentOptionsResolverTrait
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
