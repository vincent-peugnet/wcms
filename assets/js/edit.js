let form;
let unsavedChanges = false;

window.onload = () => {
    form = document.getElementById('update');
    let inputs = form.elements;
    for (i = 0; i < inputs.length; i++) {
        inputs[i].onchange = changeHandler;
        inputs[i].oninput = changeHandler;
    }

    form.onsubmit = submitHandler;
    window.onkeydown = keyboardHandler;
    window.onbeforeunload = confirmExit;
};

/**
 * Manage a keyboardEvent
 * @param {KeyboardEvent} e
 */
function keyboardHandler(e) {
    if (e.composed) {
        if (e.ctrlKey) {
            // console.log(e.key);
            switch (e.key) {
                case 's':
                    e.preventDefault();
                    unsavedChanges = false;
                    form.submit();
                    return false;
            }
        }
    }
}

/**
 * Manage change event
 * @param {Event} e
 */
function changeHandler(e) {
    unsavedChanges = true;
    // console.log({unsavedChanges});
}

/**
 * Manage submit event
 * @param {Event} e
 */
function submitHandler(e) {
    unsavedChanges = false;
}

/**
 * Manage a beforeUnloadEvent
 * @param {BeforeUnloadEvent} e
 */
function confirmExit(e) {
    // console.log({unsavedChanges});
    if (unsavedChanges) {
        return "You have unsaved changes, do you really want to leave this page?";
    }
}