import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import ClickObserver from '@ckeditor/ckeditor5-engine/src/view/observer/clickobserver';
import ButtonView from '@ckeditor/ckeditor5-ui/src/button/buttonview';
import ContextualBalloon from '@ckeditor/ckeditor5-ui/src/panel/balloon/contextualballoon';
import { action, observable } from 'mobx';
import { Observer } from 'mobx-react';
import React from 'react';
import { render, unmountComponentAtNode } from 'react-dom';
import LinkBalloonView from 'sulu-admin-bundle/containers/CKEditor5/LinkBalloonView';
import LinkCommand from 'sulu-admin-bundle/containers/CKEditor5/LinkCommand';
import UnlinkCommand from 'sulu-admin-bundle/containers/CKEditor5/UnlinkCommand';
import { addLinkConversion, findModelItemInSelection, findViewLinkItemInSelection } from 'sulu-admin-bundle/containers/CKEditor5/utils';
import { translate } from 'sulu-admin-bundle/utils';
import ReferenceLinkOverlay from './ReferenceLinkOverlay';

import linkIcon from '!!raw-loader!./book.svg';

const LINK_EVENT_URL = 'url';
const LINK_HREF_ATTRIBUTE = 'referenceLinkHref';
const LINK_TAG = 'sulu-reference';

export default class ReferenceLinkPlugin extends Plugin {
    @observable open: boolean = false;
    @observable url: ?string;
    balloon: typeof ContextualBalloon;

    init() {
        this.referenceLinkOverlayElement = document.createElement('div');
        this.editor.sourceElement.appendChild(this.referenceLinkOverlayElement);
        this.balloon = this.editor.plugins.get(ContextualBalloon);
        this.balloonView = new LinkBalloonView(this.editor.locale, true);
        this.balloonView.bind('href').to(this, 'href');

        this.listenTo(this.balloonView, 'unlink', () => {
            this.editor.execute('referenceUnlink');
            this.hideBalloon();
        });

        this.listenTo(this.balloonView, 'link', action(() => {
            this.selection = this.editor.model.document.selection;
            const node = findModelItemInSelection(this.editor);

            this.url = node.getAttribute(LINK_HREF_ATTRIBUTE);
            this.open = true;

            this.hideBalloon();
        }));

        const locale = this.editor.config.get('sulu.locale');

        render(
            (
                <Observer>
                    {() => (
                        <ReferenceLinkOverlay
                            href={this.url}
                            locale={locale}
                            onCancel={this.handleOverlayClose}
                            onConfirm={this.handleOverlayConfirm}
                            onHrefChange={this.handleHrefChange}
                            open={this.open}
                            options={undefined}
                        />
                    )
                    }
                </Observer>
            ),
            this.referenceLinkOverlayElement
        );

        this.editor.commands.add(
            'referenceLink',
            new LinkCommand(
                this.editor,
                {
                    [LINK_HREF_ATTRIBUTE]: LINK_EVENT_URL,
                },
                LINK_EVENT_URL
            )
        );
        this.editor.commands.add(
            'referenceUnlink',
            new UnlinkCommand(
                this.editor,
                [LINK_HREF_ATTRIBUTE]
            )
        );

        this.editor.ui.componentFactory.add('referenceLink', (locale) => {
            const button = new ButtonView(locale);

            button.bind('isEnabled').to(
                this.editor.commands.get('internalLink'),
                'buttonEnabled',
                this.editor.commands.get('referenceLink'),
                'buttonEnabled',
                (internalLinkEnabled, referenceLinkEnabled) => internalLinkEnabled && referenceLinkEnabled
            );

            button.set({
                icon: linkIcon,
                label: translate('sulu_admin.reference_link'),
                tooltip: true,
            });

            button.on('execute', action(() => {
                this.selection = this.editor.model.document.selection;
                this.open = true;
                this.target = DEFAULT_TARGET;
                this.title = undefined;
                this.url = undefined;
                this.rel = undefined;
            }));

            return button;
        });

        addLinkConversion(this.editor, LINK_TAG, LINK_HREF_ATTRIBUTE, 'href');

        const view = this.editor.editing.view;
        view.addObserver(ClickObserver);

        this.listenTo(view.document, 'click', () => {
            const referenceLink = findViewLinkItemInSelection(this.editor, LINK_TAG);

            this.hideBalloon();

            if (referenceLink) {
                this.set('href', referenceLink.getAttribute('href'));
                this.balloon.add({
                    position: {
                        target: view.domConverter.mapViewToDom(referenceLink),
                    },
                    view: this.balloonView,
                });
            }
        });

        this.listenTo(view.document, 'blur', () => {
            this.hideBalloon();
        });
    }

    hideBalloon() {
        if (this.balloon.hasView(this.balloonView)) {
            this.balloon.remove(this.balloonView);
        }
    }

    @action handleOverlayConfirm = () => {
        this.editor.execute(
            'referenceLink',
            {
                selection: this.selection,
                [LINK_EVENT_URL]: this.url,
            }
        );
        this.open = false;
    };

    @action handleOverlayClose = () => {
        this.open = false;
    };

    @action handleHrefChange = (href: ?string | number) => {
        this.url = String(href);
    };

    destroy() {
        unmountComponentAtNode(this.referenceLinkOverlayElement);
        this.referenceLinkOverlayElement.remove();
        this.referenceLinkOverlayElement = undefined;
    }
}
