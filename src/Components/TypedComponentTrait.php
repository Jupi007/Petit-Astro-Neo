<?php

declare(strict_types=1);

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PreMount;

trait TypedComponentTrait
{
    #[ExposeInTemplate(getter: 'getType')]
    public ?ComponentType $type = null;

    /**
     * @param mixed[] $data
     *
     * @return mixed[]
     */
    #[PreMount]
    public function preMount(array $data): array
    {
        if (\array_key_exists('type', $data) && \is_string($data['type'])) {
            $data['type'] = ComponentType::from($data['type']);
        }

        return $data;
    }

    public function getType(): ?string
    {
        return $this->type?->value;
    }
}
