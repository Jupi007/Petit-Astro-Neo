<?php

declare(strict_types=1);

namespace App\Sulu\Admin;

use App\Entity\ContactRequest;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ListItemAction;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;

class ContactRequestAdmin extends Admin
{
    final public const SECURITY_CONTEXT = 'app.settings.contact_requests';

    final public const NAVIGATION_ITEM = 'app.admin.contact_requests';

    final public const LIST_KEY = 'contact_requests';
    final public const LIST_VIEW = 'app.contact_requests_list';

    public function __construct(
        private readonly ViewBuilderFactoryInterface $viewBuilderFactory,
        private readonly SecurityCheckerInterface $securityChecker,
    ) {
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        if ($this->securityChecker->hasPermission(self::SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            $navigationItem = new NavigationItem(static::NAVIGATION_ITEM);
            $navigationItem->setPosition(20);
            $navigationItem->setIcon(ContactRequest::RESOURCE_ICON);
            $navigationItem->setView(static::LIST_VIEW);

            $navigationItemCollection->add($navigationItem);
        }
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        if ($this->securityChecker->hasPermission(self::SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            /** @var ToolbarAction[] */
            $toolbarActions = [];

            if ($this->securityChecker->hasPermission(self::SECURITY_CONTEXT, PermissionTypes::DELETE)) {
                $toolbarActions[] = new ToolbarAction('sulu_admin.delete');
            }
            $toolbarActions[] = new ToolbarAction('sulu_admin.export');

            $listItemActions = [
                new ListItemAction('app.contact_request_overlay'),
            ];

            $viewCollection->add(
                $this->viewBuilderFactory->createListViewBuilder(
                    static::LIST_VIEW,
                    '/' . ContactRequest::RESOURCE_KEY,
                )
                ->setResourceKey(ContactRequest::RESOURCE_KEY)
                ->setListKey(static::LIST_KEY)
                ->addListAdapters(['table'])
                ->setTitle('app.admin.contact_requests')
                ->addToolbarActions($toolbarActions)
                ->addItemActions($listItemActions),
            );
        }
    }

    /**
     * @return mixed[]
     */
    public function getSecurityContexts()
    {
        return [
            self::SULU_ADMIN_SECURITY_SYSTEM => [
                'ContactRequests' => [
                    static::SECURITY_CONTEXT => [
                        PermissionTypes::VIEW,
                        PermissionTypes::ADD,
                        PermissionTypes::DELETE,
                    ],
                ],
            ],
        ];
    }
}
