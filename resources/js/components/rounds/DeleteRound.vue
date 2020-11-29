<template>
    <div>
        <button v-if="!show" @click="show = true" class="btn btn-outline-primary mt-4">Runde löschen</button>
        <button v-if="show" @click="show = false" class="btn btn-outline-primary mt-4">Abbrechen</button>
        <br>
        <button v-if="show" @click="submit" class="btn btn-primary mt-2">Runde wirklich löschen?</button>
    </div>
</template>

<script>
export default {
    props: ['round'],
    data() {
        return {
            show: false,
        }
    },
    computed: {},
    methods: {
        submit() {
            axios.post('/rounds/' + this.round.id, {
                '_method': 'DELETE'
            })
                .then((e) => {
                    window.location.href = "/runde/erstellen";
                });
        }
    }
};
</script>
