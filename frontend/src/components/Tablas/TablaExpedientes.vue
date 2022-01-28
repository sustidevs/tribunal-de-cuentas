<template>
  <div>

    <v-row>
      <v-col
          cols="12"
          sm="4"
      >
        <v-text-field
            color="#8D93AB"
            v-model="search"
            append-icon="mdi-magnify"
            label="Buscar"
            hide-details
            outlined
            class="py-6"
        />
      </v-col>
    </v-row>


      <v-data-table
          :headers="headers"
          :items="data"
          :search="search"
          :items-per-page="5"
          disable-sort
          mobile-breakpoint="300"
          class="elevation-1 mytable"
          loading-text="Cargando expedientes. Por favor, espere."
          :loading="loading"
          no-data-text="No tienes Expedientes"
      >

        <template v-slot:item.prioridad="{ item }">
          <v-chip
              :color="getColor(item.prioridad)"
          >
            <v-icon size="20px" class="mr-1">{{getIcon(item.prioridad)}}</v-icon><h5 class="font-weight-regular">{{ item.prioridad }}</h5>
          </v-chip>
        </template>

        <template v-slot:item.action1="{ }">
          <v-btn @click="downloadPDF()" fab small color="#FACD89" depressed>
            <v-icon> mdi-download </v-icon>
          </v-btn>
        </template>

        <template v-slot:item.action2="{ item }">
          <v-btn @click="historial_expediente(item)" fab small color="#FACD89" depressed>
            <v-icon> mdi-eye </v-icon>
          </v-btn>
        </template>
      </v-data-table>

      <modal-caratula
        :show="show"
        :dato="get_todos_expedientes"
      />
  </div>
</template>

<script>
import {mapActions} from "vuex";
import ModalCaratula from "../../components/dialogs/ModalCaratula";

export default {

  components: {
    ModalCaratula
  },
  props: {
    headers: Array,
    data: Array,
    loading: {type: Boolean, default: false},
  },

  data () {
    return {
      selected:[],
      search: '',
      show: false,
    }
  },

  methods: {
    getColor (prioridades) {
      if (prioridades === 'alta') return 'red lighten-3'
      if (prioridades === 'normal') return 'grey lighten-2'
    },

    getIcon (prioridades) {
      if (prioridades === 'alta') return 'mdi-exclamation-thick'
      else return 'mdi-check-bold'
    },

    ...mapActions([
      'historial_expediente', 'caratula'
    ]),

    downloadPDF () {
      this.$refs.DownloadComp.generatePdf()
      this.show = true
    },
  }
}
</script>

<style>
.v-data-table > .v-data-table__wrapper > table > thead > tr > th > span {
  font-size: 19px !important;
}

.mytable thead {
  background-color: #facd89 !important;
  font-family: "Montserrat-Regular",serif !important;
}

.v-data-table > .v-data-table__wrapper > table > tbody > tr > td {
  font-family: "Montserrat-Regular",serif !important;
  font-size: 17px !important;
  padding: 12px !important;
}

.v-data-table > .v-data-table__wrapper > table > tbody > tr:hover{
  background-color: #FAE3BF !important;
}

</style>