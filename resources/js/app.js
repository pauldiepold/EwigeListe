import './bootstrap';
import './scripts/custom.js';

import { createApp } from 'vue';

const app = createApp({});

const files = import.meta.glob('./**/*.vue', { eager: true });
Object.entries(files).forEach(([path, module]) => {
    const name = path.split('/').pop().replace(/\.\w+$/, '');
    app.component(name, module.default);
});

app.mount('#app');
