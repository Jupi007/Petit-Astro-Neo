import { action, observable } from 'mobx';
import React from 'react';
import Overlay from 'sulu-admin-bundle/components/Overlay';
import { translate } from 'sulu-admin-bundle/utils';
import { AbstractListItemAction } from 'sulu-admin-bundle/views';

export default class ContactRequestOverlayAction extends AbstractListItemAction {
  @observable showOverlay = false;
  @observable object = undefined;
  @observable email = undefined;
  @observable message = undefined;

  getItemActionConfig(item) {
    return {
      icon: 'su-eye',
      onClick: item ? action(() => {
        this.object = item.object;
        this.email = item.email;
        this.message = item.message;
        console.log(item);
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

  handleConfirmAction = () => {
    window.location.assign('mailto:' + this.email + '?subject=Re: ' + this.object);
  };

  getNode() {
    return (
      <Overlay
        actions={[{
          onClick: () => this.handleHideOverlay(),
          title: translate('sulu_admin.close'),
        }]}
        onClose={action(() => this.handleHideOverlay())}
        onConfirm={action(() => this.handleConfirmAction())}
        open={this.showOverlay}
        size="small"
        key="app.contact_request_overlay"
        title={this.object}
        confirmText={translate('app.admin.answer')}
      >
        <div style={{ padding: '1rem', whiteSpace: 'pre-wrap', lineHeight: '1.5' }} >
          {this.message}
        </div>
      </Overlay>
    );
  }
}
