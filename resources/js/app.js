require('./bootstrap');
require('./scripts/custom.js');
/*require('./scripts/cards.js');*/


import {createApp} from 'vue';
import {plugin as Slicksort} from 'vue-slicksort';
import VueScrollTo from 'vue-scrollto';

const app = createApp({})
    .use(VueScrollTo, {offset: -70})
    .use(Slicksort);

const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => app.component(key.split('/').pop().split('.')[0], files(key).default))

app.mount('#app');
/*global.vm = new Vue({
    el: '#app',
    components: {},
    data: {},
    methods: {}
});*/
