<?php

declare(strict_types=1);

namespace Tests\Common;

use App\Common\DoctrineListRepresentationFactory;
use PHPUnit\Framework\TestCase;
use Sulu\Component\Rest\ListBuilder\Doctrine\DoctrineListBuilder;
use Sulu\Component\Rest\ListBuilder\Doctrine\DoctrineListBuilderFactoryInterface;
use Sulu\Component\Rest\ListBuilder\Doctrine\FieldDescriptor\DoctrineFieldDescriptor;
use Sulu\Component\Rest\ListBuilder\ListRestHelperInterface;
use Sulu\Component\Rest\ListBuilder\Metadata\FieldDescriptorFactoryInterface;
use Sulu\Component\Rest\ListBuilder\PaginatedRepresentation;
use Sulu\Component\Rest\RestHelperInterface;

class DoctrineListRepresentationFactoryTest extends TestCase
{
    public function testCreateDoctrineListRepresentation(): void
    {
        $resourceKey = 'example_resource';
        $filters = ['filter1' => 'value1', 'filter2' => 'value2'];
        $parameters = ['param1' => 'value1', 'param2' => 'value2'];
        $includedFields = ['field1', 'field2'];

        $items = [
            ['id' => 1],
            ['id' => 2],
        ];
        $idsOrdering = [2, 1];
        $currentPage = 1;
        $limit = 10;

        $itemsCallback = function (array $itemsParameter) use ($items): array {
            $this->assertSame($items, $itemsParameter);

            return $items;
        };

        $restHelper = $this->createRestHelperMock();
        $listRestHelper = $this->createListRestHelperMock($idsOrdering);
        $listBuilder = $this->createListBuilderMock(
            $parameters,
            $filters,
            $includedFields,
            $items,
            $currentPage,
            $limit,
        );
        $listBuilderFactory = $this->createListBuilderFactoryMock($listBuilder);
        $fieldDescriptorFactory = $this->createFieldDescriptorFactoryMock([
            'id' => $this->createFieldDescriptorMock(),
            'filter1' => $this->createFieldDescriptorMock(),
            'filter2' => $this->createFieldDescriptorMock(),
            'field1' => $this->createFieldDescriptorMock(),
            'field2' => $this->createFieldDescriptorMock(),
        ]);

        $factory = new DoctrineListRepresentationFactory(
            $restHelper,
            $listRestHelper,
            $listBuilderFactory,
            $fieldDescriptorFactory,
        );

        $expectedResult = new PaginatedRepresentation(
            \array_reverse($items), // Items are reversed because $idsOrdering
            $resourceKey,
            $currentPage,
            $limit,
            2,
        );
        $result = $factory->createDoctrineListRepresentation($resourceKey, $filters, $parameters, $includedFields, $itemsCallback);

        $this->assertSame($expectedResult->toArray(), $result->toArray());
    }

    private function createRestHelperMock(): RestHelperInterface
    {
        return $this->createMock(RestHelperInterface::class);
    }

    /** @param int[] $ids */
    private function createListRestHelperMock(array $ids): ListRestHelperInterface
    {
        $listRestHelper = $this->createMock(ListRestHelperInterface::class);
        $listRestHelper
            ->expects($this->once())
            ->method('getIds')
            ->willReturn($ids);

        return $listRestHelper;
    }

    /**
     * @param array<string, string> $parameters
     * @param array<string, string> $filters
     * @param array<string> $includedFields
     * @param array<mixed> $items
     */
    private function createListBuilderMock(
        array $parameters,
        array $filters,
        array $includedFields,
        array $items,
        int $currentPage,
        int $limit,
    ): DoctrineListBuilder {
        $listBuilder = $this->createMock(DoctrineListBuilder::class);
        $listBuilder
            ->expects($this->exactly(\count($parameters)))
            ->method('setParameter');
        $listBuilder
            ->expects($this->exactly(\count($filters)))
            ->method('where');
        $listBuilder
            ->expects($this->exactly(\count($includedFields)))
            ->method('addSelectField');

        $listBuilder->method('execute')->willReturn($items);
        $listBuilder->method('getCurrentPage')->willReturn($currentPage);
        $listBuilder->method('getLimit')->willReturn($limit);
        $listBuilder->method('count')->willReturn(\count($items));

        return $listBuilder;
    }

    private function createListBuilderFactoryMock(DoctrineListBuilder $listBuilder): DoctrineListBuilderFactoryInterface
    {
        $listBuilderFactory = $this->createMock(DoctrineListBuilderFactoryInterface::class);
        $listBuilderFactory->method('create')->willReturn($listBuilder);

        return $listBuilderFactory;
    }

    /** @param DoctrineFieldDescriptor[] $fieldDescriptors */
    private function createFieldDescriptorFactoryMock(array $fieldDescriptors): FieldDescriptorFactoryInterface
    {
        $fieldDescriptorFactory = $this->createMock(FieldDescriptorFactoryInterface::class);
        $fieldDescriptorFactory->method('getFieldDescriptors')->willReturn($fieldDescriptors);

        return $fieldDescriptorFactory;
    }

    private function createFieldDescriptorMock(): DoctrineFieldDescriptor
    {
        $fieldDescriptor = $this->createMock(DoctrineFieldDescriptor::class);
        $fieldDescriptor->method('getEntityName')->willReturn('ExampleEntity');

        return $fieldDescriptor;
    }
}
