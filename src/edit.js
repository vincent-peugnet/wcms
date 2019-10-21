import CodeMirror from "codemirror";
import "codemirror/lib/codemirror.css";
import "codemirror/mode/markdown/markdown";
import "codemirror/mode/css/css"
import "codemirror/mode/htmlmixed/htmlmixed"
import "codemirror/mode/javascript/javascript"

let form;
let unsavedChanges = false;
const inputEvent = new InputEvent('input');

window.onload = () => {
    form = document.getElementById('update');
    let inputs = form.elements;
    for (const input of inputs) {
        input.oninput = changeHandler;
    }

    form.addEventListener("submit", function (event) {
        event.preventDefault();
        submitHandler(this);
    });

    CodeMirror.fromTextArea(document.getElementById('editmain'), {
        mode: 'markdown',
        lineNumbers: true,
    }).on('change', cmChangeHandler);

    CodeMirror.fromTextArea(document.getElementById('editcss'), {
        mode: 'css',
        lineNumbers: true,
    }).on('change', cmChangeHandler);

    CodeMirror.fromTextArea(document.getElementById('editheader'), {
        mode: 'markdown',
        lineNumbers: true,
    }).on('change', cmChangeHandler);

    CodeMirror.fromTextArea(document.getElementById('editnav'), {
        mode: 'markdown',
        lineNumbers: true,
    }).on('change', cmChangeHandler);

    CodeMirror.fromTextArea(document.getElementById('editaside'), {
        mode: 'markdown',
        lineNumbers: true,
    }).on('change', cmChangeHandler);

    CodeMirror.fromTextArea(document.getElementById('editfooter'), {
        mode: 'markdown',
        lineNumbers: true,
    }).on('change', cmChangeHandler);

    CodeMirror.fromTextArea(document.getElementById('editbody'), {
        mode: 'htmlmixed',
        lineNumbers: true,
    }).on('change', cmChangeHandler);

    CodeMirror.fromTextArea(document.getElementById('editjavascript'), {
        mode: 'javascript',
        lineNumbers: true,
    }).on('change', cmChangeHandler);

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
 * Manage CodeMirror editor change event
 * @param {CodeMirror.EditorFromTextArea} cm the CodeMirror instance
 */
function cmChangeHandler(cm){
    let textArea = cm.getTextArea();
    textArea.value = cm.getValue();
    textArea.dispatchEvent(inputEvent);
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
