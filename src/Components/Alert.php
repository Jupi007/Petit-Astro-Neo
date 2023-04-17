<?php

declare(strict_types=1);

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent]
class Alert
{
    #[ExposeInTemplate(getter: 'getType')]
    public AlertType $type;

    public string $label;

    public bool $demissible = false;

    /**
     * @param array{type: mixed} $data
     *
     * @return mixed[]
     */
    #[PreMount]
    public function preMount(array $data): array
    {
        if (\is_string($data['type'])) {
            $data['type'] = AlertType::from($data['type']);
        }

        return $data;
    }

    public function getType(): string
    {
        return $this->type->value;
    }
}
