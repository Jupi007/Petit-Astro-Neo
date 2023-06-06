<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\Admin;

use App\Domain\Entity\NewsletterRegistration;
use Sulu\Bundle\ActivityBundle\Infrastructure\Sulu\Admin\View\ActivityViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;

class NewsletterRegistrationAdmin extends Admin
{
    final public const SECURITY_CONTEXT = 'app.settings.newsletter_registrations';

    final public const NAVIGATION_ITEM = 'app.admin.newsletter';

    final public const LIST_KEY = 'newsletter_registrations';
    final public const FORM_KEY = 'newsletter_registration_details';

    final public const LIST_VIEW = 'app.newsletter_registrations_list';
    final public const ADD_FORM_VIEW = 'app.newsletter_registration_add_form';
    final public const EDIT_FORM_VIEW = 'app.newsletter_registration_edit_form';

    public function __construct(
        private readonly ViewBuilderFactoryInterface $viewBuilderFactory,
        private readonly ActivityViewBuilderFactoryInterface $activityViewBuilderFactory,
        private readonly SecurityCheckerInterface $securityChecker,
    ) {
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        if ($this->securityChecker->hasPermission(static::SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            $item = new NavigationItem(static::NAVIGATION_ITEM);
            $item->setPosition(40);
            $item->setIcon(NewsletterRegistration::RESOURCE_ICON);
            $item->setView(static::LIST_VIEW);

            $navigationItemCollection->add($item);
        }
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $this->configureListView($viewCollection);
        $this->configureAddView($viewCollection);
        $this->configureEditView($viewCollection);
    }

    private function configureListView(ViewCollection $viewCollection): void
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
                path: '/newsletter',
            )
            ->setResourceKey(NewsletterRegistration::RESOURCE_KEY)
            ->setListKey(static::LIST_KEY)
            ->setTitle('app.admin.newsletter_registrations')
            ->addListAdapters(['table'])
            ->setAddView(static::ADD_FORM_VIEW)
            ->setEditView(static::EDIT_FORM_VIEW)
            ->addToolbarActions($listToolbarActions);
        $viewCollection->add($listView);
    }

    private function configureAddView(ViewCollection $viewCollection): void
    {
        $addFormView = $this->viewBuilderFactory
            ->createResourceTabViewBuilder(
                name: static::ADD_FORM_VIEW,
                path: '/newsletter/add',
            )
            ->setResourceKey(NewsletterRegistration::RESOURCE_KEY)
            ->setBackView(static::LIST_VIEW);
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
            ->setResourceKey(NewsletterRegistration::RESOURCE_KEY)
            ->setFormKey(static::FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->setEditView(static::EDIT_FORM_VIEW)
            ->addToolbarActions($formToolbarActions)
            ->setParent(static::ADD_FORM_VIEW);
        $viewCollection->add($addDetailsFormView);
    }

    private function configureEditView(ViewCollection $viewCollection): void
    {
        $formView = $this->viewBuilderFactory
            ->createResourceTabViewBuilder(
                name: static::EDIT_FORM_VIEW,
                path: '/newsletter/:id',
            )
            ->setResourceKey(NewsletterRegistration::RESOURCE_KEY)
            ->setBackView(static::LIST_VIEW)
            ->setTitleProperty('email');
        $viewCollection->add($formView);

        /** @var ToolbarAction[] */
        $formToolbarActions = [];

        if ($this->securityChecker->hasPermission(static::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $formToolbarActions[] = new ToolbarAction('sulu_admin.save');
        }
        if ($this->securityChecker->hasPermission(static::SECURITY_CONTEXT, PermissionTypes::DELETE)) {
            $formToolbarActions[] = new ToolbarAction('sulu_admin.delete');
        }

        $detailsFormView = $this->viewBuilderFactory
            ->createFormViewBuilder(
                name: static::EDIT_FORM_VIEW . '.details',
                path: '/details',
            )
            ->setResourceKey(NewsletterRegistration::RESOURCE_KEY)
            ->setFormKey(static::FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->addToolbarActions($formToolbarActions)
            ->setParent(static::EDIT_FORM_VIEW);
        $viewCollection->add($detailsFormView);

        if ($this->activityViewBuilderFactory->hasActivityListPermission()) {
            $viewCollection->add(
                $this->activityViewBuilderFactory
                    ->createActivityListViewBuilder(
                        name: static::EDIT_FORM_VIEW . '.activity',
                        path: '/activity',
                        resourceKey: NewsletterRegistration::RESOURCE_KEY,
                    )
                    ->setParent(static::EDIT_FORM_VIEW),
            );
        }
    }

    public function getSecurityContexts()
    {
        return [
            self::SULU_ADMIN_SECURITY_SYSTEM => [
                'NewsletterRegistrations' => [
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
