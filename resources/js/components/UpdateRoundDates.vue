<template>
    <div>
        <date-picker v-model="date"></date-picker>

        <form @submit.prevent="submit">
            <button type="submit"
                    class="btn btn-primary mt-4 d-flex vertical-align-center mx-auto">
                <span>
                    <i v-if="loading"
                       class="fa fa-spinner fa-spin text-lg mr-2"
                       style="font-size:1.2rem; vertical-align: -0.1rem;"></i>
                </span>
                <span>
                    Datum verschieben
                </span>
            </button>
        </form>
    </div>
</template>

<script>
    import DatePicker from './components/DatePicker';
    import moment from 'moment';

    moment.locale('de');

    export default {
        components: {
            DatePicker
        },

        props: [
            'roundId'
        ],

        data() {
            return {
                date: '',
                loading: false
            };
        },

        created() {
        },

        computed: {
        },

        methods: {
            submit() {
                this.loading = true;

                axios.post('/rounds/dates/' + this.roundId, {
                    '_method': 'PATCH',
                    'date': moment(this.date).format('YYYY-MM-DD')
                })
                    .then(response => {
                        console.log(response.data);
                        this.loading = false;
                    })
                    .catch(error => {
                        console.log(error.response.data.errors)
                        this.loading = false;
                    });
            },
        },

    };
</script>
