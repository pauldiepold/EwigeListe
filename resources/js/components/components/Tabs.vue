<template>
    <div style="min-height: 50vh;">
        <div class="sticky top-0 z-50 pt-2 -mx-4 flex justify-center"
             style="background-color: #f1f1ef;">
            <ul class="flex-1 flex justify-around border-b border-gray-400 max-w-md">
                <li v-for="tab in tabs" class="mx-1">
                    <a class="min-w-7xs inline-block py-2 text-orange-600 w-100 no-underline hover:no-underline "
                       :class="{ '-mx-px -mt-px border-l border-t border-r rounded-t-lg border-gray-400 text-orange-700': tab.isActive }"
                       :href="tab.href"
                       @click="selectTab(tab);">
                        <div class="flex flex-col"
                             :title="tab.name">
                            <i :class="tab.icon"
                               class="fas text-2xl align-middle"
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
