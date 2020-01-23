<template>
    <div>
        <div v-for="group in filteredGroups"
             class="text-left d-flex align-items-center justify-content-between group"
             style="max-width: 24rem;"
             id="groups">
            <a :href="'/liste/' + group.id"
               class="font-weight-bold tw-text-black"
               :class="{'tw-text-gray-600': !inSelectedGroups(group)}">
                {{group.name}}
            </a><!-- tw-block tw-flex-grow -->
            <div class="tw--mx-4 tw-px-4 tw--my-2 tw-py-2 tw-flex tw-items-center"
                 @click="inSelectedGroups(group) && edit ? removeGroup(group) : addGroup(group)"
                 :class="{'tw-cursor-pointer': group.id !== 1 && group.closed !== 1 && edit}">
                <i class="fas fa-2x tw-text-gray-700"
                   :class="{'fa-toggle-on': inSelectedGroups(group), 'fa-toggle-off': !inSelectedGroups(group)}"
                   v-show="group.id !== 1 && group.closed !== 1 && edit">
                </i>
            </div>
        </div>

        <button v-show="canUpdate && !edit"
                @click="edit = true"
                class="btn btn-outline-primary mt-4 mx-auto">
            Listen bearbeiten
        </button>

        <form @submit.prevent="submit"
              v-show="canUpdate && edit">
            <button type="submit"
                    class="btn btn-primary mt-4 d-flex vertical-align-center mx-auto">
                <span>
                    <i v-if="loading"
                       class="fa fa-spinner fa-spin text-lg mr-2"
                       style="font-size:1.2rem; vertical-align: -0.1rem;"></i>
                </span>
                <span>
                    Listen speichern
                </span>
            </button>
        </form>

        <button v-show="canUpdate && edit"
                @click="cancelEdit"
                class="btn btn-outline-primary mt-4 mx-auto">
            Bearbeiten abbrechen
        </button>

        <div v-show="canUpdate && edit">
            <a data-container="body"
               data-toggle="popover"
               data-placement="top"
               title="Listen hinzufügen"
               data-content="Eine Runde kann nur Listen hinzugefügt werden, in denen mindestens einer der Spieler Mitglied ist. Erstelle eine neue Liste oder trete einer bestehende Liste bei, um die Runde auf die Liste zu schreiben.">
                <i class="fas fa-info-circle fa-lg tw-mt-6"></i>
            </a>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'roundInput',
            'canUpdate'
        ],

        data() {
            return {
                groups: [],
                edit: false,
                loading: false
            };
        },

        created() {
            let self = this;
            self.roundInput.groups.forEach(function (group) {
                self.groups.push(group.id);
            });
        },

        computed: {
            filteredGroups() {
                let output = [];

                this.roundInput.players.forEach(player => {
                    player.groups.forEach(group => {
                        if (!output.map(v => v.id).includes(group.id)) {
                            output.push(group);
                        }
                    });
                });

                output = output.filter(group => !group.closed && (this.edit || this.inSelectedGroups(group)));

                return output;
            },
        },
        methods: {
            inSelectedGroups(group) {
                return this.groups.includes(group.id);
            },
            addGroup(group) {
                if (!this.inSelectedGroups(group)) {
                    this.groups.push(group.id);
                }
            },
            removeGroup(group) {
                if (group.id !== 1 && group.closed !== 1) {
                    let index = this.groups.indexOf(group.id);
                    if (index > -1) {
                        this.groups.splice(index, 1);
                    }
                }
            },
            submit() {
                this.loading = true;

                axios.post('/rounds/' + this.roundInput.id, {
                    '_method': 'PATCH',
                    'groups': this.groups
                })
                    .then(response => {
                        //console.log(response.data);
                        this.edit = false;
                        this.loading = false;
                    })
                    .catch(error => {
                        //console.log(error.response.data.errors)
                        this.loading = false;
                    });
            },
            cancelEdit() {
                this.edit = false;
                let output = [];
                this.roundInput.groups.forEach(function (group) {
                    output.push(group.id);
                });
                this.groups = output;
            }
        },

    };
</script>
