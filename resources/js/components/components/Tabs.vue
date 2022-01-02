<template>
    <div style="min-height: 50vh;">
        <div class="tw-sticky tw-top-0 tw-z-50 tw-pt-2 tw--mx-4 tw-flex tw-justify-center"
             style="background-color: #f1f1ef;">
            <ul class="tw-flex-1 tw-flex tw-justify-around tw-border-b tw-border-gray-400 tw-max-w-md">
                <li v-for="tab in tabs" class="tw-mx-1">
                    <a class="tw-min-w-7xs tw-inline-block tw-py-2 tw-text-orange-600 tw-w-100 tw-no-underline hover:tw-no-underline "
                       :class="{ 'tw--mx-px tw--mt-px tw-border-l tw-border-t tw-border-r tw-rounded-t-lg tw-border-gray-400 tw-text-orange-700': tab.isActive }"
                       :href="tab.href"
                       @click="selectTab(tab);">
                        <div class="tw-flex tw-flex-col"
                             :title="tab.name">
                            <i :class="tab.icon"
                               class="fas tw-text-2xl tw-align-middle"
                            ></i>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        <div class="tabs-details">
            <slot name="oben" :selectedTabName="selectedTab.name"></slot>
            <slot :selectedTabName="selectedTab.name"></slot>
            <slot name="unten" :selectedTabName="selectedTab.name"></slot>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            tabs: [],
            selectedTab: '',
            mobile: false
        };
    },

    created() {
        this.getOrientation();
        window.addEventListener("resize", this.getOrientation);
    },

    unmounted() {
        window.removeEventListener("resize", this.getOrientation);
        window.removeEventListener("fullscreenchange", this.getFullscreen);
    },

    methods: {
        selectTabByName(name) {
            this.selectTab(this.tabs.find(tab => tab.$props.name === name));
        },
        selectTab(selectedTab) {
            if (selectedTab.name === 'Live') {
                this.$emit('clicked');
            }
            this.tabs.forEach(tab => {
                tab.isActive = (tab.href === selectedTab.href);
                tab.tabKey++;
            });

            this.selectedTab = selectedTab;
        },
        getOrientation() {
            this.mobile = window.screen.width < 1024;
        },
    }
};
</script>
