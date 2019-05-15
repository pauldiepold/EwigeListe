require('./bootstrap');
require('./scripts/custom.js');

import Vue from 'vue';

import RoundGraph from './components/RoundGraph.vue';
import ProfileGraphs from './components/ProfileGraphs.vue';
import HomeGraph from './components/HomeGraph.vue';

new Vue({
    el: '#app',
    components: {
        RoundGraph,
        ProfileGraphs,
        HomeGraph
    }
});
Vue.config.productionTip = false;