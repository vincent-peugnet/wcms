let form;
let unsavedChanges = false;
const pageurl = basepath + pageid;

window.onload = () => {
    form = document.getElementById('update');
    let inputs = form.elements;
    for (i = 0; i < inputs.length; i++) {
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
            switch (e.key) {
                // ctrl + s
                case 's':
                    submitHandler(form);
                    break;
                // ctrl + d
                case 'd':
                    url = document.getElementById('update').getAttribute('href');
                    window.open(url);
                    break;
                default:
                    return true;
            }
            e.preventDefault();
            return false;
        }
    }
}

/**
 * Manage change event
 * @param {InputEvent} e
 */
function changeHandler(e) {
    if(e.target.classList.contains("toggle")||e.target.classList.contains("checkboxtab")) {
        return;
    }
    unsavedChanges = true;
}

/**
 * Manage submit event
 * @param {HTMLFormElement} form
 */
function submitHandler(form) {
    var xhr = new XMLHttpRequest();
    var fd = new FormData(form);

    xhr.addEventListener("load", function(event) {
        unsavedChanges = false;
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
