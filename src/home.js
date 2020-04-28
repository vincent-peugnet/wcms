import {
    activateCheckall,
    activateCloseSubmenus,
    activateSelectAll,
} from './fn/fn';

window.addEventListener('load', () => {
    activateCheckall('pagesid[]', 'checkall');
    activateSelectAll();
    activateCloseSubmenus();
});
