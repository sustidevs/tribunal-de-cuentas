<template>
  <v-dialog v-model="show" width="1200px" content-class="round" persistent>
    <v-card class="px-7 pt-1">
      <v-row class="mt-5 mb-2">
        <v-col cols="10">
          <h2 class="Montserrat-Bold text-justify">Expediente N° {{ this.datos.nro_expediente }}</h2>
        </v-col>
        <v-col cols="2" align="right">
          <v-icon color="#393B44" large>mdi-file-document</v-icon>
        </v-col>
      </v-row>
      <v-divider color="#393B44" class="mt-2"></v-divider>

      <v-row no-gutters align="start" class="mt-5">
        <div class="textHereSmall d-flex flex-column Montserrat-Bold mb-2 mr-2">
          Extracto:
        </div>
        <div
          class="textHereSmall d-flex flex-column Montserrat-SemiBold text-justify"
        >
          {{ this.datos.extracto }}
        </div>
      </v-row>

      <v-row no-gutters align="start" class="pt-6">
        <v-col>
          <div class="d-flex">
            <div class="textHereSmall Montserrat-Bold mr-1">Iniciador:</div>
            <div class="textHereSmall Montserrat-SemiBold ml-1">
              {{ this.datos.iniciador }}
            </div>
          </div>
        </v-col>
        <v-col>
          <div class="d-flex">
            <div class="textHereSmall Montserrat-Bold mr-1">
              Fecha de creación:
            </div>
            <div class="textHereSmall Montserrat-SemiBold ml-1">
              {{ this.datos.fecha_creacion }}
            </div>
          </div>
        </v-col>
      </v-row>

      <v-row no-gutters align="start" class="mt-5">
        <div class="textHereSmall d-flex flex-column Montserrat-Bold mb-2 mr-2">
          Observaciones:
        </div>
        <div
          class="textHereSmall d-flex flex-column Montserrat-SemiBold text-justify"
        >
          {{ this.datos.observacion }}
        </div>
      </v-row>
      
      <v-divider color="#c2c3cc" class="mt-2"></v-divider>

      <v-row no-gutters align="start" class="mt-5" v-if="getArea == 'DPTO. MESA DE ENTRADAS Y SALIDAS' ">
        <div class="textHereSmall d-flex flex-column Montserrat-Bold mb-2 mr-2">
          A afectos de:
        </div>
        <div
          class="textHereSmall d-flex flex-column Montserrat-SemiBold text-justify"
        >
          {{ this.datos.motivo[0].motivo }}
        </div>
      </v-row>

      <v-row no-gutters align="start" class="mt-5" v-else>
        <div class="textHereSmall d-flex flex-column Montserrat-Bold mb-2 mr-2">
          A afectos de:
        </div>
        <div
          class="textHereSmall d-flex flex-column Montserrat-SemiBold text-justify"
        >
          {{ this.datos.observacion_pase }}
        </div>
      </v-row>

      <v-row no-gutters align="start" class="mt-5">
        <v-col>
          <div class="d-flex">
            <div class="textHereSmall Montserrat-Bold mr-2">
              Archivos adjuntos:
            </div>
            <div v-if="this.datos.archivo !== null">
              <v-chip
                class="textHereSmall Montserrat-Regular"
                @click="getArchiv()"
              >
                Descargar</v-chip
              >
            </div>
            <div
              v-else
              class="textHereSmall d-flex flex-column Montserrat-SemiBold text-justify"
            >
              No se han cargado archivos
            </div>
          </div>
        </v-col>
      </v-row>

      <v-row no-gutters justify="center" class="mt-8">
        <v-col cols="12" sm="6" md="6" lg="6" class="py-6 px-sm-2">
          <v-btn
            @click="close"
            class="pa-5 color Montserrat-SemiBold"
            height="55"
            elevation="0"
            color="#FACD89"
            block
          >
            <v-icon class="px-5"> mdi-check-bold </v-icon>
            Cerrar
          </v-btn>
        </v-col>
      </v-row>
    </v-card>
  </v-dialog>
</template>
<script>
import { mapActions, mapGetters } from "vuex";
export default {
  name: "ModalVerDetalleExp",
  props: {
    show: { type: Boolean, default: false },
    datos: Object,
  },

  computed: mapGetters(["allExpedientes", "get_archivos", "getArea"]),

  methods: {
    ...mapActions(["getExpedientes", "getArchivos"]),

    getArchiv() {
      let files = {
        id: this.datos.expediente_id,
        download: true,
        nro_expediente: this.datos.nro_expediente,
      };
      this.getArchivos(files);
    },
    close() {
      this.$emit("close");
    },
  },
};
</script>
<style>
.round {
  border-radius: 30px;
}

.descargar {
  font-size: 18px !important;
}
</style>
