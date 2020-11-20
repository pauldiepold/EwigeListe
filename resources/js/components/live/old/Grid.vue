<template>


    <div class="tw-grid tw-grid-cols-livegame tw-grid-rows-livegame lg:tw-grid-rows-livegame_lg">

        <div class="tw-col-start-1 tw-row-start-2 tw-col-span-3 lg:tw-col-span-1 tw-flex tw-flex-col tw-justify-around tw-items-center tw-p-4">

            <slot name="center"></slot>

        </div>

        <!-- ******** Hand ********* -->
        <div class="tw-col-start-2 tw-row-start-3 tw-col-span-2 lg:tw-col-start-2 lg:tw-row-start-4 lg:tw-col-span-3 tw-flex tw-items-center tw-p-4">

            <slot name="hand"></slot>

        </div>

        <!-- ******** Ich ********* -->
        <div class="tw-col-start-1 tw-row-start-3 lg:tw-col-start-2 tw-flex tw-items-center tw-justify-center tw-py-4">

            <slot name="spieler0"></slot>

        </div>

        <!-- ******** Spieler 1 ********* -->
        <div class="tw-col-start-1 tw-row-start-1 lg:tw-row-start-2 tw-flex tw-items-center tw-justify-center">

            <slot name="spieler1"></slot>

        </div>

        <!-- ******** Spieler 2 ********* -->
        <div class="tw-col-start-2 tw-row-start-1 tw-flex tw-items-center tw-justify-center">

            <slot name="spieler2"></slot>

        </div>

        <!-- ******** Spieler 3 ********* -->
        <div class="tw-col-start-3 tw-row-start-1 lg:tw-row-start-2 tw-flex tw-items-center tw-justify-center">

            <slot name="spieler3"></slot>

        </div>
    </div>

</template>

<script>
    export default {};
</script>
