<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Definition;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

class DefinitionAdmin extends Admin
{
    final public const DEFINITION_LIST_KEY = 'definitions';

    final public const DEFINITION_FORM_KEY = 'definition_details';

    final public const DEFINITION_LIST_VIEW = 'app.definitions_list';
    final public const DEFINITION_ADD_FORM_VIEW = 'app.definition_add_form';
    final public const DEFINITION_EDIT_FORM_VIEW = 'app.definition_edit_form';

    public function __construct(
        private readonly ViewBuilderFactoryInterface $viewBuilderFactory,
        private readonly WebspaceManagerInterface $webspaceManager,
    ) {
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        // Configure a NavigationItem with a View
        $definitions = new NavigationItem('app.definitions');
        $definitions->setPosition(40);
        $definitions->setIcon('fa-book');
        $definitions->setView(static::DEFINITION_LIST_VIEW);
        $navigationItemCollection->add($definitions);
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $locales = $this->webspaceManager->getAllLocales();

        // Configure Definition List View
        $listToolbarActions = [
            new ToolbarAction('sulu_admin.add'),
            new ToolbarAction('sulu_admin.delete'),
        ];
        $listView = $this->viewBuilderFactory->createListViewBuilder(static::DEFINITION_LIST_VIEW, '/definitions/:locale')
            ->setResourceKey(Definition::RESOURCE_KEY)
            ->setListKey(static::DEFINITION_LIST_KEY)
            ->setTitle('app.definitions')
            ->addListAdapters(['table'])
            ->addLocales($locales)
            ->setDefaultLocale($locales[0])
            ->setAddView(static::DEFINITION_ADD_FORM_VIEW)
            ->setEditView(static::DEFINITION_EDIT_FORM_VIEW)
            ->addToolbarActions($listToolbarActions);
        $viewCollection->add($listView);

        // Configure Definition Add View
        $addFormView = $this->viewBuilderFactory->createResourceTabViewBuilder(static::DEFINITION_ADD_FORM_VIEW, '/definitions/:locale/add')
            ->setResourceKey(Definition::RESOURCE_KEY)
            ->setBackView(static::DEFINITION_LIST_VIEW)
            ->addLocales($locales);
        $viewCollection->add($addFormView);

        $addDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder(static::DEFINITION_ADD_FORM_VIEW . '.details', '/details')
            ->setResourceKey(Definition::RESOURCE_KEY)
            ->setFormKey(static::DEFINITION_FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->setEditView(static::DEFINITION_EDIT_FORM_VIEW)
            ->addToolbarActions([new ToolbarAction('sulu_admin.save')])
            ->setParent(static::DEFINITION_ADD_FORM_VIEW);
        $viewCollection->add($addDetailsFormView);

        // Configure Definition Edit View
        $editFormView = $this->viewBuilderFactory->createResourceTabViewBuilder(static::DEFINITION_EDIT_FORM_VIEW, '/definitions/:locale/:id')
            ->setResourceKey(Definition::RESOURCE_KEY)
            ->setBackView(static::DEFINITION_LIST_VIEW)
            ->setTitleProperty('title')
            ->addLocales($locales);
        $viewCollection->add($editFormView);

        $formToolbarActions = [
            new ToolbarAction('sulu_admin.save'),
            new ToolbarAction('sulu_admin.delete'),
        ];
        $editDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder(static::DEFINITION_EDIT_FORM_VIEW . '.details', '/details')
            ->setResourceKey(Definition::RESOURCE_KEY)
            ->setFormKey(static::DEFINITION_FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->addToolbarActions($formToolbarActions)
            ->setParent(static::DEFINITION_EDIT_FORM_VIEW);
        $viewCollection->add($editDetailsFormView);
    }
}
