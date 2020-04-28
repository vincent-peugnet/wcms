import {
    activateCheckall,
    activateCloseSubmenus,
    activateSelectAll,
} from './fn/fn';

window.addEventListener('load', () => {
    activateCheckall('id[]', 'checkall');
    activateSelectAll();
    activateCloseSubmenus();
});
