require('./bootstrap');
require('./scripts/custom.js');

import Vue from 'vue';

window.Vue = require('vue');

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

//Vue.component('graph', require('./components/Graph.vue').default);

Vue.component('tabs', {

    template: `
        d
    `,


    mounted() {
        console.log(this.$children);
    },
});


let app = new Vue({
    el: '#app',
    data: {
        message: 'Hello World',
        names: ['Joe', 'Mary', 'Jane', 'Jack']
    }
});
Vue.config.productionTip = false;