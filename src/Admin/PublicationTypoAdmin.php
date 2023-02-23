<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\PublicationTypo;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ListItemAction;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;

class PublicationTypoAdmin extends Admin
{
    final public const NAVIGATION_ITEM = 'app.admin.publication_typos';
    final public const LIST_KEY = 'publication_typos';
    final public const LIST_VIEW = 'app.publication_typos_list';
    final public const LIST_VIEW_PUBLICATION = 'app.publication_typos_list.publication';

    public function __construct(
        private readonly ViewBuilderFactoryInterface $viewBuilderFactory,
        private readonly SecurityCheckerInterface $securityChecker,
    ) {
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        if ($this->securityChecker->hasPermission(PublicationAdmin::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $navigationParent = $navigationItemCollection->get(PublicationAdmin::NAVIGATION_ITEM);

            $navigationItem = new NavigationItem(static::NAVIGATION_ITEM);
            $navigationItem->setPosition(20);
            $navigationItem->setIcon(PublicationTypo::RESOURCE_ICON);
            $navigationItem->setView(static::LIST_VIEW);

            $navigationParent->addChild($navigationItem);
        }
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        if ($this->securityChecker->hasPermission(PublicationAdmin::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $viewCollection->add(
                $this->viewBuilderFactory->createListViewBuilder(
                    static::LIST_VIEW,
                    '/' . PublicationTypo::RESOURCE_KEY,
                )
                ->setResourceKey(PublicationTypo::RESOURCE_KEY)
                ->setListKey(static::LIST_KEY)
                ->setTitle('app.admin.publication_typos')
                ->addListAdapters(['table'])
                ->addToolbarActions([
                    new ToolbarAction('sulu_admin.delete'),
                    new ToolbarAction('sulu_admin.export'),
                ])
                ->addItemActions([
                    new ListItemAction('app.publication_typo_overlay'),
                ]),
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createListViewBuilder(
                    static::LIST_VIEW_PUBLICATION,
                    '/' . PublicationTypo::RESOURCE_KEY,
                )
                ->setResourceKey(PublicationTypo::RESOURCE_KEY)
                ->setListKey(static::LIST_KEY)
                ->setTabTitle('app.admin.publication_typos')
                ->setTabOrder(60)
                ->addListAdapters(['table'])
                ->addToolbarActions([
                    new ToolbarAction('sulu_admin.delete'),
                    new ToolbarAction('sulu_admin.export'),
                ])
                ->addItemActions([
                    new ListItemAction('app.publication_typo_overlay'),
                ])
                ->addRouterAttributesToListRequest(['id' => 'publicationId'])
                ->setParent(PublicationAdmin::EDIT_FORM_VIEW),
            );
        }
    }
}
