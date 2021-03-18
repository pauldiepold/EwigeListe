require('./bootstrap');
require('./scripts/custom.js');
require('./scripts/cards.js');

const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

import Vue from 'vue';

//import fullscreen from 'vue-fullscreen';
import VueScrollTo from 'vue-scrollto';
import Vue2TouchEvents from 'vue2-touch-events';

//Vue.use(fullscreen)
Vue.use(VueScrollTo, {offset: -70});
Vue.use(Vue2TouchEvents, {swipeTolerance: 70});

Vue.mixin({
    methods: {
        pluck: (array, key) => array.map(o => o[key]),
    }
})

global.vm = new Vue({
    el: '#app',
    components: {},
    data: {},
    methods: {}
});

Vue.config.productionTip = false;
