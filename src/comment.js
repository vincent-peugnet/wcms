import {
    activateCheckall,
    activateCloseSubmenus,
    initWorkspaceForm,
} from './fn/fn';

window.addEventListener('load', () => {
    activateCheckall('id[]', 'checkall');
    activateCloseSubmenus();
    initWorkspaceForm();
});
