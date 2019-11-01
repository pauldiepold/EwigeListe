require('./bootstrap');
require('./scripts/custom.js');

import Vue from 'vue';

import RoundGraph from './components/RoundGraph';
import ProfileGraphs from './components/ProfileGraphs';
import GroupGraph from './components/GroupGraph';
import CreateRound from './components/CreateRound';
import Alert from './components/Alert';
import Tabs from "./components/components/Tabs";
import Tab from "./components/components/Tab";
import NavIcon from "./components/components/NavIcon";
import VueScrollTo from 'vue-scrollto';

Vue.use(VueScrollTo, {
    offset: -70
});

import Form from './lib/Form';

var vm = new Vue({
    el: '#app',
    components: {
        RoundGraph,
        ProfileGraphs,
        GroupGraph,
        CreateRound,
        Alert,
        Tab,
        Tabs,
        NavIcon
    },
    data: {
    },

    methods: {
    }
});

global.vm = vm;

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
