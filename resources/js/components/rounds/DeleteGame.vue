<template>
    <div v-if="round.games.length >= 1">
        <div v-if="confirm === false" class="tw-mt-4">
            <button @click="confirm = true" type="button" class="btn btn-outline-primary">
                Letztes Spiel löschen
            </button>
        </div>
        <div v-if="confirm === true" class="tw-mt-4">
            <button @click="confirm = false" type="button" class="btn btn-outline-primary">
                Abbrechen
            </button>
        </div>
        <div v-if="confirm === true" class="tw-mt-2">
            <button @click="submit" type="button" class="btn btn-danger">
                <div class="d-flex vertical-align-center">
                    <span v-if="loading">
                        <i class="fa fa-spinner fa-spin text-lg mr-2"
                           style="font-size:1.2rem; vertical-align: -0.1rem;"></i>
                    </span>
                    <span>
                        Letztes Spiel wirklich löschen?
                    </span>
                </div>
            </button>
        </div>

    </div>
</template>

<script>
export default {
    props: ['round'],
    data() {
        return {
            confirm: false,
            loading: false,
        }
    },
    computed: {
        lastGame() {
            return this.round.games[this.round.games.length - 1];
        },
    },
    methods: {
        submit() {
            this.loading = true;
            axios.delete('/api/games/' + this.lastGame.id)
                .then(response => {
                    this.confirm = false;
                    this.loading = false;
                    this.$emit('updated');
                })
                .catch(error => {
                    console.log(error);
                });
        },
    }
};
</script>
