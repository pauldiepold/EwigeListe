require('./bootstrap');
require('./scripts/custom.js');

import Vue from 'vue';

import RoundGraph from './components/RoundGraph';
import ProfileGraphs from './components/ProfileGraphs';
import GroupGraph from './components/GroupGraph';
import CreateRound from './components/CreateRound';
import UpdateGroups from './components/UpdateGroups';
import Alert from './components/Alert';
import Tabs from "./components/components/Tabs";
import Tab from "./components/components/Tab";
import Badge from "./components/components/Badge";
import NavIcon from "./components/components/NavIcon";
import SelectListe from "./components/components/SelectListe";

import VueScrollTo from 'vue-scrollto';
import Vue2TouchEvents from 'vue2-touch-events';

Vue.use(VueScrollTo, {offset: -70});
Vue.use(Vue2TouchEvents, {swipeTolerance: 70});

global.vm = new Vue({
    el: '#app',
    components: {
        RoundGraph,
        ProfileGraphs,
        GroupGraph,
        CreateRound,
        Alert,
        Tab,
        Tabs,
        NavIcon,
        Badge,
        SelectListe,
        UpdateGroups
    },
    data: {},

    methods: {}
});

Vue.config.productionTip = false;
