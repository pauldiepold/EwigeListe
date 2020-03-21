<template>
    <div class="input-group tw-mx-auto tw-max-w-2xs">
        <div class="input-group-prepend">
            <div class="input-group-text">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>

        <date-picker-raw v-model="datePickerValue"
                         :language="de"
                         :format="customFormatter"
                         :full-month-name="true"
                         :wrapper-class="'date-picker-wrapper fullscreen-when-on-mobile'"
                         :input-class="'date-picker-input'"
                         :monday-first="true"
        ></date-picker-raw>
    </div>
</template>

<script>

    import DatePickerRaw from 'vuejs-datepicker';
    import {de} from 'vuejs-datepicker/dist/locale';
    import moment from 'moment';

    moment.locale('de');

    export default {
        components: {
            DatePickerRaw
        },

        model: {
            prop: 'date',
            event: 'change'
        },

        props: {
            date: ''
        },

        data() {
            return {
                de: de,
                datePickerValue: '',
            }
        },

        watch: {
            datePickerValue: function (date, oldDate) {
                this.$emit('change', moment(date))
            }
        },

        methods: {
            customFormatter(date) {
                return moment(date).format('Do MMMM YYYY');
            }
        }
    };
</script>
