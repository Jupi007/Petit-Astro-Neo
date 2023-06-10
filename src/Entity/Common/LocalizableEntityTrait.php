<?php

declare(strict_types=1);

namespace App\Entity\Common;

use App\Entity\Contract\EntityTranslationInterface;
use Doctrine\Common\Collections\Collection;

/** @template T of EntityTranslationInterface */
trait LocalizableEntityTrait
{
    private string $locale;

    /** @var Collection<string, T> */
    private Collection $translations;

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

    /** @return ($createIfNull is true ? T : ?T) */
    private function getTranslation(bool $createIfNull = false)
    {
        if ($this->translations->containsKey($this->locale)) {
            return $this->translations->get($this->locale);
        } elseif (!$createIfNull) {
            return null;
        }

        $translation = $this->createTranslation();
        $this->translations->set($this->locale, $translation);

        return $translation;
    }

    /** @return T */
    abstract private function createTranslation();
}
