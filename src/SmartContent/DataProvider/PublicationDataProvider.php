<?php

declare(strict_types=1);

namespace App\SmartContent\DataProvider;

use App\Admin\PublicationAdmin;
use App\Entity\Publication;
use App\Repository\PublicationRepository;
use App\SmartContent\DataItem\PublicationDataItem;
use Sulu\Component\Serializer\ArraySerializerInterface;
use Sulu\Component\SmartContent\Orm\BaseDataProvider;

class PublicationDataProvider extends BaseDataProvider
{
    public function __construct(
        PublicationRepository $repository,
        ArraySerializerInterface $serializer,
    ) {
        parent::__construct($repository, $serializer);
    }

    public function getConfiguration()
    {
        if (null === $this->configuration) {
            $this->configuration = self::createConfigurationBuilder()
                ->enableLimit()
                ->enablePagination()
                ->enablePresentAs()
                ->enableSorting([
                    ['column' => null, 'title' => 'sulu_admin.default'],
                    ['column' => 'translation.title', 'title' => 'sulu_admin.title'],
                    ['column' => 'translation.created', 'title' => 'sulu_admin.created'],
                    ['column' => 'translation.changed', 'title' => 'sulu_admin.changed'],
                    ['column' => 'translation.authored', 'title' => 'sulu_admin.authored'],
                ])
                ->enableView(PublicationAdmin::EDIT_FORM_VIEW, [
                    'id' => 'id',
                ])
                ->getConfiguration();
        }

        return $this->configuration;
    }

    /**
     * @param Publication[] $data
     *
     * @return PublicationDataItem[]
     */
    protected function decorateDataItems(array $data): array
    {
        return \array_map(
            fn (Publication $publication) => new PublicationDataItem($publication),
            $data,
        );
    }
}
