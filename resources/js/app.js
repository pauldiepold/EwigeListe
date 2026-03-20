import './bootstrap';
import './scripts/custom.js';

import { createApp } from 'vue';
import { plugin as Slicksort } from 'vue-slicksort';
import VueScrollTo from 'vue-scrollto';

const app = createApp({})
    .use(VueScrollTo, { offset: -70 })
    .use(Slicksort);

const files = import.meta.glob('./**/*.vue', { eager: true });
Object.entries(files).forEach(([path, module]) => {
    const name = path.split('/').pop().replace(/\.\w+$/, '');
    app.component(name, module.default);
});

app.mount('#app');
