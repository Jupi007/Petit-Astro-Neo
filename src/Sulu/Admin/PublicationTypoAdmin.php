<?php

declare(strict_types=1);

namespace App\Sulu\Admin;

use App\Entity\PublicationTypo;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ListItemAction;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Component\Localization\Manager\LocalizationManagerInterface;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;

class PublicationTypoAdmin extends Admin
{
    final public const SECURITY_CONTEXT = 'app.settings.publication_typos';

    final public const NAVIGATION_ITEM = 'app.admin.publication_typos';

    final public const LIST_KEY = 'publication_typos';
    final public const LIST_KEY_PUBLICATION = 'publication_typos_publication';

    final public const LIST_VIEW = 'app.publication_typos_list';
    final public const LIST_VIEW_PUBLICATION = 'app.publication_typos_list.publication';

    public function __construct(
        private readonly ViewBuilderFactoryInterface $viewBuilderFactory,
        private readonly SecurityCheckerInterface $securityChecker,
        private readonly LocalizationManagerInterface $localizationManager,
    ) {
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        if ($this->securityChecker->hasPermission(self::SECURITY_CONTEXT, PermissionTypes::VIEW)) {
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
        $locales = $this->localizationManager->getLocales();

        if ($this->securityChecker->hasPermission(self::SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            /** @var ToolbarAction[] */
            $toolbarActions = [];

            if ($this->securityChecker->hasPermission(self::SECURITY_CONTEXT, PermissionTypes::DELETE)) {
                $toolbarActions[] = new ToolbarAction('sulu_admin.delete');
            }
            $toolbarActions[] = new ToolbarAction('sulu_admin.export');

            $listItemActions = [
                new ListItemAction('app.publication_typo_overlay'),
            ];

            $viewCollection->add(
                $this->viewBuilderFactory->createListViewBuilder(
                    static::LIST_VIEW,
                    '/' . PublicationTypo::RESOURCE_KEY . '/:locale',
                )
                ->setResourceKey(PublicationTypo::RESOURCE_KEY)
                ->setListKey(static::LIST_KEY)
                ->addListAdapters(['table'])
                ->setTitle('app.admin.publication_typos')
                ->addLocales($locales)
                ->setDefaultLocale($locales[0])
                ->addToolbarActions($toolbarActions)
                ->addItemActions($listItemActions),
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createListViewBuilder(
                    static::LIST_VIEW_PUBLICATION,
                    '/' . PublicationTypo::RESOURCE_KEY,
                )
                ->setResourceKey(PublicationTypo::RESOURCE_KEY)
                ->setListKey(static::LIST_KEY_PUBLICATION)
                ->addListAdapters(['table'])
                ->setTabTitle('app.admin.publication_typos')
                ->setTabOrder(60)
                ->addToolbarActions($toolbarActions)
                ->addItemActions($listItemActions)
                ->addRouterAttributesToListRequest(['id' => 'publicationId'])
                ->setParent(PublicationAdmin::EDIT_FORM_VIEW),
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
                'Publications' => [
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
