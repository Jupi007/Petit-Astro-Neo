// Add project specific javascript code and import of additional bundles here:
import { formToolbarActionRegistry, listItemActionRegistry } from 'sulu-admin-bundle/views';
import NotifyFormToolbarAction from "./formToolbarActions/NotifyFormToolbarAction";
import PublicationTypoOverlayAction from "./listItemActions/PublicationTypoOverlayAction";

listItemActionRegistry.add('app.publication_typo_overlay', PublicationTypoOverlayAction);
formToolbarActionRegistry.add('app.notify', NotifyFormToolbarAction);
