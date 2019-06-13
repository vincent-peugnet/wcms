let form;
let unsavedChanges = false;
const pageurl = basepath + pageid;
const myWorker = new Worker(jspath + 'worker.js');

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

    myWorker.postMessage({
        type: 'init',
        pageurl: pageurl,
    });
    myWorker.postMessage({ type: 'stillEditing' });
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
        alert(event.target.responseText);
    });
    xhr.addEventListener("error", function(event) {
        alert('Oups! Quelque chose s\'est mal passé.');
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
        const url = pageurl + '/removeeditby';
        console.log('send quit editing')
        fetch(url, { method: 'POST' })
            .then(handleErrors)
            .then((response) => {
                console.log(response);
                setTimeout(() => {
                    myWorker.postMessage({ type: 'stillEditing' });
                }, 1500);
            });
        return 'You have unsaved changes, do you really want to leave this page?';
    } else {
        myWorker.postMessage({ type: 'quitEditing' });
    }
}

async function handleErrors(response) {
    if (!response.ok) {
        const data = await response.json();
        throw Error(`${response.statusText}. ${data.message}`);
    }
    return response.json();
}
