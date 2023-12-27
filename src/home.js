import {
    activateCheckall,
    activateCloseSubmenus,
    activateSelectAll,
    initWorkspaceForm,
} from './fn/fn';

window.addEventListener('load', () => {
    activateCheckall('pagesid[]', 'checkall');
    activateSelectAll();
    activateCloseSubmenus();
    initWorkspaceForm();
});
