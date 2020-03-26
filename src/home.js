import { checkallHandler, closeSubmenus } from './fn/fn';

window.addEventListener('load', () => {
    let checkboxes = document.getElementsByName('pagesid[]');
    let checkall = document.getElementById('checkall');
    if (!checkall) {
        return;
    }
    let checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.addEventListener('input', checkallHandler.bind({ checkboxes }));
    checkall.innerHTML = '';
    checkall.appendChild(checkbox);
});

window.addEventListener('click', closeSubmenus);
