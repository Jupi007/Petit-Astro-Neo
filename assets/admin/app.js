// Add project specific javascript code and import of additional bundles here:
import { ckeditorConfigRegistry, ckeditorPluginRegistry } from 'sulu-admin-bundle/containers';
import { formToolbarActionRegistry, listItemActionRegistry } from 'sulu-admin-bundle/views';
import ReferenceLinkPlugin from './CKEditor5/ReferenceLinkPlugin/ReferenceLinkPlugin';
import NotifyFormToolbarAction from "./formToolbarActions/NotifyFormToolbarAction";
import PublicationTypoOverlayAction from "./listItemActions/PublicationTypoOverlayAction";

listItemActionRegistry.add('app.publication_typo_overlay', PublicationTypoOverlayAction);
formToolbarActionRegistry.add('app.notify', NotifyFormToolbarAction);

ckeditorPluginRegistry.add(ReferenceLinkPlugin);
ckeditorConfigRegistry.add((config) => ({
  toolbar: [...config.toolbar, 'referenceLink'],
}));
