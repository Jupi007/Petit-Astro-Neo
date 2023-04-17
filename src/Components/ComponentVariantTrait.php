<?php

declare(strict_types=1);

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PreMount;

trait ComponentVariantTrait
{
    #[ExposeInTemplate(getter: 'getVariant')]
    public ?ComponentVariant $variant = null;

    /**
     * @param mixed[] $data
     *
     * @return mixed[]
     */
    #[PreMount]
    public function preMount(array $data): array
    {
        if (\array_key_exists('variant', $data) && \is_string($data['variant'])) {
            $data['variant'] = ComponentVariant::from($data['variant']);
        }

        return $data;
    }

    public function getVariant(): ?string
    {
        return $this->variant?->value;
    }
}
