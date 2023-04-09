import CodeMirror from 'codemirror';
import 'codemirror/lib/codemirror.css';
import 'codemirror/theme/monokai.css';
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

import 'leaflet/dist/leaflet.css';
import icon from 'leaflet/dist/images/marker-icon.png';
import icon_2x from 'leaflet/dist/images/marker-icon-2x.png';
import shadow from 'leaflet/dist/images/marker-shadow.png';
import * as L from 'leaflet';

/* ________________ LEAFLET ________________ */

L.Icon.Default.prototype.options.iconUrl = icon;
L.Icon.Default.prototype.options.iconRetinaUrl = icon_2x;
L.Icon.Default.prototype.options.shadowUrl = shadow;

/** @var {HTMLInputElement} */
let inputLatitude = document.querySelector('input[name=latitude]');

/** @var {HTMLInputElement} */
let inputLongitude = document.querySelector('input[name=longitude]');

if (isNaN(inputLatitude.valueAsNumber) || isNaN(inputLongitude.valueAsNumber)) {
    var lat = 0;
    var long = 0;
    var zoom = 0;
} else {
    var lat = inputLatitude.valueAsNumber;
    var long = inputLongitude.valueAsNumber;
    var zoom = 7;
}

console.log(lat);

let map = L.map('geomap').setView([lat, long], zoom);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution:
        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
}).addTo(map);

let marker = L.marker([lat, long], {
    draggable: true,
}).addTo(map);

inputLatitude.addEventListener('change', inputLatitudeChangeHandler);
inputLongitude.addEventListener('change', inputLongitudeChangeHandler);
marker.addEventListener('dragend', markerDragHandler);

/**
 * Manage a change in the latitude input
 * @param {InputEvent} e
 */
function inputLatitudeChangeHandler(e) {
    if (isNaN(e.target.valueAsNumber)) {
        return;
    }
    let coord = marker.getLatLng();
    coord.lat = e.target.valueAsNumber;
    marker.setLatLng(coord);
}

/**
 * Manage a change in the longitude input
 * @param {InputEvent} e
 */
function inputLongitudeChangeHandler(e) {
    if (isNaN(e.target.valueAsNumber)) {
        return;
    }
    let coord = marker.getLatLng();
    coord.lng = e.target.valueAsNumber;
    marker.setLatLng(coord);
}

/**
 * Manage a drag of the marker
 * @param {L.DragEndEvent} e
 */
function markerDragHandler(e) {
    inputLatitude.value = e.target.getLatLng().lat.toFixed(5);
    inputLongitude.value = e.target.getLatLng().lng.toFixed(5);
    changed();
}

/* ________________ CODE MIRROR ________________ */

CodeMirror.defineSimpleMode('wcms', {
    // detect a Wcms markup then pass to 'wcms' mode
    start: [
        {
            regex: /%(?=(HEADER|NAV|ASIDE|MAIN|FOOTER|SUMMARY|LIST|MEDIA|TITLE|DESCRIPTION|DATE|TIME|DATEMODIF|TIMEMODIF|THUMBNAIL|RSS|AUTHORS|ID|PATH|URL|VISITCOUNT|EDITCOUNT|DISPLAYCOUNT)(\?[^\s]*)?%)/,
            token: 'wcms',
            next: 'wcms',
        },
        {
            regex: /\[\[[a-z0-9\-\_\#\/]+\]\]/,
            token: 'wikilink',
            next: 'start',
        },
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
        {
            regex: /(DATE|TIME|DATEMODIF|TIMEMODIF)\?/,
            token: 'wcms',
            next: 'datetime',
        },
        { regex: /[^&]*&/, token: 'wcms', pop: true },
        { regex: /.*%/, token: 'wcms', next: 'start' },
    ],
    // 'element' mode, parameters' keywords of 'element' macros
    element: [
        {
            regex: /id|everylink|markdown|headerid|headeranchor|urllinker|tag/,
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
            regex: /sortby|order|secure|tagcompare|authorcompare|tagfilter|authorfilter|linkto|limit|description|thumbnail|date|time|since|until|author|hidecurrent|style|bookmark/,
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
    initForm();
    if (theme !== 'none') {
        initEditors(theme);
    }
    initEditFeatures();
});

function initForm() {
    form = document.getElementById('update');
    let inputs = form.elements;
    for (const input of inputs) {
        input.oninput = changeHandler;
    }

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        submitHandler(this);
    });
}

function initEditors(theme) {
    // disable CodeMirror's default ctrl+D shortcut (delete line)
    delete CodeMirror.keyMap['default']['Ctrl-D'];

    editors = [
        CodeMirror.fromTextArea(document.getElementById('editmain'), {
            mode: 'wcms-markdown',
            lineNumbers: true,
            lineWrapping: true,
            theme,
            extraKeys: { 'Alt-F': 'findPersistent' },
        }),
        CodeMirror.fromTextArea(document.getElementById('editcss'), {
            mode: 'css',
            lineNumbers: true,
            theme,
            extraKeys: { 'Alt-F': 'findPersistent' },
        }),
        CodeMirror.fromTextArea(document.getElementById('editheader'), {
            mode: 'wcms-markdown',
            lineNumbers: true,
            lineWrapping: true,
            theme,
            extraKeys: { 'Alt-F': 'findPersistent' },
        }),
        CodeMirror.fromTextArea(document.getElementById('editnav'), {
            mode: 'wcms-markdown',
            lineNumbers: true,
            lineWrapping: true,
            theme,
            extraKeys: { 'Alt-F': 'findPersistent' },
        }),
        CodeMirror.fromTextArea(document.getElementById('editaside'), {
            mode: 'wcms-markdown',
            lineNumbers: true,
            lineWrapping: true,
            theme,
            extraKeys: { 'Alt-F': 'findPersistent' },
        }),
        CodeMirror.fromTextArea(document.getElementById('editfooter'), {
            mode: 'wcms-markdown',
            lineNumbers: true,
            lineWrapping: true,
            theme,
            extraKeys: { 'Alt-F': 'findPersistent' },
        }),
        CodeMirror.fromTextArea(document.getElementById('editbody'), {
            mode: 'wcms-html',
            lineNumbers: true,
            theme,
            extraKeys: { 'Alt-F': 'findPersistent' },
        }),
        CodeMirror.fromTextArea(document.getElementById('editjavascript'), {
            mode: 'javascript',
            lineNumbers: true,
            theme,
            extraKeys: { 'Alt-F': 'findPersistent' },
        }),
    ];
    for (const editor of editors) {
        editor.on('change', cmChangeHandler);
    }
}

function initEditFeatures() {
    const fontSizeInput = document.getElementById('editfontsize');
    fontSizeInput.addEventListener('change', fontSizeChangeHandler);
    fontSizeInput.dispatchEvent(new Event('change'));

    const themeSelect = document.getElementById('edithighlighttheme');
    themeSelect.addEventListener('change', themeChangeHandler);

    document.getElementById('title').addEventListener('input', e => {
        pagetitle = e.target.value;
    });

    window.onkeydown = keyboardHandler;
    window.onbeforeunload = confirmExit;
}

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
    const editorElements = document.getElementsByClassName('editorarea');
    for (const element of editorElements) {
        element.style.fontSize = `${e.target.value}px`;
    }
    for (const editor of editors) {
        const element = editor.getWrapperElement();
        element.style.fontSize = `${e.target.value}px`;
        editor.refresh();
    }
}

/**
 *
 * @param {InputEvent} e
 */
function themeChangeHandler(e) {
    if (theme === e.target.value) {
        return;
    }
    if (theme === 'none') {
        initEditors(e.target.value);
    } else {
        for (const editor of editors) {
            if (e.target.value === 'none') {
                editor.toTextArea();
            } else {
                editor.setOption('theme', e.target.value);
            }
        }
    }
    theme = e.target.value;
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
