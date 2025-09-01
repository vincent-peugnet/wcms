import Tagify from '@yaireo/tagify';

/**
 *
 * @param {HTMLInputElement} inputTag
 */
export function initTagify(inputTag) {
    // taglist come from assets/js/taglist.js
    // if not successfully loaded: use empty array instead
    if (typeof taglist === 'undefined' || taglist === null) {
        var tags = [];
        console.log(
            'use empty taglist as taglist.js has not been generated yet'
        );
    } else {
        var tags = taglist;
    }

    let tagify = new Tagify(inputTag, {
        originalInputValueFormat: valuesArr =>
            valuesArr.map(item => item.value).join(', '),
        pattern: /^[a-z0-9_-]{1,64}$/,
        editTags: false,
        whitelist: tags,
        dropdown: {
            enabled: 0,
            maxItems: 30,
        },
        templates: {
            tag(tagData, tagify) {
                return `<tag title="${tagData.title || tagData.value}"
                    contenteditable='false'
                    spellcheck='false'
                    tabIndex="${this.settings.a11y.focusableTags ? 0 : -1}"
                    class="${this.settings.classNames.tag} ${
                    tagData.class ? tagData.class : ''
                } tag_${tagData.value}"
                        ${this.getAttributes(tagData)}>
                        <x title='' class="${
                            this.settings.classNames.tagX
                        }" role='button' aria-label='remove tag'></x>
                            <div>
                            <span class="${
                                this.settings.classNames.tagText
                            }">${tagData[this.settings.tagTextProp] ||
                    tagData.value}</span>
            </div>
            </tag>`;
            },
            dropdownItem(item) {
                return `<div ${this.getAttributes(item)}
            class='${this.settings.classNames.dropdownItem} ${item.class || ''}'
                        tabindex="0"
                        role="option"
                        >
                        <span class="tag tag_${
                            item.value
                        }">${item.mappedValue || item.value}</span>
                            </div>`;
            },
        },
    });
}

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
    let details = document.querySelectorAll('.dropdown');
    let currentDetail = e.target.closest('.dropdown');
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
 * Manage submit event for a given form
 * @param {HTMLFormElement} form
 * @param {(response: any) => void} onSuccess
 */
export function submitHandler(form, onSuccess = () => {}) {
    var xhr = new XMLHttpRequest();
    var fd = new FormData(form);

    xhr.addEventListener('load', function(event) {
        if (httpOk(xhr.status)) {
            onSuccess(xhr.response);
        } else {
            alert(
                '⚠️ Error while trying to update:\ncopy your work and refresh the current page\n\nAPI response code: ' +
                    xhr.status +
                    ' ' +
                    xhr.statusText
            );
        }
    });
    xhr.addEventListener('error', function(event) {
        alert('Network error while trying to update.');
    });
    xhr.open(form.method, form.dataset.api);
    xhr.send(fd);
}

/**
 * Check if an HTTP response status indicates a success.
 * @param {number} status
 */
function httpOk(status) {
    return status >= 200 && status < 300;
}

export function initWorkspaceForm() {
    let form = document.getElementById('workspace-form');
    let inputs = form.elements;
    for (const input of inputs) {
        input.oninput = workspaceChanged;
    }
    let saveworkspace = document.getElementById('save-workspace');
    if (saveworkspace instanceof HTMLElement) {
        saveworkspace.style.display = 'none';
    }

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        submitHandler(this);
    });
}

/**
 * @param {InputEvent} e
 */
function workspaceChanged(e) {
    let elem = e.target;
    elem.form.requestSubmit();
}
