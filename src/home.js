/** @type {HTMLInputElement[]} */
let checkboxes = [];

window.addEventListener('load', () => {
    checkboxes = document.getElementsByName('pagesid[]');
    let checkall = document.getElementById('checkall');
    let checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.addEventListener('input', checkallHandler);
    checkall.innerHTML = '';
    checkall.appendChild(checkbox);
});

window.addEventListener('click', clickHandler);

/**
 * Manage input event on the checkall checkbox.
 * @param {InputEvent} e the input event
 */
function checkallHandler(e) {
    if (e.target.checked) {
        for (const checkbox of checkboxes) {
            checkbox.checked = true;
        }
    } else {
        for (const checkbox of checkboxes) {
            checkbox.checked = false;
        }
    }
}

/**
 * Manage click event on the home page.
 * @param {MouseEvent} e
 */
function clickHandler(e) {
    let details = document.querySelectorAll('details');
    let currentDetail = e.target.closest('details');
    for (const detail of details) {
        if (!detail.isSameNode(currentDetail)) {
            detail.removeAttribute('open');
        }
    }
}
