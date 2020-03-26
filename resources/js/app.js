require('./bootstrap');
require('./scripts/custom.js');
//require('@fortawesome/fontawesome-free/js/all.min.js');

import Vue from 'vue';

import RoundGraph from './components/RoundGraph';
import ProfileGraphs from './components/ProfileGraphs';
import GroupGraph from './components/GroupGraph';
import CreateRound from './components/CreateRound';
import CreateGroup from './components/CreateGroup';
import UpdateGroups from './components/UpdateGroups';
import UpdateRoundDates from './components/UpdateRoundDates';
import StandardGroups from './components/StandardGroups';
import Alert from './components/Alert';
import Tabs from "./components/components/Tabs";
import Tab from "./components/components/Tab";
import Badge from "./components/components/Badge";
import NavIcon from "./components/components/NavIcon";
import SelectListe from "./components/components/SelectListe";
import Toggle from "./components/components/Toggle";
import DatePicker from "./components/components/DatePicker";
import AvatarForm from "./components/AvatarForm";

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
        CreateGroup,
        Alert,
        Tab,
        Tabs,
        NavIcon,
        Badge,
        SelectListe,
        UpdateGroups,
        UpdateRoundDates,
        StandardGroups,
        Toggle,
        DatePicker,
        AvatarForm
    },
    data: {    },

    methods: {}
});

Vue.config.productionTip = false;
