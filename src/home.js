import {
    activateCheckall,
    activateCloseSubmenus,
    initWorkspaceForm,
} from './fn/fn';

window.addEventListener('load', () => {
    activateCheckall('pagesid[]', 'checkall');
    activateCloseSubmenus();
    initWorkspaceForm();
});
