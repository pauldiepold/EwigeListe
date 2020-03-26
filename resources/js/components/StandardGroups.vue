<template>
    <div>
        <div v-show="showAlert" class="alert alert-success tw-mb-4 tw-max-w-md">
            <div class="row align-items-center no-gutters">
                <div class="col-11">
                    Listen Einstellungen erfolgreich gespeichert!
                </div>
                <div class="col-1">
                    <button type="button" class="close" @click="showAlert=false">
                        <i class="fas fa-sm fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
        <div v-for="group in groups"
             class="text-left d-flex align-items-center justify-content-between group"
             style="max-width: 24rem;"
             id="groups">
                {{group.name}}
            <div class="tw--mx-4 tw-px-4 tw--my-2 tw-py-2 tw-flex tw-items-center"
                 @click="group.default ? removeGroup(group) : addGroup(group)"
                 :class="{'tw-cursor-pointer': group.group_id !== 1 && !group.closed}">
                <i class="fas fa-2x tw-text-gray-700"
                   :class="{'fa-toggle-on': group.default, 'fa-toggle-off': !group.default}"
                   v-show="!group.closed && group.group_id !== 1">
                </i>
                <i class="fa fa-lock fa-lg tw-mr-2" v-show="group.closed"></i>
            </div>
        </div>

        <form @submit.prevent="submit">
            <button type="submit"
                    class="btn btn-primary mt-4 d-flex vertical-align-center mx-auto">
                <span>
                    <i v-if="loading"
                       class="fa fa-spinner fa-spin text-lg mr-2"
                       style="font-size:1.2rem; vertical-align: -0.1rem;"></i>
                </span>
                <span>
                    Listen Einstellungen speichern
                </span>
            </button>
        </form>
    </div>
</template>

<script>
    export default {
        props: [
            'profilesInput',
            'userId'
        ],

        data() {
            return {
                groups: [],
                loading: false,
                showAlert: false
            };
        },

        created() {
            let self = this;
            self.profilesInput.forEach(function (group) {
                self.groups.push({
                    "profile_id": group.id,
                    "group_id": group.group.id,
                    "closed": group.group.closed,
                    "default": group.default,
                    "name": group.group.name});
            });
        },

        computed: {

        },
        methods: {
            addGroup(group) {
                this.showAlert = false;
                if (!group.default) {
                    let index = this.groups.indexOf(group);
                    this.groups[index].default = 1;
                }
            },
            removeGroup(group) {
                this.showAlert = false;
                if (group.default) {
                    let index = this.groups.indexOf(group);
                    this.groups[index].default = 0;
                }
            },
            submit() {
                this.loading = true;

                axios.post('/users/' + this.userId + '/listen', {
                    '_method': 'PATCH',
                    'groups': this.groups
                })
                    .then(response => {
                        console.log(response.data);
                        this.loading = false;
                        this.showAlert = true;
                    })
                    .catch(error => {
                        console.log(error.response.data.errors)
                        this.loading = false;
                    });
            },
        },

    };
</script>
