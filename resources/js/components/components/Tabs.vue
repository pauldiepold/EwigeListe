<template>
    <div>
        <div class="tabs">
            <ul class="tw-flex tw-justify-center tw-mb-6 tw-border-b tw-border-gray-400">
                <li class="tw-mx-1" v-for="tab in tabs">
                    <a class="tab"
                       :class="{ 'tab-active': tab.isActive }"
                       :href="tab.href"
                       @click="selectTab(tab)">
                        <div class="tw-px-2 tw-py-1 tw-flex tw-flex-col">
                            <i :class="tab.name"
                               class="fas tw-text-2xl tw-align-middle"></i>
                        </div>
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
    import NavIcon from "./NavIcon";

    export default {
        components: {
            NavIcon
        },
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
