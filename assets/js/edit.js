let form;
let unsavedChanges = false;
const pageurl = basepath + pageid;

window.onload = () => {
    form = document.getElementById('update');
    let inputs = form.elements;
    for (i = 0; i < inputs.length; i++) {
        inputs[i].onchange = changeHandler;
        inputs[i].oninput = changeHandler;
    }

    form.addEventListener("submit", function (event) {
        event.preventDefault();
        submitHandler(this);
      });
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
                    submitHandler(form);
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
}

/**
 * Manage submit event
 * @param {HTMLFormElement} form
 */
function submitHandler(form) {
    unsavedChanges = false;

    var xhr = new XMLHttpRequest();
    var fd = new FormData(form);

    xhr.addEventListener("load", function(event) {
        alert("updated");
    });
    xhr.addEventListener("error", function(event) {
        alert('Error while trying to update.');
    });
    xhr.open(form.method, form.action);
    xhr.send(fd);
}

/**
 * Manage a beforeUnloadEvent
 * @param {BeforeUnloadEvent} e
 */
function confirmExit(e) {
    if (unsavedChanges) {
        return 'You have unsaved changes, do you really want to leave this page?';
    }
}
