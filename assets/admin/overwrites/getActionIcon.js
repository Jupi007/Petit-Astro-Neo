export default function getActionIcon(action: string) {
  switch (action) {
    case 'view':
      return 'su-eye';
    case 'add':
      return 'su-plus-circle';
    case 'edit':
      return 'su-pen';
    case 'delete':
      return 'su-trash-alt';
    case 'security':
      return 'su-lock';
    case 'live':
      return 'su-publish';
    case 'notify':
      return 'su-bell';
    default:
      throw new Error('No icon defined for "' + action + '"');
  }
}
