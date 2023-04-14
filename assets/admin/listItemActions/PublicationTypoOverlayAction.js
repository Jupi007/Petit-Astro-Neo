import { action, observable } from 'mobx';
import React from 'react';
import Overlay from 'sulu-admin-bundle/components/Overlay';
import { translate } from 'sulu-admin-bundle/utils';
import { AbstractListItemAction } from 'sulu-admin-bundle/views';

export default class PublicationTypoOverlayAction extends AbstractListItemAction {
  @observable showOverlay = false;
  @observable description = undefined;

  getItemActionConfig(item) {
    return {
      icon: 'su-eye',
      onClick: item ? action(() => {
        this.description = item.description;
        this.handleShowOverlay();
      }) : undefined,
    };
  }

  @action handleShowOverlay = () => {
    this.showOverlay = true;
  };

  @action handleHideOverlay = () => {
    this.showOverlay = false;
  };

  getNode() {
    return (
      <Overlay
        onClose={action(() => this.handleHideOverlay())}
        onConfirm={action(() => this.handleHideOverlay())}
        open={this.showOverlay}
        size="small"
        key="app.publication_typo_overlay"
        title={translate('app.admin.publication_typo')}
        confirmText={translate('sulu_admin.close')}
      >
        <div style={{ padding: '1rem', whiteSpace: 'pre-wrap', lineHeight: '1.5' }} >
          {this.description}
        </div>
      </Overlay>
    );
  }
}
