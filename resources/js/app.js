import './bootstrap';
import { initModalScripts } from './modal-utils.js';
import { initOptionsMenus } from './options-menu.js';
import { initAjaxForms, submitFormAjax } from './form-ajax-utils.js';

window.initModalScripts = initModalScripts;
window.submitFormAjax = submitFormAjax;
window.initAjaxForms = initAjaxForms;

document.addEventListener('DOMContentLoaded', () => {
    initModalScripts(document);
    initOptionsMenus(document);
    initAjaxForms(document);
});
