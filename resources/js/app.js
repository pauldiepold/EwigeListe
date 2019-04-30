require('./bootstrap');
require('./scripts/custom.js');

import Vue from 'vue';

Vue.component('graph', require('./components/Graph.vue').default);

new Vue({
    el: '#app'
});
Vue.config.productionTip = false;