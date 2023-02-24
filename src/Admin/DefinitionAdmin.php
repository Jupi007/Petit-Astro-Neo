<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Definition;
use Sulu\Bundle\ActivityBundle\Infrastructure\Sulu\Admin\View\ActivityViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

class DefinitionAdmin extends Admin
{
    final public const SECURITY_CONTEXT = 'app.settings.definitions';

    final public const NAVIGATION_ITEM = 'app.admin.lexicon';

    final public const LIST_KEY = 'definitions';
    final public const FORM_KEY = 'definition_details';

    final public const LIST_VIEW = 'app.definitions_list';
    final public const ADD_FORM_VIEW = 'app.definition_add_form';
    final public const EDIT_FORM_VIEW = 'app.definition_edit_form';

    public function __construct(
        private readonly ViewBuilderFactoryInterface $viewBuilderFactory,
        private readonly WebspaceManagerInterface $webspaceManager,
        private readonly ActivityViewBuilderFactoryInterface $activityViewBuilderFactory,
        private readonly SecurityCheckerInterface $securityChecker,
    ) {
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        if ($this->securityChecker->hasPermission(static::SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            $item = new NavigationItem(static::NAVIGATION_ITEM);
            $item->setPosition(40);
            $item->setIcon(Definition::RESOURCE_ICON);
            $item->setView(static::LIST_VIEW);

            $navigationItemCollection->add($item);
        }
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $locales = $this->webspaceManager->getAllLocales();

        $this->configureListView($viewCollection, $locales);
        $this->configureAddView($viewCollection, $locales);
        $this->configureEditView($viewCollection, $locales);
    }

    /** @param string[] $locales */
    private function configureListView(ViewCollection $viewCollection, array $locales): void
    {
        /** @var ToolbarAction[] */
        $listToolbarActions = [];

        if ($this->securityChecker->hasPermission(static::SECURITY_CONTEXT, PermissionTypes::ADD)) {
            $listToolbarActions[] = new ToolbarAction('sulu_admin.add');
        }
        if ($this->securityChecker->hasPermission(static::SECURITY_CONTEXT, PermissionTypes::DELETE)) {
            $listToolbarActions[] = new ToolbarAction('sulu_admin.delete');
        }

        $listView = $this->viewBuilderFactory
            ->createListViewBuilder(
                name: static::LIST_VIEW,
                path: '/lexicon/:locale',
            )
            ->setResourceKey(Definition::RESOURCE_KEY)
            ->setListKey(static::LIST_KEY)
            ->setTitle('app.admin.lexicon')
            ->addListAdapters(['table'])
            ->addLocales($locales)
            ->setDefaultLocale($locales[0])
            ->setAddView(static::ADD_FORM_VIEW)
            ->setEditView(static::EDIT_FORM_VIEW)
            ->addToolbarActions($listToolbarActions);
        $viewCollection->add($listView);
    }

    /** @param string[] $locales */
    private function configureAddView(ViewCollection $viewCollection, array $locales): void
    {
        $addFormView = $this->viewBuilderFactory
            ->createResourceTabViewBuilder(
                name: static::ADD_FORM_VIEW,
                path: '/lexicon/:locale/add',
            )
            ->setResourceKey(Definition::RESOURCE_KEY)
            ->setBackView(static::LIST_VIEW)
            ->addLocales($locales);
        $viewCollection->add($addFormView);

        /** @var ToolbarAction[] */
        $formToolbarActions = [];

        if ($this->securityChecker->hasPermission(static::SECURITY_CONTEXT, PermissionTypes::ADD)) {
            $formToolbarActions[] = new ToolbarAction('sulu_admin.save');
        }

        $addDetailsFormView = $this->viewBuilderFactory
            ->createFormViewBuilder(
                name: static::ADD_FORM_VIEW . '.details',
                path: '/details',
            )
            ->setResourceKey(Definition::RESOURCE_KEY)
            ->setFormKey(static::FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->setEditView(static::EDIT_FORM_VIEW)
            ->addToolbarActions($formToolbarActions)
            ->setParent(static::ADD_FORM_VIEW);
        $viewCollection->add($addDetailsFormView);
    }

    /** @param string[] $locales */
    private function configureEditView(ViewCollection $viewCollection, array $locales): void
    {
        $editFormView = $this->viewBuilderFactory
            ->createResourceTabViewBuilder(
                name: static::EDIT_FORM_VIEW,
                path: '/lexicon/:locale/:id',
            )
            ->setResourceKey(Definition::RESOURCE_KEY)
            ->setBackView(static::LIST_VIEW)
            ->setTitleProperty('title')
            ->addLocales($locales);
        $viewCollection->add($editFormView);

        /** @var ToolbarAction[] */
        $formToolbarActions = [];

        if ($this->securityChecker->hasPermission(static::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $formToolbarActions[] = new ToolbarAction('sulu_admin.save');
        }
        if ($this->securityChecker->hasPermission(static::SECURITY_CONTEXT, PermissionTypes::DELETE)) {
            $formToolbarActions[] = new ToolbarAction('sulu_admin.delete');
        }

        $editDetailsFormView = $this->viewBuilderFactory
            ->createFormViewBuilder(
                name: static::EDIT_FORM_VIEW . '.details',
                path: '/details',
            )
            ->setResourceKey(Definition::RESOURCE_KEY)
            ->setFormKey(static::FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->addToolbarActions($formToolbarActions)
            ->setParent(static::EDIT_FORM_VIEW);
        $viewCollection->add($editDetailsFormView);

        if ($this->activityViewBuilderFactory->hasActivityListPermission()) {
            $viewCollection->add(
                $this->activityViewBuilderFactory
                    ->createActivityListViewBuilder(
                        name: static::EDIT_FORM_VIEW . '.activity',
                        path: '/activity',
                        resourceKey: Definition::RESOURCE_KEY,
                    )
                    ->setParent(static::EDIT_FORM_VIEW),
            );
        }
    }

    public function getSecurityContexts()
    {
        return [
            self::SULU_ADMIN_SECURITY_SYSTEM => [
                'Definitions' => [
                    self::SECURITY_CONTEXT => [
                        PermissionTypes::VIEW,
                        PermissionTypes::ADD,
                        PermissionTypes::EDIT,
                        PermissionTypes::DELETE,
                    ],
                ],
            ],
        ];
    }
}
