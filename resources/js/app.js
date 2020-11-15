require('./bootstrap');
require('./scripts/custom.js');

const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

import Vue from 'vue';

import VueScrollTo from 'vue-scrollto';
import Vue2TouchEvents from 'vue2-touch-events';

Vue.use(VueScrollTo, {offset: -70});
Vue.use(Vue2TouchEvents, {swipeTolerance: 70});

global.vm = new Vue({
    el: '#app',
    components: {},
    data: {},
    methods: {}
});

Vue.config.productionTip = false;
