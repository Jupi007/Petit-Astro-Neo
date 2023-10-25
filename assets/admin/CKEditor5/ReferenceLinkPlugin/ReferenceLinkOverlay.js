// @flow
import { action, observable } from 'mobx';
import { observer } from 'mobx-react';
import React from 'react';
import Dialog from 'sulu-admin-bundle/components/Dialog';
import Form from 'sulu-admin-bundle/components/Form';
import Url from 'sulu-admin-bundle/components/Url';
import type { LinkTypeOverlayProps } from 'sulu-admin-bundle/containers/Link/types';
import { translate } from 'sulu-admin-bundle/utils';

@observer
class ReferenceLinkOverlay extends React.Component<LinkTypeOverlayProps> {
    @observable href: ?string = undefined;

    constructor(props: LinkTypeOverlayProps) {
        super(props);

        this.updateUrl();
    }

    @action componentDidUpdate(prevProps: LinkTypeOverlayProps) {
        if (prevProps.open === false && this.props.open === true) {
            this.updateUrl();
        }
    }

    updateUrl() {
        if (!this.props.href) {
            this.href = undefined;

            return;
        }

        this.href = String(this.props.href);
    }

    callUrlChange = () => {
        if (!this.href) {
            this.props.onHrefChange(undefined);

            return;
        }

        this.props.onHrefChange(this.href);
    };

    handleUrlBlur = this.callUrlChange;

    @action handleHrefChange = (href: ?string) => {
        this.href = href;
    };

    render() {
        return (
            <Dialog
                cancelText={translate('sulu_admin.cancel')}
                confirmDisabled={!this.props.href}
                confirmText={translate('sulu_admin.confirm')}
                onCancel={this.props.onCancel}
                onConfirm={this.props.onConfirm}
                open={this.props.open}
                title={translate('sulu_admin.link')}
            >
                <Form>
                    <Form.Field label={translate('sulu_admin.link_url')} required={true}>
                        <Url
                            protocols={['http://', 'https://',]}
                            defaultProtocol="https://"
                            onBlur={this.handleUrlBlur}
                            onChange={this.handleHrefChange}
                            onProtocolChange={this.handleProtocolChange}
                            valid={true}
                            value={this.href}
                        />
                    </Form.Field>
                </Form>
            </Dialog>
        );
    }
}

export default ReferenceLinkOverlay;
