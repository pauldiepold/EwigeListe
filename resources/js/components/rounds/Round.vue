<template>
  <div>
    <tabs>
      <tab name="Runde" icon="fa-play-circle" :selected="true">
      </tab>
      <tab name="Live" icon="fa-dice" :selected="false">

      </tab>

      <tab name="Statistiken" icon="fa-chart-area" :selected="false">

      </tab>

      <tab name="Listen" icon="fa-list-alt">

      </tab>

      <tab name="Kommentare" icon="fa-comments">

      </tab>


    </tabs>

  </div>
</template>

<script>
export default {
  props: [],
  data() {
    return {
      show: false
    }
  }
};
</script>
