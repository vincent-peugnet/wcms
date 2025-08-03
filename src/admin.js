import Tagify from '@yaireo/tagify';
import '@yaireo/tagify/dist/tagify.css';

/* ________________ TAGIFY  ________________ */

/** @var {HTMLInputElement} */
let inputTag = document.querySelector('input[name=defaulttag]');

let tagify = new Tagify(inputTag, {
    originalInputValueFormat: valuesArr =>
        valuesArr.map(item => item.value).join(', '),
    pattern: /^[a-z0-9_-]{1,64}$/,
    editTags: false,
    whitelist: taglist,
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
                <span class="${this.settings.classNames.tagText}">${tagData[
                this.settings.tagTextProp
            ] || tagData.value}</span>
            </div>
          </tag>`;
        },
        dropdownItem(item) {
            return `<div ${this.getAttributes(item)}
                        class='${
                            this.settings.classNames.dropdownItem
                        } ${item.class || ''}'
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
