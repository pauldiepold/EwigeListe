require('./bootstrap');
require('./scripts/custom.js');

import Vue from 'vue';

Vue.component('graph_round', require('./components/RoundGraph.vue').default);

new Vue({
    el: '#app'
});
Vue.config.productionTip = false;