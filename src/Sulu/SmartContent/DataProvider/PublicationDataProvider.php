<?php

declare(strict_types=1);

namespace App\Sulu\SmartContent\DataProvider;

use App\Entity\Publication;
use App\Sulu\SmartContent\Repository\PublicationDataProviderRepository;
use Sulu\Bundle\AdminBundle\Metadata\FormMetadata\TypedFormMetadata;
use Sulu\Bundle\AdminBundle\Metadata\MetadataProviderInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentManager\ContentManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Infrastructure\Sulu\SmartContent\Provider\ContentDataProvider;
use Sulu\Component\Security\Authentication\UserInterface;
use Sulu\Component\Serializer\ArraySerializerInterface;
use Sulu\Component\SmartContent\Configuration\BuilderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

#[AutoconfigureTag('sulu.smart_content.data_provider', [
    'alias' => Publication::RESOURCE_KEY,
])]
class PublicationDataProvider extends ContentDataProvider
{
    public function __construct(
        PublicationDataProviderRepository $repository,
        ArraySerializerInterface $arraySerializer,
        ContentManagerInterface $contentManager,
        #[Autowire('@sulu_admin.form_metadata_provider')]
        private readonly MetadataProviderInterface $formMetadataProvider,
        private readonly TokenStorageInterface $tokenStorage,
    ) {
        parent::__construct(
            $repository,
            $arraySerializer,
            $contentManager,
        );
    }

    protected function configure(BuilderInterface $builder): void
    {
        parent::configure($builder);

        $builder
            ->enableTypes($this->getTypes());
    }

    /**
     * Return template types.
     *
     * @return array<int, array<string, string>>
     */
    private function getTypes(): array
    {
        $types = [];
        if ($this->tokenStorage->getToken() instanceof TokenInterface) {
            $user = $this->tokenStorage->getToken()->getUser();

            if (!$user instanceof UserInterface) {
                return $types;
            }

            /** @var TypedFormMetadata $metadata */
            $metadata = $this->formMetadataProvider->getMetadata(Publication::TEMPLATE_TYPE, $user->getLocale(), []);

            foreach ($metadata->getForms() as $form) {
                $types[] = ['type' => $form->getName(), 'title' => $form->getTitle()];
            }
        }

        return $types;
    }
}
