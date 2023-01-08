<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Contract\Localizable;
use App\Repository\DefinitionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sulu\Bundle\RouteBundle\Model\RoutableInterface;
use Sulu\Bundle\RouteBundle\Model\RouteInterface;
use Sulu\Component\Security\Authentication\UserInterface;

#[ORM\Entity(repositoryClass: DefinitionRepository::class)]
class Definition implements Localizable, RoutableInterface
{
    final public const RESOURCE_KEY = 'definitions';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<string, DefinitionTranslation>
     */
    #[ORM\OneToMany(targetEntity: DefinitionTranslation::class, mappedBy: 'definition', cascade: ['persist'], indexBy: 'locale')]
    private Collection $translations;

    private string $locale;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return DefinitionTranslation[]
     */
    public function getTranslations(): array
    {
        return $this->translations->toArray();
    }

    protected function getTranslation(string $locale): ?DefinitionTranslation
    {
        if (!$this->translations->containsKey($locale)) {
            return null;
        }

        return $this->translations->get($locale);
    }

    protected function createTranslation(string $locale): DefinitionTranslation
    {
        $translation = new DefinitionTranslation($this, $locale);
        $this->translations->set($locale, $translation);

        return $translation;
    }

    public function getTitle(): ?string
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation instanceof DefinitionTranslation) {
            return null;
        }

        return $translation->getTitle();
    }

    public function setTitle(string $title): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation instanceof DefinitionTranslation) {
            $translation = $this->createTranslation($this->locale);
        }

        $translation->setTitle($title);

        return $this;
    }

    public function getContent(): ?string
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation instanceof DefinitionTranslation) {
            return null;
        }

        return $translation->getContent();
    }

    public function setContent(string $content): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation instanceof DefinitionTranslation) {
            $translation = $this->createTranslation($this->locale);
        }

        $translation->setContent($content);

        return $this;
    }

    public function getCreator(): ?UserInterface
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation instanceof DefinitionTranslation) {
            return null;
        }

        return $translation->getCreator();
    }

    public function setCreator(?UserInterface $creator): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation instanceof DefinitionTranslation) {
            $translation = $this->createTranslation($this->locale);
        }

        $translation->setCreator($creator);

        return $this;
    }

    public function getChanger(): ?UserInterface
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation instanceof DefinitionTranslation) {
            return null;
        }

        return $translation->getChanger();
    }

    public function setChanger(?UserInterface $changer): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation instanceof DefinitionTranslation) {
            $translation = $this->createTranslation($this->locale);
        }

        $translation->setChanger($changer);

        return $this;
    }

    public function getCreated(): ?\DateTime
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation instanceof DefinitionTranslation) {
            return null;
        }

        return $translation->getCreated();
    }

    public function setCreated(\DateTime $created): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation instanceof DefinitionTranslation) {
            $translation = $this->createTranslation($this->locale);
        }

        $translation->setCreated($created);

        return $this;
    }

    public function getChanged(): ?\DateTime
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation instanceof DefinitionTranslation) {
            return null;
        }

        return $translation->getChanged();
    }

    public function setChanged(\DateTime $changed): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation instanceof DefinitionTranslation) {
            $translation = $this->createTranslation($this->locale);
        }

        $translation->setChanged($changed);

        return $this;
    }

    public function getRoute(): ?RouteInterface
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation instanceof DefinitionTranslation) {
            return null;
        }

        return $translation->getRoute();
    }

    public function setRoute(RouteInterface $route): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation instanceof DefinitionTranslation) {
            $translation = $this->createTranslation($this->locale);
        }

        $translation->setRoute($route);

        return $this;
    }
}
