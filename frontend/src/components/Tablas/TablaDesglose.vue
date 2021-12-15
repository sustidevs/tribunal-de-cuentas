<template>
  <div class="pb-12">
    <v-row>
      <v-col cols="12" lg="4">
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
    <v-row class="mb-16">
      <v-col cols="12" lg="7">
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
            no-data-text="No tienes expedientes englosados"
        >
          <template v-slot:item.action="{ item }">
            <v-btn @click="verHijos(item)" fab small color="#FACD89" depressed>
              <v-icon>mdi-arrow-right-thick</v-icon>
            </v-btn>
          </template>
        </v-data-table>
      </v-col>

      <v-col cols="12" lg="5">
        <v-toolbar height="59px"
            color="#FACD89"
            class="contentSize Montserrat-SemiBold text--darken-3 grey--text"
        >
          Expediente seleccionado
        </v-toolbar>
        <v-card class="mx-auto" tile>
          <v-list class="pa-4">
            <v-list-item-title class="contentSize Montserrat-SemiBold">
              <v-icon large color="#FACD89">mdi-file</v-icon> Expediente principal: <strong>{{ exp_padreSeleccionado.nro_expediente }}</strong>
              <v-list-item-subtitle class="contentSize Montserrat-Regular mt-2 ml-10" v-text="exp_padreSeleccionado.extracto"/>
            </v-list-item-title>

            <v-divider class="my-4"></v-divider>

            <v-list-item-title class="contentSize Montserrat-SemiBold mb-2">
              <v-icon large color="#FACD89">mdi-file</v-icon> Expedientes englosados:
            </v-list-item-title>

            <v-list-item class="ml-6" v-for="item in getExpedientesHijos" :key="item.id">
              <v-list-item-content>
                <v-list-item-title class="contentSize Montserrat-SemiBold" v-text="item.nro_expediente"/>
              </v-list-item-content>
            </v-list-item>
          </v-list>

          <v-row justify="center" class="contentSize Montserrat-Regular py-6" align="center">
            <v-btn @click="confirmarDesglose" :disabled="!exp_padreSeleccionado.nro_expediente" class="pa-1 color Montserrat-SemiBold px-6" height="50" elevation="0" color="#FACD89">
              <v-icon class="px-2">
                mdi-check-bold
              </v-icon>
                Desglosar
            </v-btn>
          </v-row>
        </v-card>
      </v-col>
    </v-row>
    <modal-exito-desglose :show="show"/>
  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import ModalExitoDesglose from '../../components/dialogs/ModalExitoDesglose';

export default {
  components:{ModalExitoDesglose},

  props: {
    headers: Array,
    data: Array,
    loading: {type: Boolean, default: false},
  },

  data() {
    return {
      seleccionados: [],
      search: "",
      exp_padreSeleccionado: '',
      id_padre: '',
      show: false,
    };
  },

  computed: mapGetters(['getExpedientesHijos','getExpedientesPadres']),

  methods: {
    ...mapActions([
      "desglosarVerHijos",
        'desglose'
    ]),

    confirmarDesglose(){
      let expedientes_englose = {
        exp_padre: this.id_padre,
        user_id:  this.$store.getters.getIdUser
      }
      this.desglose(expedientes_englose)
      this.show = true
    },

    quitar (item){
      this.seleccionados.splice(item)
    },

    verHijos: function (item) {
      this.id_padre = item.id,

      this.exp_padreSeleccionado = {
        nro_expediente: item.nro_expediente,
        extracto: item.extracto,
      };

      let expedientePase = {
        exp_padre : item.id
      }
      this.desglosarVerHijos(expedientePase)
    },
  },
};
</script>

<style>
.v-data-table > .v-data-table__wrapper > table > thead > tr > th > span {
  font-size: 19px !important;
}

.mytable thead {
  background-color: #facd89 !important;
  font-family: "Montserrat-Regular", serif !important;
}

.v-data-table > .v-data-table__wrapper > table > tbody > tr > td {
  font-family: "Montserrat-Regular", serif !important;
  font-size: 17px !important;
}

.v-data-table > .v-data-table__wrapper > table > tbody > tr:hover {
  background-color: #fae3bf !important;
}

.subSize{
  font-size: 22px ;
}

.contentSize{
  font-size: 18px;
}
</style>
