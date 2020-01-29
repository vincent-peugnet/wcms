/**
 * Manage input event on the checkall checkbox.
 * Call with .bind({checkboxes: HTMLElement[]})
 * @param {InputEvent} e the input event
 */
export function checkallHandler(e) {
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
 * Close all submenus of the menubar.
 * @param {MouseEvent} e
 */
export function closeSubmenus(e) {
    let details = document.querySelectorAll('aside details');
    let currentDetail = e.target.closest('details');
    for (const detail of details) {
        if (!detail.isSameNode(currentDetail)) {
            detail.removeAttribute('open');
        }
    }
}
