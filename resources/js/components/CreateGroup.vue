<template>
    <div>
        <div class="form-group">
            <label for="dateStart">Start-Datum:</label>
            <date-picker v-model="dateStart" id="dateStart"></date-picker>
        </div>
        <div class="form-group">
            <label for="dateFinish">End-Datum:</label>
            <date-picker v-model="dateFinish" id="dateFinish"></date-picker>
        </div>
    </div>
</template>

<script>

    import moment from 'moment';

    moment.locale('de');

    export default {
        props: {
            allPlayers: {
                type: Array,
                required: true,
            }
        },

        data() {
            return {
                players: [],
                dateStart: '',
                dateFinish: '',
            }
        },

        created() {

        },

        methods: {}
    };
</script>
