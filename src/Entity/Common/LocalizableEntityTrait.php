<?php

declare(strict_types=1);

namespace App\Entity\Common;

use App\Entity\Contract\EntityTranslationInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/** @template T of EntityTranslationInterface */
trait LocalizableEntityTrait
{
    private string $locale;

    /** @var Collection<string, T> */
    private Collection $translations;

    public function __localizableEntityTraitConstructor(): void
    {
        $this->translations = new ArrayCollection();
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

    /** @return string[] */
    public function getLocales(): array
    {
        return \array_keys($this->translations->toArray());
    }

    /** @return T */
    public function getTranslation(): EntityTranslationInterface
    {
        if ($this->translations->containsKey($this->locale)) {
            /** @var T */
            return $this->translations->get($this->locale);
        }

        $translation = $this->createTranslation();
        $this->translations->set($this->locale, $translation);

        return $translation;
    }

    /** @return T */
    abstract private function createTranslation(): EntityTranslationInterface;
}
