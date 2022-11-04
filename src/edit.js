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
    // detect a Wcms markup then pass to 'wcms' mode
    start: [
        {
            regex: /%(?=(HEADER|NAV|ASIDE|MAIN|FOOTER|SUMMARY|LIST|MEDIA|TITLE|DESCRIPTION|DATE|TIME|THUMBNAIL|RSS)(\?[^\s]*)?%)/,
            token: 'wcms',
            next: 'wcms',
        },
        { regex: /\[\[[a-z0-9\-\_]+\]\]/, token: 'wikilink', next: 'start' },
        { regex: /<!--/, token: 'comment', next: 'comment' },
    ],
    // 'wcms' mode, for each macro, if there is parameters, pass to its associated mode
    wcms: [
        {
            regex: /(HEADER|NAV|ASIDE|MAIN|FOOTER)\?/,
            token: 'wcms',
            next: 'element',
        },
        { regex: /SUMMARY\?/, token: 'wcms', next: 'summary' },
        { regex: /LIST\?/, token: 'wcms', next: 'list' },
        { regex: /MEDIA\?/, token: 'wcms', next: 'media' },
        { regex: /RSS\?/, token: 'wcms', next: 'rss' },
        { regex: /(DATE|TIME)\?/, token: 'wcms', next: 'datetime' },
        { regex: /[^&]*&/, token: 'wcms', pop: true },
        { regex: /.*%/, token: 'wcms', next: 'start' },
    ],
    // 'element' mode, parameters' keywords of 'element' macros
    element: [
        {
            regex: /id|autolink|markdown|headerid/,
            token: 'wkeyword',
            push: 'wcms',
        },
        { regex: null, push: 'wcms' },
    ],
    // 'summary' mode, parameters' keywords of the 'summary' macro
    summary: [
        { regex: /min|max|element/, token: 'wkeyword', push: 'wcms' },
        { regex: null, push: 'wcms' },
    ],
    // 'list' mode, parameters' keywords of the 'list' macro
    list: [
        {
            regex: /sortby|order|secure|tagcompare|authorcompare|tagfilter|authorfilter|linkto|limit|description|thumbnail|date|time|since|until|author|style|bookmark/,
            token: 'wkeyword',
            push: 'wcms',
        },
        { regex: null, push: 'wcms' },
    ],
    // 'media' mode, parameters' keywords of the 'media' macro
    media: [
        { regex: /path|sortby|order|type/, token: 'wkeyword', push: 'wcms' },
        { regex: null, push: 'wcms' },
    ],
    // 'rss' mode, parameters' keywords of the 'rss' macro
    rss: [
        { regex: /bookmark/, token: 'wkeyword', push: 'wcms' },
        { regex: null, push: 'wcms' },
    ],
    // 'datetime' mode, parameters' keywords of the 'datetime' macro
    datetime: [
        { regex: /format|lang/, token: 'wkeyword', push: 'wcms' },
        { regex: null, push: 'wcms' },
    ],
    comment: [
        { regex: /.*?-->/, token: 'comment', next: 'start' },
        { regex: /.*/, token: 'comment' },
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
const inputEvent = new Event('input');

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
                    const a = document.getElementById('display');
                    const href = a.getAttribute('href');
                    const target = a.getAttribute('target');
                    window.open(href, target);
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
        if (httpOk(xhr.status)) {
            saved(JSON.parse(xhr.response));
        } else {
            alert('Error while trying to update: ' + xhr.statusText);
        }
    });
    xhr.addEventListener('error', function(event) {
        alert('Network error while trying to update.');
    });
    xhr.open(form.method, form.dataset.api);
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

function saved(data) {
    unsavedChanges = false;
    document.title = '✏ ' + pagetitle;
    document.getElementById('headid').innerHTML = pageid;
    document.querySelector('input[name="datemodif"]').value = data.datemodif;
}

/**
 * Check if an HTTP response status indicates a success.
 * @param {number} status
 */
function httpOk(status) {
    return status >= 200 && status < 300;
}
