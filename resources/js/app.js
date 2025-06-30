import './bootstrap';

import Alpine from 'alpinejs';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const editors = document.querySelectorAll('.ckeditor');
    editors.forEach(el => {
        ClassicEditor.create(el).catch(error => console.error(error));
    });
});