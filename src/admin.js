import { initTagify } from './fn/fn';
import '@yaireo/tagify/dist/tagify.css';

/* ________________ TAGIFY  ________________ */

/** @var {HTMLInputElement} */
let inputTag = document.querySelector('input[name=defaulttag]');

initTagify(inputTag);
