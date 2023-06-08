<?php

declare(strict_types=1);

namespace App\Sulu\Admin;

use App\Entity\Publication;
use App\Sulu\Security\PermissionTypes;
use Sulu\Bundle\ActivityBundle\Infrastructure\Sulu\Admin\View\ActivityViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\FormViewBuilderInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Bundle\ContentBundle\Content\Infrastructure\Sulu\Admin\ContentViewBuilderFactoryInterface;
use Sulu\Component\Localization\Manager\LocalizationManagerInterface;
use Sulu\Component\Security\Authorization\PermissionTypes as SuluPermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;

class PublicationAdmin extends Admin
{
    final public const SECURITY_CONTEXT = 'app.settings.publications';

    final public const NAVIGATION_ITEM = 'app.admin.publications';

    final public const LIST_KEY = 'publications';
    final public const FORM_KEY = 'publication_details';

    final public const LIST_VIEW = 'app.publications_list';
    final public const ADD_FORM_VIEW = 'app.publication_add_form';
    final public const EDIT_FORM_VIEW = 'app.publication_edit_form';

    public function __construct(
        private readonly ViewBuilderFactoryInterface $viewBuilderFactory,
        private readonly ContentViewBuilderFactoryInterface $contentViewBuilderFactory,
        private readonly SecurityCheckerInterface $securityChecker,
        private readonly LocalizationManagerInterface $localizationManager,
        private readonly ActivityViewBuilderFactoryInterface $activityViewBuilderFactory,
    ) {
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        if ($this->securityChecker->hasPermission(static::SECURITY_CONTEXT, SuluPermissionTypes::VIEW)) {
            $navigationParent = new NavigationItem(static::NAVIGATION_ITEM);
            $navigationParent->setPosition(40);
            $navigationParent->setIcon(Publication::RESOURCE_ICON);

            $navigationItem = new NavigationItem(static::NAVIGATION_ITEM);
            $navigationItem->setPosition(10);
            $navigationItem->setView(static::LIST_VIEW);

            $navigationParent->addChild($navigationItem);
            $navigationItemCollection->add($navigationParent);
        }
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $locales = $this->localizationManager->getLocales();

        $formToolbarActions = [];
        $listToolbarActions = [];

        if ($this->securityChecker->hasPermission(static::SECURITY_CONTEXT, SuluPermissionTypes::ADD)) {
            $listToolbarActions[] = new ToolbarAction('sulu_admin.add');
        }

        if ($this->securityChecker->hasPermission(static::SECURITY_CONTEXT, SuluPermissionTypes::EDIT)) {
            $formToolbarActions[] = new ToolbarAction('sulu_admin.save');
        }

        if ($this->securityChecker->hasPermission(static::SECURITY_CONTEXT, SuluPermissionTypes::DELETE)) {
            $formToolbarActions[] = new ToolbarAction('sulu_admin.delete');
            $listToolbarActions[] = new ToolbarAction('sulu_admin.delete');
        }

        if ($this->securityChecker->hasPermission(static::SECURITY_CONTEXT, SuluPermissionTypes::VIEW)) {
            $listToolbarActions[] = new ToolbarAction('sulu_admin.export');
        }

        if ($this->securityChecker->hasPermission(static::SECURITY_CONTEXT, SuluPermissionTypes::EDIT)) {
            $viewCollection->add(
                $this->viewBuilderFactory->createListViewBuilder(
                    static::LIST_VIEW,
                    '/' . Publication::RESOURCE_KEY . '/:locale',
                )
                ->setResourceKey(Publication::RESOURCE_KEY)
                ->setListKey(static::LIST_KEY)
                ->setTitle('app.admin.publications')
                ->addListAdapters(['table'])
                ->addLocales($locales)
                ->setDefaultLocale($locales[0])
                ->setAddView(static::ADD_FORM_VIEW)
                ->setEditView(static::EDIT_FORM_VIEW)
                ->addToolbarActions($listToolbarActions),
            );
            $viewCollection->add(
                $this->viewBuilderFactory->createResourceTabViewBuilder(
                    static::ADD_FORM_VIEW,
                    '/' . Publication::RESOURCE_KEY . '/:locale/add',
                )
                ->setResourceKey(Publication::RESOURCE_KEY)
                ->addLocales($locales)
                ->setBackView(static::LIST_VIEW),
            );
            $viewCollection->add(
                $this->viewBuilderFactory->createResourceTabViewBuilder(
                    static::EDIT_FORM_VIEW,
                    '/' . Publication::RESOURCE_KEY . '/:locale/:id',
                )
                ->setResourceKey(Publication::RESOURCE_KEY)
                ->addLocales($locales)
                ->setBackView(static::LIST_VIEW)
                ->setTitleProperty('name'),
            );

            $viewBuilders = $this->contentViewBuilderFactory->createViews(
                Publication::class,
                static::EDIT_FORM_VIEW,
                static::ADD_FORM_VIEW,
                static::SECURITY_CONTEXT,
            );

            foreach ($viewBuilders as $viewBuilder) {
                $viewCollection->add($viewBuilder);
            }

            if ($this->securityChecker->hasPermission(static::SECURITY_CONTEXT, PermissionTypes::NOTIFY)) {
                /** @var FormViewBuilderInterface */
                $editFormView = $viewCollection->get(static::EDIT_FORM_VIEW . '.content');
                $editFormView->addToolbarActions([
                    new ToolbarAction('app.notify'),
                ]);
            }

            if ($this->activityViewBuilderFactory->hasActivityListPermission()) {
                $viewCollection->add(
                    $this->activityViewBuilderFactory
                        ->createActivityListViewBuilder(
                            name: static::EDIT_FORM_VIEW . '.activity',
                            path: '/activity',
                            resourceKey: Publication::RESOURCE_KEY,
                        )
                        ->setTabOrder(70)
                        ->setParent(static::EDIT_FORM_VIEW),
                );
            }
        }
    }

    /**
     * @return mixed[]
     */
    public function getSecurityContexts()
    {
        return [
            self::SULU_ADMIN_SECURITY_SYSTEM => [
                'Publications' => [
                    static::SECURITY_CONTEXT => [
                        SuluPermissionTypes::VIEW,
                        SuluPermissionTypes::ADD,
                        SuluPermissionTypes::EDIT,
                        SuluPermissionTypes::DELETE,
                        SuluPermissionTypes::LIVE,
                        PermissionTypes::NOTIFY,
                    ],
                ],
            ],
        ];
    }
}
