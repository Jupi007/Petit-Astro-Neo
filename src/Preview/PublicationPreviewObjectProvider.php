<?php

declare(strict_types=1);

namespace App\Preview;

use App\Admin\PublicationAdmin;
use App\Entity\Publication;
use App\Repository\PublicationRepository;
use Sulu\Bundle\PreviewBundle\Preview\Object\PreviewObjectProviderInterface;

class PublicationPreviewObjectProvider implements PreviewObjectProviderInterface
{
    public function __construct(private readonly PublicationRepository $publicationRepository)
    {
    }

    /**
     * @param string $id
     * @param string $locale
     */
    public function getObject($id, $locale): ?Publication
    {
        return $this->publicationRepository->findByIdLocalized((int) $id, $locale);
    }

    /** @param Publication $object */
    public function getId($object): string
    {
        return (string) $object->getId();
    }

    /**
     * @param Publication $object
     * @param string $locale
     * @param mixed[] $data
     */
    public function setValues($object, $locale, array $data): void
    {
        $object
            ->setLocale($locale)
            ->setTitle($data['title'])
            ->setSubtitle($data['subtitle'])
            ->setBlocks($data['blocks']);
    }

    /**
     * @param Publication $object
     * @param string $locale
     * @param mixed[] $context
     */
    public function setContext($object, $locale, array $context): Publication
    {
        return $object;
    }

    /** @param Publication $object */
    public function serialize($object): string
    {
        return \serialize($object);
    }

    /**
     * @param string $serializedObject
     * @param string $objectClass
     */
    public function deserialize($serializedObject, $objectClass): ?Publication
    {
        $object = \unserialize($serializedObject);

        if ($object instanceof Publication) {
            return $object;
        }

        return null;
    }

    public function getSecurityContext($id, $locale): ?string
    {
        return PublicationAdmin::SECURITY_CONTEXT;
    }
}
