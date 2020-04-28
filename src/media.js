import { checkallHandler, closeSubmenus, activateSelectAll } from './fn/fn';

window.addEventListener('load', () => {
    let checkboxes = document.getElementsByName('id[]');
    let checkall = document.getElementById('checkall');
    let checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.addEventListener('input', checkallHandler.bind({ checkboxes }));
    checkall.innerHTML = '';
    checkall.appendChild(checkbox);

    activateSelectAll();
});

window.addEventListener('click', closeSubmenus);
