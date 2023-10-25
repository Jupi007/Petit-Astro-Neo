<?php

declare(strict_types=1);

namespace App\Sulu\Markup;

use Sulu\Bundle\MarkupBundle\Tag\TagInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Twig\Environment;

#[AutoconfigureTag(
    name: 'sulu_markup.tag',
    attributes: [
        'tag' => 'reference',
        'type' => 'html',
    ],
)]
readonly class ReferenceLinkTag implements TagInterface
{
    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    /**
     * @param array<string, array{type?: string}> $attributesByTag
     * @param string $locale
     *
     * @return array<string, string>
     */
    public function parseAll(array $attributesByTag, $locale): array
    {
        $result = [];
        $references = [];
        $referenceIndex = 0;

        foreach ($attributesByTag as $key => $value) {
            if (isset($value['type']) && 'list' === $value['type']) {
                $result[$key] = $this->twig->render('markup/reference_link_list.html.twig', [
                    ...$value,
                    'references' => $references,
                    'locale' => $locale,
                ]);
                $references = [];
            } else {
                ++$referenceIndex;
                $data = [
                    ...$value,
                    'index' => $referenceIndex,
                ];

                $references[$referenceIndex] = $data;
                $result[$key] = $this->twig->render('markup/reference_link.html.twig', [
                    ...$data,
                    'locale' => $locale,
                ]);
            }
        }

        return $result;
    }

    /**
     * @param array<string, array{provider: string}> $attributesByTag
     * @param string $locale
     *
     * @return array<string, string>
     */
    public function validateAll(array $attributesByTag, $locale): array
    {
        return [];
    }
}
