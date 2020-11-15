<template>
    <div style="min-height: 50vh;"
         v-touch:swipe.left="swipeLeft"
         v-touch:swipe.right="swipeRight">
        <div class="tw-sticky tw-z-50 tw-pt-2 tw--mx-4 tw-flex tw-justify-center"
             style="top: 48px; background-color: #f1f1ef;">
            <ul class="tw-flex-1 tw-flex tw-justify-around tw-border-b tw-border-gray-400 tw-max-w-md">
                <li class="tw-mx-1" v-for="tab in tabs">
                    <a class="tw-min-w-7xs tw-inline-block tw-py-2 tw-text-orange-600 tw-w-100 tw-no-underline hover:tw-no-underline "
                       :class="{ 'tw--mx-px tw--mt-px tw-border-l tw-border-t tw-border-r tw-rounded-t-lg tw-border-gray-400 tw-text-orange-700': tab.isActive }"
                       :href="tab.href"
                       @click="selectTab(tab)">
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
                selectedTab: ''
            };
        },

        created() {
            this.tabs = this.$children;
        },

        mounted() {
            let self = this;
            self.tabs.forEach(function (tab) {
                if (tab.isActive === true) {
                    self.selectedTab = tab;
                }
            });
        },

        methods: {
            selectTab(selectedTab) {
                this.tabs.forEach(tab => {
                    tab.isActive = (tab.href === selectedTab.href);
                    tab.tabKey++;
                });

                this.selectedTab = selectedTab;
            },
            swipeLeft() {
                this.selectTab(this.tabs[this.nextIndex('right')]);
            },
            swipeRight() {
                this.selectTab(this.tabs[this.nextIndex('left')]);
            },
            currentIndex() {
                return this.tabs.indexOf(this.selectedTab);
            },
            nextIndex(direction) {
                let length = this.tabs.length;
                let index = this.currentIndex();

                if (direction === 'left') {
                    if (index > 0) {
                        return index - 1;
                    } else if (index === 0) {
                        return length - 1;
                    }
                } else if (direction === 'right') {
                    if (index < (length - 1)) {
                        return index + 1;
                    } else if (index === length - 1) {
                        return 0;
                    }
                }
            }
        }
    };
</script>
