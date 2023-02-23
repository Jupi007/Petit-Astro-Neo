// Add project specific javascript code and import of additional bundles here:
import { listItemActionRegistry } from 'sulu-admin-bundle/views';
import PublicationTypoOverlayAction from "./listItemActions/PublicationTypoOverlayAction";

listItemActionRegistry.add('app.publication_typo_overlay', PublicationTypoOverlayAction);
