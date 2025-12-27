import { activateCheckall, activateCloseSubmenus } from './fn/fn';

window.addEventListener('load', () => {
    activateCheckall('id[]', 'checkall');
    activateCloseSubmenus();
});
