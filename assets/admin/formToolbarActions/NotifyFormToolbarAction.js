// @flow
import jexl from 'jexl';
import { action, observable } from 'mobx';
import React from 'react';
import Dialog from 'sulu-admin-bundle/components/Dialog';
import ResourceRequester from 'sulu-admin-bundle/services/ResourceRequester';
import { translate } from 'sulu-admin-bundle/utils';
import { AbstractFormToolbarAction } from 'sulu-admin-bundle/views';

export default class NotifyFormToolbarAction extends AbstractFormToolbarAction {
  @observable showNotifyDialog = false;
  @observable notifying = false;

  getNode() {
    const {
      resourceFormStore: {
        id,
      },
    } = this;

    if (!id) {
      return null;
    }

    return (
      <Dialog
        cancelText={translate('sulu_admin.cancel')}
        confirmLoading={this.notifying}
        confirmText={translate('sulu_admin.yes')}
        onCancel={this.handleNotifyDialogClose}
        onConfirm={this.handleNotifyDialogConfirm}
        open={this.showNotifyDialog}
        title={translate('app.admin.notify_warning_title')}
      >
        {translate('app.admin.notify_warning_text')}
      </Dialog>
    );
  }

  getToolbarItemConfig() {
    const {
      visible_condition: visibleCondition,
    } = this.options;

    const { id, data } = this.resourceFormStore;
    const { notified } = data;

    const visibleConditionFulfilled = !visibleCondition || jexl.evalSync(visibleCondition, this.conditionData);

    if (visibleConditionFulfilled) {
      return {
        disabled: !id || notified,
        icon: 'su-bell',
        label: translate('app.admin.notify'),
        onClick: action(() => {
          this.showNotifyDialog = true;
        }),
        type: 'button',
      };
    }
  }

  @action handleNotifyDialogConfirm = () => {
    const {
      id,
      locale,
      options: {
        webspace,
      },
      resourceKey,
    } = this.resourceFormStore;

    if (!id) {
      throw new Error(
        'The page can only be notified if an ID is given! This should not happen and is likely a bug.'
      );
    }

    this.notifying = true;

    ResourceRequester.post(
      resourceKey,
      undefined,
      {
        action: 'notify',
        locale,
        id,
        webspace,
      }
    ).then(action((response) => {
      this.notifying = false;
      this.showNotifyDialog = false;
      this.form.showSuccessSnackbar();
      this.resourceFormStore.changeMultiple(response, { isServerValue: true });
      this.resourceFormStore.dirty = false;
      console.log(this.resourceFormStore);
    }));
  };

  @action handleNotifyDialogClose = () => {
    this.showNotifyDialog = false;
  };
}
