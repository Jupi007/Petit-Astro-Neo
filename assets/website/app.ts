import focus from '@alpinejs/focus';
import Alpine from 'alpinejs';

import './bootstrap';
import './styles/app.css';

window.Alpine = Alpine;

Alpine.plugin(focus);
Alpine.start();
