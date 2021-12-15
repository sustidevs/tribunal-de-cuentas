<template>
    <div v-if="this.texto" class="Montserrat-Bold red--text fontError text-start py-2 pt-5">

      <div v-if="!(this.texto === undefined)">
        {{ fullError }}
      </div>

    </div>
</template>

<script>
export default {
  props: {
    texto: String ,
  },

  computed: {
    fullError: function () {
      return this.texto.toString()
    },
  }

}
</script>

<style>
.fontError{
  font-size: 20px !important;
}
</style>