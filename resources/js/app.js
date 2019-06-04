require('./bootstrap');
require('./scripts/custom.js');

import Vue from 'vue';

import RoundGraph from './components/RoundGraph';
import ProfileGraphs from './components/ProfileGraphs';
import HomeGraph from './components/HomeGraph';
import CreateRound from './components/CreateRound';
import Alert from './components/Alert';

import Form from './lib/Form';

new Vue({
    el: '#app',
    components: {
        RoundGraph,
        ProfileGraphs,
        HomeGraph,
        CreateRound,
        Alert
    },
    data: {
        form: new Form({
            players: preselectedPlayers,
            numberOfPlayers: 4,
        }),
    },

    methods: {
        onSubmit() {
            this.form.post('/rounds')
                .then(response => window.location.href = response)
                .catch(errors => console.log(errors));
        }
    }
});
Vue.config.productionTip = false;