<template>
  <div class="mb-12">
    <form @submit.prevent="storeExpe()">
      <v-row class="py-3">
        <v-col cols="12" sm="12" md="10" lg="10">
          <h1 class="justify-start Montserrat-Bold ">
            Nuevo Expediente
          </h1>
        </v-col>
        <v-col cols="6" sm="12" md="2" lg="2">
          <div class="pb-3 alingText">
            <input-date :fecha="fecha" />
          </div>
        </v-col>
      </v-row>

      <v-divider color="#393B44" class="mb-10"></v-divider>

      <v-row no-gutters class="pb-4" justify="start">
        <v-col cols="12" sm="12" lg="6" class="pr-lg-2">
          <v-alert
            icon="mdi-information-outline orange--text"
            colored-border
            dense
            class="alert Montserrat-Regular pa-4 orange--text text-left pl-0"            
          >
           (*) Campo Obligatorio 
          </v-alert>
        </v-col>
      </v-row>

      <v-row no-gutters>
        <v-col cols="12" sm="12" lg="6" class="pr-lg-2">
          <label-error :texto="this.iniciador_id_error" />
        </v-col>
      </v-row>

      <v-row no-gutters justify="start">
        <v-col cols="12" sm="12" lg="6" class="pr-lg-2">
          <label-input texto="Iniciador *" />

          <!--
          <autocomplete-field
              nombre="nombre"
              label="email"
              @input="cargarExpediente"
              :data="$store.state.nuevo_expediente.iniciadores"
              :value="$store.state.nuevo_expediente.iniciadorSelected"
          ></autocomplete-field>
          />-->


          <autocomplete-field
            :data="allIniciadores"
            nombre="nombre"
            @input="cargarExpediente()"
            v-model="expe.iniciador_id"
        ></autocomplete-field>

        </v-col>

        <v-col cols="12" sm="12" lg="6" class="pl-lg-2">
          <label-input texto="Nº de Expediente" />
          <v-text-field
            class="Montserrat-Regular text-justify"
            color="amber accent-4"
            outlined
            readonly
            v-model="nroExpediente"
          >
          </v-text-field>
        </v-col>
      </v-row>

      <v-row no-gutters justify="start" class="py-3">
        <v-col cols="12" sm="12" lg="6" class="pr-lg-2">
          <LabelInput texto="Motivo del Expediente *" />

          <autocomplete-field
            @input="setMotivo"
            :data="motivoSinExtracto"
            nombre="descripcion"
            v-model="expe.tipo_exp_id"
          />
        </v-col>

        <v-col cols="12" sm="12" lg="6" class="pl-lg-2 pb-3">
          <v-alert
            icon="mdi-alert-outline"
            border="left"
            dense
            text
            type="warning"
            class="alert Montserrat-Regular pa-4 mt-10"
          >
            Seleccione un motivo de expediente para cargar la plantilla del
            extracto.
          </v-alert>
        </v-col>
      </v-row>

      <v-row no-gutters>
        <v-col cols="12" sm="12" lg="6" class="pr-lg-2">
          <label-error :texto="this.descripcion_extracto_error" />
        </v-col>
      </v-row>

      <v-row no-gutters justify="start">
        <v-col cols="12" class="pr-lg-2">
          <label-input texto="Extracto *" />
          <Extractos
            v-model="expe.descripcion_extracto"
            :tipo="expe.tipo_exp_id"
          />
        </v-col>
      </v-row>

      <v-row no-gutters justify="end">
        <v-col cols="12" sm="12" lg="6" class="pl-lg-2">
          <label-error :texto="this.nro_fojas_error" />
        </v-col>
      </v-row>

      <v-row no-gutters justify="center" class="pt-5">
        <v-col cols="12" sm="12" lg="6" class="pr-lg-2 pb-3">
          <label-input texto="Número SIIF" />
          <text-field tipo="number" />
        </v-col>
        <v-col cols="12" sm="12" lg="6" class="pl-lg-2 pb-3">
          <Label-input texto="Cantidad de Fojas *" />
          <text-field tipo="number" v-model="expe.nro_fojas" />
        </v-col>
      </v-row>

      <div class="mt-4">
        <label-input texto="Observaciones:" />
        <v-textarea
          v-model="expe.observacion"
          outlined
          name="textarea"
          color="amber accent-4"
        ></v-textarea>
      </div>

      <v-row no-gutters justify="start">
        <v-col cols="12" sm="12" lg="6">
          <label-error :texto="this.pase_a_error" />
        </v-col>
        <v-col cols="12" sm="12" lg="6" class="pl-lg-2">
          <label-error :texto="this.prioridad_error" />
        </v-col>
      </v-row>

      <v-row no-gutters justify="center">
        <v-col cols="12" sm="12" lg="6" class="pr-lg-2 pb-3">
          <label-error />
          <label-input texto="Pase a *" />

          <v-autocomplete
            class="Montserrat-Regular text-justify"
            color="amber accent-4"
            outlined
            item-value="id"
            single-line
            item-color="amber accent-4"
            :items="this.$store.getters.get_areas_all"
            item-text="nombre"
            v-model="expe.area_id"
          >
          </v-autocomplete>
        </v-col>

        <v-col cols="12" sm="12" lg="6" class="pl-lg-2 pb-6">
          <label-input texto="Seleccione la prioridad *" />
          <div class="d-flex column justify-center Montserrat-Semibold">
            <v-btn-toggle
              v-model="expe.prioridad"
              group
              class="justify-space-around"
            >
              <v-btn value="2" x-large class="px-sm-12">
                <v-icon large color="rgb(251, 140, 0, 0.7)" class="pr-2">
                  mdi-check-circle-outline
                </v-icon>
                Normal
              </v-btn>
              <v-btn value="1" x-large class="px-sm-12">
                <v-icon large color="rgb(244, 67, 54, 0.7)" class="pr-2">
                  mdi-alert-circle-outline
                </v-icon>
                Alta
              </v-btn>
            </v-btn-toggle>
          </div>
        </v-col>
      </v-row>

      <v-card color="#FFF5E6" class="pa-5">
        <label-input texto="Adjuntar Archivos al Pase" />
        <input type="file" multiple @change="handleFileUpload($event)" />
        <modal-error-tipo-archivo
          :show="showArchivoError"
          @close="closeModalErrorArchivo"
        />
      </v-card>
      <v-progress-linear
        :active="this.$store.getters.get_btn_creado"
        indeterminate
        color="yellow darken-3"
      ></v-progress-linear>

      <v-row no-gutters justify="center" class="py-16">
        <v-col cols="12" sm="6" md="6" lg="6" class="px-sm-2">
          <v-btn
            type="submit"
            class="pa-5 color Montserrat-SemiBold"
            height="55"
            elevation="0"
            color="#FACD89"
            block
            :disabled="this.$store.getters.get_btn_creado"
          >
            <v-icon class="px-5"> mdi-check-bold </v-icon>
            <div class="">Confirmar</div>
          </v-btn>
        </v-col>
      </v-row>
    </form>
    <v-overlay :value="this.$store.getters.get_btn_creado">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
    <modal-nuevos-expedientes
      :show="creado_exito"
      :dato="expediente_new"
      @close="cerrarModal"
    />
  </div>
</template>
<script>
import LabelInput from "../../components/LabelInput";
import Extractos from "../../components/Extractos/Extractos";
import { mapActions, mapGetters } from "vuex";
import InputDate from "../../components/InputDate";
import TextField from "../../components/TextField";
import AutocompleteField from "../../components/AutocompleteField";
import ModalNuevosExpedientes from "../../components/dialogs/ModalNuevosExpedientes";
import LabelError from "../../components/LabelError";
import ModalErrorTipoArchivo from "../../components/dialogs/ModalErrorTipoArchivo";

export default {
  name: "Home",
  components: {
    AutocompleteField,
    TextField,
    InputDate,
    LabelInput,
    Extractos,
    ModalNuevosExpedientes,
    LabelError,
    ModalErrorTipoArchivo,
  },
  data: () => ({
    radioGroup: 1,
    toggle_none: null,
    agregarIniciador: [
      { texto: "Agregar Iniciador", imagen: "./img/cards/ver-todos.svg" },
    ],
    motivo: [],
    showDetalle: false,
    files: "dasdas",
    loader: null,
    expe: {
      iniciador_id: "",
      nro_fojas: "",
      prioridad: "",
      tipo_exp_id: 0,
      observacion: "",
      descripcion_extracto: "",
      area_id: "",
      archivos: "",
    },
    showArchivoError: false,
  }),

  methods: {
    cargarExpediente() {
      this.nroExpedienteAleatorio(this.expe);
      this.capturarIniciador(this.expe.iniciador_id);
    },

    setMotivo() {
      this.capturarMotivo(this.expe.tipo_exp_id)
    },

    handleFileUpload(event) {
      this.files = event.target.files;
      for (var i = 0; i < this.files.length; i++) {
        if (event.target.files[i].type === "application/x-msdownload") {
          this.showArchivoError = !this.showArchivoError;
          event.target.value = null;
        }
      }
    },

    storeExpe() {
      let formData = new FormData();

      for (var i = 0; i < this.files.length; i++) {
        let file = this.files[i];

        formData.append("archivo" + i + "", file);
      }

      let cantidad = this.files.length.toString();
      formData.append("iniciador_id", this.expe.iniciador_id);
      formData.append("nro_fojas", this.expe.nro_fojas);
      formData.append("observacion", this.expe.observacion);
      formData.append("nro_expediente", this.nroExpediente);
      formData.append("prioridad_id", this.expe.prioridad);
      formData.append("tipo_exp_id", this.expe.tipo_exp_id);
      formData.append("descripcion_extracto", this.extracto);
      formData.append("area_id", this.expe.area_id);
      formData.append("archivos_length", cantidad);

      this.storeExpediente(formData);
    },

    closeModalErrorArchivo() {
      this.showArchivoError = false;
    },

    ...mapActions([
      "getCreate",
      "storeExpediente",
      "nroExpedienteAleatorio",
      "cerrarModal",
      "capturarIniciador",
        'capturarMotivo',
    ]),
  },

  computed: {
    ...mapGetters([
        'get_motivo_selected',
        'get_iniciadorSelected',
      'creado_exito',
      "extracto",
      "allIniciadores",
      "fecha",
      "motivoSinExtracto",

      "expediente",
      "getTipoUsuario",
      "get_areas_all",
      "descripcion_extracto_error",
      "iniciador_id_error",
      "nro_fojas_error",
      "prioridad_error",
      "pase_a_error",
      "expediente_new",

      "get_btn_creado",
    ]),


    //si el valor de nro expediente cambia, se ejecuta la funcion y el componente reacciona a los cambios


    nroExpediente: {
      get() {
        return this.$store.getters.nro_expediente;
      },
    },

    extracto: {
      get() {
        return this.$store.getters.extracto;
      },
    },
  },

  mounted() {
    this.getCreate();
  },
};
</script>
<style>

.radioFont {
  font-family: "Montserrat-Bold";
  font-size: 18px;
}
.size {
  font-size: 22px;
}
.alert {
  font-size: 13px;
}
@media (min-width: 1100px) {
  .alingText {
    text-align: end;
  }
}
@media (max-width: 859px) {
  .alert {
    font-size: 12px;
  }
}
@media (max-width: 768px) {
  .alert {
    font-size: 11px;
  }
}
</style>
