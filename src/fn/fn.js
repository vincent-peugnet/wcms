/**
 * Manage input event on the checkall checkbox.
 * Call with .bind({checkboxes: HTMLElement[]})
 * @param {InputEvent} e the input event
 */
function checkallHandler(e) {
    if (e.target.checked) {
        for (const checkbox of this.checkboxes) {
            checkbox.checked = true;
        }
    } else {
        for (const checkbox of this.checkboxes) {
            checkbox.checked = false;
        }
    }
}

/**
 * Activate the checkall feature
 * @param {string} checkboxesName value of the name property of the desired checkbox elements.
 * @param {string} checkallId value of the id property of the desired checkall element.
 */
export function activateCheckall(checkboxesName, checkallId) {
    let checkboxes = document.getElementsByName(checkboxesName);
    let checkall = document.getElementById(checkallId);
    if (!checkall) {
        return;
    }
    let checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.addEventListener('input', checkallHandler.bind({ checkboxes }));
    checkall.innerHTML = '';
    checkall.appendChild(checkbox);
}

/**
 * Close all submenus of the menubar.
 * @param {MouseEvent} e
 */
function closeSubmenus(e) {
    let details = document.querySelectorAll('aside details');
    let currentDetail = e.target.closest('details');
    for (const detail of details) {
        if (!detail.isSameNode(currentDetail)) {
            detail.removeAttribute('open');
        }
    }
}

/**
 * Activate "close submenus" feature on click anywhere.
 */
export function activateCloseSubmenus() {
    window.addEventListener('click', closeSubmenus);
}

/**
 * Select the whole content of the clicked item.
 * @param {MouseEvent} e
 */
function selectAll(e) {
    if (e.target instanceof HTMLInputElement) {
        e.target.select();
        e.target.focus();
    }
}

/**
 * Activate "select all" feature for `input.select-all` elements.
 */
export function activateSelectAll() {
    let selectAllInputs = document.querySelectorAll('input.select-all');
    for (const selectAllInput of selectAllInputs) {
        selectAllInput.addEventListener('click', selectAll);
    }
}
