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
    final public const LIST_KEY = 'definitions';

    final public const FORM_KEY = 'definition_details';

    final public const LIST_VIEW = 'app.definitions_list';
    final public const ADD_FORM_VIEW = 'app.definition_add_form';
    final public const EDIT_FORM_VIEW = 'app.definition_edit_form';

    public function __construct(
        private readonly ViewBuilderFactoryInterface $viewBuilderFactory,
        private readonly WebspaceManagerInterface $webspaceManager,
    ) {
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        // Configure a NavigationItem with a View
        $definitions = new NavigationItem('app.lexicon');
        $definitions->setPosition(40);
        $definitions->setIcon('fa-book');
        $definitions->setView(static::LIST_VIEW);
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
        $listView = $this->viewBuilderFactory->createListViewBuilder(static::LIST_VIEW, '/lexicon/:locale')
            ->setResourceKey(Definition::RESOURCE_KEY)
            ->setListKey(static::LIST_KEY)
            ->setTitle('app.lexicon')
            ->addListAdapters(['table'])
            ->addLocales($locales)
            ->setDefaultLocale($locales[0])
            ->setAddView(static::ADD_FORM_VIEW)
            ->setEditView(static::EDIT_FORM_VIEW)
            ->addToolbarActions($listToolbarActions);
        $viewCollection->add($listView);

        // Configure Definition Add View
        $addFormView = $this->viewBuilderFactory->createResourceTabViewBuilder(static::ADD_FORM_VIEW, '/lexicon/:locale/add')
            ->setResourceKey(Definition::RESOURCE_KEY)
            ->setBackView(static::LIST_VIEW)
            ->addLocales($locales);
        $viewCollection->add($addFormView);

        $addDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder(static::ADD_FORM_VIEW . '.details', '/details')
            ->setResourceKey(Definition::RESOURCE_KEY)
            ->setFormKey(static::FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->setEditView(static::EDIT_FORM_VIEW)
            ->addToolbarActions([new ToolbarAction('sulu_admin.save')])
            ->setParent(static::ADD_FORM_VIEW);
        $viewCollection->add($addDetailsFormView);

        // Configure Definition Edit View
        $editFormView = $this->viewBuilderFactory->createResourceTabViewBuilder(static::EDIT_FORM_VIEW, '/lexicon/:locale/:id')
            ->setResourceKey(Definition::RESOURCE_KEY)
            ->setBackView(static::LIST_VIEW)
            ->setTitleProperty('title')
            ->addLocales($locales);
        $viewCollection->add($editFormView);

        $formToolbarActions = [
            new ToolbarAction('sulu_admin.save'),
            new ToolbarAction('sulu_admin.delete'),
        ];
        $editDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder(static::EDIT_FORM_VIEW . '.details', '/details')
            ->setResourceKey(Definition::RESOURCE_KEY)
            ->setFormKey(static::FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->addToolbarActions($formToolbarActions)
            ->setParent(static::EDIT_FORM_VIEW);
        $viewCollection->add($editDetailsFormView);
    }
}
