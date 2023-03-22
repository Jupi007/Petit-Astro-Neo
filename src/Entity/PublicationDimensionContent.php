<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Contract\PersistableEntityInterface;
use App\Entity\Trait\PersistableEntityTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\AuthorInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\AuthorTrait;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\DimensionContentInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\DimensionContentTrait;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\ExcerptInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\ExcerptTrait;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\RoutableInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\RoutableTrait;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\SeoInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\SeoTrait;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\TemplateInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\TemplateTrait;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\WebspaceInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\WebspaceTrait;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\WorkflowInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\WorkflowTrait;

/** @implements DimensionContentInterface<Publication> */
#[ORM\Entity]
class PublicationDimensionContent implements PersistableEntityInterface, DimensionContentInterface, ExcerptInterface, SeoInterface, TemplateInterface, RoutableInterface, WorkflowInterface, AuthorInterface, WebspaceInterface
{
    use AuthorTrait;
    use DimensionContentTrait;
    use ExcerptTrait;
    use PersistableEntityTrait;
    use RoutableTrait;
    use SeoTrait;
    use TemplateTrait {
        setTemplateData as parentSetTemplateData;
    }
    use WebspaceTrait;
    use WorkflowTrait;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $title;

    #[ORM\ManyToOne(targetEntity: Publication::class, inversedBy: 'dimensionContents')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Publication $publication;

    public function __construct(Publication $publication)
    {
        $this->publication = $publication;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getResource(): Publication
    {
        return $this->publication;
    }

    public function setTemplateData(array $templateData): void
    {
        if (\array_key_exists('title', $templateData)) {
            $this->title = $templateData['title'];
        }

        $this->parentSetTemplateData($templateData);
    }

    public static function getTemplateType(): string
    {
        return Publication::TEMPLATE_TYPE;
    }

    public static function getResourceKey(): string
    {
        return Publication::RESOURCE_KEY;
    }
}
