require('./bootstrap');
require('./scripts/custom.js');

import Vue from 'vue';

import RoundGraph from './components/RoundGraph';
import ProfileGraphs from './components/ProfileGraphs';
import HomeGraph from './components/HomeGraph';
import CreateRound from './components/CreateRound';
import Alert from './components/Alert';
import Tabs from "./components/tabs/Tabs";
import Tab from "./components/tabs/Tab";

import Form from './lib/Form';

new Vue({
    el: '#app',
    components: {
        RoundGraph,
        ProfileGraphs,
        HomeGraph,
        CreateRound,
        Alert,
        Tab,
        Tabs
    },
    data: {
    },

    methods: {
    }
});

/* How to use Form.js

        form: new Form({
            players: preselectedPlayers,
            numberOfPlayers: 4,
        }),

        onSubmit() {
            this.form.post('/rounds')
                .then(response => window.location.href = response)
                .catch(errors => console.log(errors));
        }
 */

Vue.config.productionTip = false;
