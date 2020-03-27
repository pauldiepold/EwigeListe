<template>
    <div>
        <ul>
            <li v-for="player in players" v-text="player.userID"></li>
        </ul>
    </div>
</template>

<script>
    export default {
        props: {
            round: Object,
            deck: Object
        },

        data() {
            return {
                players: []
            }
        },

        computed: {
            channel() {
                return window.Echo.join('round.' + this.round.id);
            },

            allPlayersOnline() {
                return this.pluck(this.round.players, 'id')
                    .every(e => this.pluck(this.players, 'userID').includes(e));
            }
        },

        created() {
            this.channel
                .here(userIDs => {
                    this.players = userIDs;
                })
                .joining(userID => {
                    this.players.push(userID);
                })
                .leaving(userID => {
                    this.players.splice(this.players.indexOf(userID), 1);
                })
                .listen('testEvent', () => console.log('testEvent'));
        },

        methods: {
            pluck(array, key) {
                return array.map(o => o[key]);
            },
        }
    };
</script>
