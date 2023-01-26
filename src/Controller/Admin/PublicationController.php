<?php

declare(strict_types=1);

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller\Admin;

use App\Admin\PublicationAdmin;
use App\Entity\Publication;
use App\Entity\PublicationDimensionContent;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandlerInterface;
use HandcraftedInTheAlps\RestRoutingBundle\Routing\ClassResourceInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentIndexer\ContentIndexerInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentManager\ContentManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\DimensionContentInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\WorkflowInterface;
use Sulu\Component\Rest\AbstractRestController;
use Sulu\Component\Rest\Exception\RestException;
use Sulu\Component\Rest\ListBuilder\Doctrine\DoctrineListBuilder;
use Sulu\Component\Rest\ListBuilder\Doctrine\DoctrineListBuilderFactoryInterface;
use Sulu\Component\Rest\ListBuilder\Doctrine\FieldDescriptor\DoctrineFieldDescriptorInterface;
use Sulu\Component\Rest\ListBuilder\Metadata\FieldDescriptorFactoryInterface;
use Sulu\Component\Rest\ListBuilder\PaginatedRepresentation;
use Sulu\Component\Rest\RestHelperInterface;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/admin/api/publications')]
class PublicationController extends AbstractRestController implements ClassResourceInterface, SecuredControllerInterface
{
    public function __construct(
        ViewHandlerInterface $viewHandler,
        TokenStorageInterface $tokenStorage,
        private readonly FieldDescriptorFactoryInterface $fieldDescriptorFactory,
        private readonly DoctrineListBuilderFactoryInterface $listBuilderFactory,
        private readonly RestHelperInterface $restHelper,
        private readonly ContentManagerInterface $contentManager,
        private readonly ContentIndexerInterface $contentIndexer,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($viewHandler, $tokenStorage);
    }

    public function getSecurityContext(): string
    {
        return PublicationAdmin::SECURITY_CONTEXT;
    }

    #[Rest\Get(name: 'app.admin.get_publication_list')]
    public function cgetAction(Request $request): Response
    {
        /** @var DoctrineFieldDescriptorInterface[] $fieldDescriptors */
        $fieldDescriptors = $this->fieldDescriptorFactory->getFieldDescriptors(Publication::RESOURCE_KEY);
        /** @var DoctrineListBuilder $listBuilder */
        $listBuilder = $this->listBuilderFactory->create(Publication::class);
        $listBuilder->addSelectField($fieldDescriptors['locale']);
        $listBuilder->addSelectField($fieldDescriptors['ghostLocale']);
        $listBuilder->setParameter('locale', $request->query->get('locale'));
        $this->restHelper->initializeListBuilder($listBuilder, $fieldDescriptors);

        $listRepresentation = new PaginatedRepresentation(
            $listBuilder->execute(),
            Publication::RESOURCE_KEY,
            (int) $listBuilder->getCurrentPage(),
            (int) $listBuilder->getLimit(),
            $listBuilder->count(),
        );

        return $this->handleView($this->view($listRepresentation));
    }

    #[Rest\Get(path: '/{id}', name: 'app.admin.get_publication')]
    public function getAction(Request $request, int $id): Response
    {
        /** @var Publication|null $publication */
        $publication = $this->entityManager->getRepository(Publication::class)->findOneBy(['id' => $id]);

        if (!$publication) {
            throw new NotFoundHttpException();
        }

        $dimensionAttributes = $this->getDimensionAttributes($request);
        $dimensionContent = $this->contentManager->resolve($publication, $dimensionAttributes);

        return $this->handleView($this->view($this->normalize($publication, $dimensionContent)));
    }

    #[Rest\Post(name: 'app.admin.post_definition')]
    public function postAction(Request $request): Response
    {
        $publication = new Publication();

        $data = $this->getData($request);
        $dimensionAttributes = $this->getDimensionAttributes($request); // ["locale" => "en", "stage" => "draft"]

        $dimensionContent = $this->contentManager->persist($publication, $data, $dimensionAttributes);

        $this->entityManager->persist($publication);
        $this->entityManager->flush();

        if ('publish' === $request->query->get('action')) {
            $dimensionContent = $this->contentManager->applyTransition(
                $publication,
                $dimensionAttributes,
                WorkflowInterface::WORKFLOW_TRANSITION_PUBLISH,
            );

            $this->entityManager->flush();

            // Index live dimension content
            // $this->contentIndexer->index($publication, \array_merge($dimensionAttributes, [
            //     'stage' => DimensionContentInterface::STAGE_LIVE,
            // ]));
        }

        // Index draft dimension content
        // $this->contentIndexer->indexDimensionContent($dimensionContent);

        return $this->handleView($this->view($this->normalize($publication, $dimensionContent), 201));
    }

    #[Rest\Post(path: '/{id}', name: 'app.admin.post_trigger_publication')]
    public function postTriggerAction(string $id, Request $request): Response
    {
        /** @var Publication|null $publication */
        $publication = $this->entityManager->getRepository(Publication::class)->findOneBy(['id' => $id]);

        if (!$publication) {
            throw new NotFoundHttpException();
        }

        $dimensionAttributes = $this->getDimensionAttributes($request); // ["locale" => "en", "stage" => "draft"]
        $action = $request->query->get('action');

        switch ($action) {
            case 'copy-locale':
                $dimensionContent = $this->contentManager->copy(
                    $publication,
                    [
                        'stage' => DimensionContentInterface::STAGE_DRAFT,
                        'locale' => $request->query->get('src'),
                    ],
                    $publication,
                    [
                        'stage' => DimensionContentInterface::STAGE_DRAFT,
                        'locale' => $request->query->get('dest'),
                    ],
                );

                $this->entityManager->flush();

                return $this->handleView($this->view($this->normalize($publication, $dimensionContent)));
            case 'unpublish':
                $dimensionContent = $this->contentManager->applyTransition(
                    $publication,
                    $dimensionAttributes,
                    WorkflowInterface::WORKFLOW_TRANSITION_UNPUBLISH,
                );

                $this->entityManager->flush();

                // Deindex live dimension content
                // $this->contentIndexer->deindex(Publication::RESOURCE_KEY, $id, \array_merge(
                //     $dimensionAttributes,
                //     ['stage' => DimensionContentInterface::STAGE_LIVE],
                // ));

                return $this->handleView($this->view($this->normalize($publication, $dimensionContent)));
            case 'remove-draft':
                $dimensionContent = $this->contentManager->applyTransition(
                    $publication,
                    $dimensionAttributes,
                    WorkflowInterface::WORKFLOW_TRANSITION_REMOVE_DRAFT,
                );

                $this->entityManager->flush();

                // Index draft dimension content
                // $this->contentIndexer->indexDimensionContent($dimensionContent);

                return $this->handleView($this->view($this->normalize($publication, $dimensionContent)));
            default:
                throw new RestException('Unrecognized action: ' . $action);
        }
    }

    #[Rest\Put(path: '/{id}', name: 'app.admin.put_publication')]
    public function putAction(Request $request, int $id): Response
    {
        /** @var Publication|null $publication */
        $publication = $this->entityManager->getRepository(Publication::class)->findOneBy(['id' => $id]);

        if (!$publication) {
            throw new NotFoundHttpException();
        }

        $data = $this->getData($request);
        $dimensionAttributes = $this->getDimensionAttributes($request); // ["locale" => "en", "stage" => "draft"]

        /** @var PublicationDimensionContent $dimensionContent */
        $dimensionContent = $this->contentManager->persist($publication, $data, $dimensionAttributes);
        if (WorkflowInterface::WORKFLOW_PLACE_PUBLISHED === $dimensionContent->getWorkflowPlace()) {
            $dimensionContent = $this->contentManager->applyTransition(
                $publication,
                $dimensionAttributes,
                WorkflowInterface::WORKFLOW_TRANSITION_CREATE_DRAFT,
            );
        }

        $this->entityManager->flush();

        if ('publish' === $request->query->get('action')) {
            $dimensionContent = $this->contentManager->applyTransition(
                $publication,
                $dimensionAttributes,
                WorkflowInterface::WORKFLOW_TRANSITION_PUBLISH,
            );

            $this->entityManager->flush();

            // Index live dimension content
            // $this->contentIndexer->index($publication, \array_merge($dimensionAttributes, [
            //     'stage' => DimensionContentInterface::STAGE_LIVE,
            // ]));
        }

        // Index draft dimension content
        // $this->contentIndexer->indexDimensionContent($dimensionContent);

        return $this->handleView($this->view($this->normalize($publication, $dimensionContent)));
    }

    #[Rest\Delete(path: '/{id}', name: 'app.admin.delete_publication')]
    public function deleteAction(int $id): Response
    {
        /** @var Publication $publication */
        $publication = $this->entityManager->getReference(Publication::class, $id);

        $this->entityManager->remove($publication);
        $this->entityManager->flush();

        // Remove all documents with given id from index
        // $this->contentIndexer->deindex(Publication::RESOURCE_KEY, $id);

        return new Response('', 204);
    }

    /**
     * Will return e.g. ['locale' => 'en'].
     *
     * @return array<string, mixed>
     */
    protected function getDimensionAttributes(Request $request): array
    {
        return $request->query->all();
    }

    /**
     * Will return e.g. ['title' => 'Test', 'template' => 'publication-2', ...].
     *
     * @return array<string, mixed>
     */
    protected function getData(Request $request): array
    {
        $data = $request->request->all();

        return $data;
    }

    /**
     * Resolve will convert the resolved DimensionContentInterface object into a normalized array.
     *
     * @return mixed[]
     */
    protected function normalize(Publication $publication, DimensionContentInterface $dimensionContent): array
    {
        $normalizedContent = $this->contentManager->normalize($dimensionContent);

        return $normalizedContent;
    }
}
