<template>
    <div>
        <div class="tabs">
            <ul class="tw-flex tw-justify-center tw-mb-6 tw-border-b tw-border-gray-400">
                <li class="mr-1" v-for="tab in tabs">
                    <a class="tab"
                       :class="{ 'tab-active': tab.isActive }"
                       :href="tab.href"
                       @click="selectTab(tab)">
                        {{ tab.name }}
                    </a>
                </li>
            </ul>
        </div>
        <div class="tabs-details">
            <slot v-bind:route="route"></slot>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                tabs: [],
                route: window.location.hash,
            };
        },

        created() {
            this.tabs = this.$children;
        },

        methods: {
            selectTab(selectedTab) {
                this.tabs.forEach(tab => {
                    tab.isActive = (tab.href === selectedTab.href);
                });
            }
        }
    };
</script>
