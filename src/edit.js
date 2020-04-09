import CodeMirror from 'codemirror';
import 'codemirror/lib/codemirror.css';
import 'codemirror/mode/markdown/markdown';
import 'codemirror/mode/css/css';
import 'codemirror/mode/htmlmixed/htmlmixed';
import 'codemirror/mode/javascript/javascript';
import 'codemirror/addon/search/search';
import 'codemirror/addon/search/searchcursor';
import 'codemirror/addon/search/jump-to-line';
import 'codemirror/addon/dialog/dialog';
import 'codemirror/addon/dialog/dialog.css';
import 'codemirror/addon/mode/overlay';
import 'codemirror/addon/mode/simple';

CodeMirror.defineSimpleMode('wcms', {
    start: [
        {
            regex: /%(?:HEADER|NAV|ASIDE|MAIN|FOOTER|SUMMARY|LIST|MEDIA)(?:\?(?:[^ &]+=[^ &]+&?)+)?%/,
            token: 'wcms',
        },
    ],
});

CodeMirror.defineMode('wcms-markdown', (config, parserConfig) => {
    return CodeMirror.overlayMode(
        CodeMirror.getMode(config, parserConfig.backdrop || 'markdown'),
        CodeMirror.getMode(config, 'wcms')
    );
});

CodeMirror.defineMode('wcms-html', (config, parserConfig) => {
    return CodeMirror.overlayMode(
        CodeMirror.getMode(config, parserConfig.backdrop || 'htmlmixed'),
        CodeMirror.getMode(config, 'wcms')
    );
});

/** @type {HTMLFormElement} */
let form;

/** @type {CodeMirror.EditorFromTextArea[]} */
let editors = [];

/** @type {boolean} */
let unsavedChanges = false;

/** @type {InputEvent} */
const inputEvent = new InputEvent('input');

window.addEventListener('load', () => {
    form = document.getElementById('update');
    let inputs = form.elements;
    for (const input of inputs) {
        input.oninput = changeHandler;
    }

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        submitHandler(this);
    });

    // disable CodeMirror's default ctrl+D shortcut (delete line)
    delete CodeMirror.keyMap['default']['Ctrl-D'];

    editors = [
        CodeMirror.fromTextArea(document.getElementById('editmain'), {
            mode: 'wcms-markdown',
            lineNumbers: true,
            lineWrapping: true,
            extraKeys: { 'Alt-F': 'findPersistent' },
        }),
        CodeMirror.fromTextArea(document.getElementById('editcss'), {
            mode: 'css',
            lineNumbers: true,
            extraKeys: { 'Alt-F': 'findPersistent' },
        }),
        CodeMirror.fromTextArea(document.getElementById('editheader'), {
            mode: 'wcms-markdown',
            lineNumbers: true,
            lineWrapping: true,
            extraKeys: { 'Alt-F': 'findPersistent' },
        }),
        CodeMirror.fromTextArea(document.getElementById('editnav'), {
            mode: 'wcms-markdown',
            lineNumbers: true,
            lineWrapping: true,
            extraKeys: { 'Alt-F': 'findPersistent' },
        }),
        CodeMirror.fromTextArea(document.getElementById('editaside'), {
            mode: 'wcms-markdown',
            lineNumbers: true,
            lineWrapping: true,
            extraKeys: { 'Alt-F': 'findPersistent' },
        }),
        CodeMirror.fromTextArea(document.getElementById('editfooter'), {
            mode: 'wcms-markdown',
            lineNumbers: true,
            lineWrapping: true,
            extraKeys: { 'Alt-F': 'findPersistent' },
        }),
        CodeMirror.fromTextArea(document.getElementById('editbody'), {
            mode: 'wcms-html',
            lineNumbers: true,
            extraKeys: { 'Alt-F': 'findPersistent' },
        }),
        CodeMirror.fromTextArea(document.getElementById('editjavascript'), {
            mode: 'javascript',
            lineNumbers: true,
            extraKeys: { 'Alt-F': 'findPersistent' },
        }),
    ];
    for (const editor of editors) {
        editor.on('change', cmChangeHandler);
    }

    const fontSizeInput = document.getElementById('editfontsize');
    fontSizeInput.addEventListener('change', fontSizeChangeHandler);
    fontSizeInput.dispatchEvent(new Event('change'));

    document.getElementById('title').addEventListener('input', e => {
        pagetitle = e.target.value;
    });

    window.onkeydown = keyboardHandler;
    window.onbeforeunload = confirmExit;
});

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
                    const url = document
                        .getElementById('update')
                        .getAttribute('href');
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
    if (
        e.target.classList.contains('toggle') ||
        e.target.classList.contains('checkboxtab')
    ) {
        return;
    }
    changed();
}

/**
 * Manage CodeMirror editor change event
 * @param {CodeMirror.EditorFromTextArea} cm the CodeMirror instance
 */
function cmChangeHandler(cm) {
    let textArea = cm.getTextArea();
    textArea.value = cm.getValue();
    textArea.dispatchEvent(inputEvent);
}

function fontSizeChangeHandler(e) {
    for (const editor of editors) {
        const element = editor.getWrapperElement();
        element.style.fontSize = `${e.target.value}px`;
        editor.refresh();
    }
}

/**
 * Manage submit event
 * @param {HTMLFormElement} form
 */
function submitHandler(form) {
    var xhr = new XMLHttpRequest();
    var fd = new FormData(form);

    xhr.addEventListener('load', function(event) {
        saved();
    });
    xhr.addEventListener('error', function(event) {
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

function changed() {
    unsavedChanges = true;
    document.title = '✏ *' + pagetitle;
    document.getElementById('headid').innerHTML = '*' + pageid;
}

function saved() {
    unsavedChanges = false;
    document.title = '✏ ' + pagetitle;
    document.getElementById('headid').innerHTML = pageid;
}
