<?php

declare(strict_types=1);

namespace App\Notification;

use App\Common\DefaultLocaleGetterTrait;
use App\DomainEvent\Publication\NotifiedPublicationEvent;
use App\Entity\PublicationDimensionContent;
use App\Repository\NewsletterRegistrationRepositoryInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentManager\ContentManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Infrastructure\Sulu\Traits\ResolveContentTrait;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\BodyRendererInterface;
use Symfony\Component\Translation\LocaleSwitcher;

class PublicationEmailNotifier implements EventSubscriberInterface
{
    use DefaultLocaleGetterTrait;
    use ResolveContentTrait;

    public function __construct(
        private readonly NewsletterRegistrationRepositoryInterface $newsletterRegistrationRepository,
        private readonly WebspaceManagerInterface $webspaceManager,
        private readonly ContentManagerInterface $contentManager,
        private readonly MailerInterface $mailer,
        private readonly LocaleSwitcher $localeSwitcher,
        private readonly BodyRendererInterface $bodyRenderer,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NotifiedPublicationEvent::class => 'onPublicationNotified',
        ];
    }

    public function onPublicationNotified(NotifiedPublicationEvent $event): void
    {
        $publication = $event->getResource();
        $registrations = $this->newsletterRegistrationRepository->findAll();

        /** @var string[] */
        $locales = $this->webspaceManager->getAllLocales();
        $defaultLocale = $this->getDefaultLocale();

        /** @var array<string, array{
         *      dimensionContent: PublicationDimensionContent,
         *      data: mixed[],
         * }> */
        $localizedResolvedPublications = [];

        foreach ($locales as $publicationLocale) {
            /** @var PublicationDimensionContent|null */
            $dimensionContent = $this->resolveContent($publication, $publicationLocale);

            if (!$dimensionContent instanceof PublicationDimensionContent) {
                continue;
            }

            $localizedResolvedPublications[$publicationLocale] = [];

            $localizedResolvedPublications[$publicationLocale]['dimensionContent'] = $dimensionContent;
            $localizedResolvedPublications[$publicationLocale]['data'] = $this->contentManager->normalize($dimensionContent);
        }

        foreach ($registrations as $registration) {
            $publicationLocale = $registration->getLocale();

            if (
                !\array_key_exists($publicationLocale, $localizedResolvedPublications)
                && \array_key_exists($defaultLocale, $localizedResolvedPublications)
            ) {
                $publicationLocale = $defaultLocale;
            } else {
                $publicationLocale = (string) \array_key_first($localizedResolvedPublications);
            }

            $dimensionContent = $localizedResolvedPublications[$publicationLocale]['dimensionContent'];
            $data = $localizedResolvedPublications[$publicationLocale]['data'];

            $title = $this->getTitle($dimensionContent, $data) ?? '';
            $description = $this->getDescription($dimensionContent, $data);
            $url = $this->getUrl($data);
            $moreText = $dimensionContent->getExcerptMore();
            $imageId = $this->getImageId($dimensionContent, $data);

            $email = (new TemplatedEmail())
                ->to(new Address($registration->getEmail()))
                ->subject($title)
                ->textTemplate('emails/publication_notification.txt.twig')
                ->htmlTemplate('emails/publication_notification.html.twig')
                ->context([
                    'locale' => $registration->getLocale(),
                    'publicationLocale' => $publicationLocale,
                    'title' => $title,
                    'description' => $description,
                    'url' => $url,
                    'moreText' => $moreText,
                    'imageId' => $imageId,
                ]);

            $this->localeSwitcher->runWithLocale(
                $registration->getLocale(),
                fn () => $this->bodyRenderer->render($email),
            );

            $this->mailer->send($email);
        }
    }

    /** @param mixed[] $data */
    private function getTitle(PublicationDimensionContent $dimensionContent, array $data): ?string
    {
        if ($excerptTitle = $dimensionContent->getExcerptTitle()) {
            return $excerptTitle;
        }

        return $data['title'] ?? $data['name'] ?? null;
    }

    /** @param mixed[] $data */
    private function getDescription(PublicationDimensionContent $dimensionContent, array $data): ?string
    {
        if ($excerptDescription = $dimensionContent->getExcerptDescription()) {
            return $excerptDescription;
        }

        /** @var array{description?: string|null} $data */
        return $data['description'] ?? null;
    }

    /** @param mixed[] $data */
    private function getUrl(array $data): string
    {
        /** @var array{url: string} $data */
        return $data['url'];
    }

    /** @param mixed[] $data */
    private function getImageId(PublicationDimensionContent $dimensionContent, array $data): ?int
    {
        if (
            null !== $dimensionContent->getExcerptImage()
            && $excerptImageId = $dimensionContent->getExcerptImage()['id']
        ) {
            return $excerptImageId;
        }

        /** @var array{id: int}|null */
        $coverImage = $data['coverImage'];

        return null !== $coverImage ? $coverImage['id'] : null;
    }

    protected function getContentManager(): ContentManagerInterface
    {
        return $this->contentManager;
    }
}
