import {
    activateCheckall,
    activateCloseSubmenus,
    initWorkspaceForm,
    initTagify,
} from './fn/fn';

import '@yaireo/tagify/dist/tagify.css';

window.addEventListener('load', () => {
    activateCheckall('pagesid[]', 'checkall');
    activateCloseSubmenus();
    initWorkspaceForm();
});

/* ________________ TAGIFY  ________________ */

/** @var {HTMLInputElement} */
let inputTag = document.querySelector('input[name=addtag]');

initTagify(inputTag);
