<template>
    <div class="container">
      <!-- Loading Overlay -->
      <div v-if="loading" class="position-fixed start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0, 0, 0, 0.5); z-index: 1050; top: 0px;">
        <div class="spinner-border text-light" role="status">
          <span class="visually-hidden"></span>
        </div>
      </div>

      <!-- Water Service Modal -->
      <div v-if="showWaterModal" class="modal" @click.self="closeModal">
        <div class="modal-content" style="max-width: 900px;">
          <div class="modal-header">
            <span class="modal-title">💧 Factura de Agua</span>
            <button class="close-button" @click="closeModal">×</button>
          </div>
          <div class="modal-body p-4" style="overflow-y: auto;">
            <div class="row">
              <div class="col-12">
                <div class="contenedor-campos grupo-campos">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="campo">
                        <label class="etiqueta">Fecha Inicio</label>
                        <input type="date" class="entrada" v-model="waterForm.start_date" placeholder="Fecha Inicio">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="campo">
                        <label class="etiqueta">Fecha Fin</label>
                        <input type="date" class="entrada" v-model="waterForm.end_date" placeholder="Fecha Fin">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="campo">
                        <label class="etiqueta">Fecha Vencimiento</label>
                        <input type="date" class="entrada" v-model="waterForm.due_date" placeholder="Fecha Vencimiento">
                      </div>
                    </div>
                  </div>

                  <h4 class="subtitulo mt-5">Consumo</h4>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="campo">
                        <label class="etiqueta">Metros Cúbicos FO-APTO-201</label>
                        <input type="number" class="entrada" v-model="waterForm.metros_cubicos_201" placeholder="Metros Cúbicos FO-APTO-201">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="campo">
                        <label class="etiqueta">Metros Cúbicos FO-APTO-301</label>
                        <input type="number" class="entrada" v-model="waterForm.metros_cubicos_301" placeholder="Metros Cúbicos FO-APTO-301">
                      </div>
                    </div>
                  </div>

                  <h4 class="subtitulo mt-5">Acueducto</h4>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="campo">
                        <label class="etiqueta">Cargo Fijo</label>
                        <input type="number" class="entrada" v-model="waterForm.acueducto_cargo_fijo" placeholder="Cargo Fijo">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="campo">
                        <label class="etiqueta">Consumo Básico (Cantidad)</label>
                        <input type="number" class="entrada" v-model="waterForm.acueducto_consumo_residencial_basico_quantity" placeholder="Consumo Básico (Cantidad)">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="campo">
                        <label class="etiqueta">Consumo Básico (Subtotal)</label>
                        <input type="number" class="entrada" v-model="waterForm.acueducto_consumo_residencial_basico_subtotal"  placeholder="Consumo Básico (Subtotal)">
                      </div>
                    </div>
                    <div class="col-md-6 mt-3">
                      <div class="campo">
                        <label class="etiqueta">Consumo Superior (Cantidad)</label>
                        <input type="number" class="entrada" v-model="waterForm.acueducto_consumo_residencial_superior_quantity" placeholder="Consumo Superior (Cantidad)">
                      </div>
                    </div>
                    <div class="col-md-6 mt-3">
                      <div class="campo">
                        <label class="etiqueta">Consumo Superior (Subtotal)</label>
                        <input type="number" class="entrada" v-model="waterForm.acueducto_consumo_residencial_superior_subtotal" placeholder="Consumo Superior (Subtotal)">
                      </div>
                    </div>
                  </div>

                  <h4 class="subtitulo mt-5">Alcantarillado</h4>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="campo">
                        <label class="etiqueta">Cargo Fijo</label>
                        <input type="number" class="entrada" v-model="waterForm.alcantarillado_cargo_fijo"  placeholder="Cargo Fijo">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="campo">
                        <label class="etiqueta">Consumo Básico (Cantidad)</label>
                        <input type="number" class="entrada" v-model="waterForm.alcantarillado_consumo_residencial_basico_quantity" placeholder="Consumo Básico (Cantidad)">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="campo">
                        <label class="etiqueta">Consumo Básico (Subtotal)</label>
                        <input type="number" class="entrada" v-model="waterForm.alcantarillado_consumo_residencial_basico_subtotal" placeholder="Consumo Básico (Subtotal)">
                      </div>
                    </div>
                    <div class="col-md-6 mt-3">
                      <div class="campo">
                        <label class="etiqueta">Consumo Superior (Cantidad)</label>
                        <input type="number" class="entrada" v-model="waterForm.alcantarillado_consumo_residencial_superior_quantity" placeholder="Consumo Superior (Cantidad)">
                      </div>
                    </div>
                    <div class="col-md-6 mt-3">
                      <div class="campo">
                        <label class="etiqueta">Consumo Superior (Subtotal)</label>
                        <input type="number" class="entrada" v-model="waterForm.alcantarillado_consumo_residencial_superior_subtotal" placeholder="Consumo Superior (Subtotal)">
                      </div>
                    </div>
                  </div>

                  <div class="campo mt-4">
                    <label class="etiqueta">Recibo (PDF)</label>
                    <input type="file" class="entrada" @change="handleWaterFileUpload">
                  </div>
                </div>
              </div>
            </div>

            <div class="modal-footer mt-0">
              <button type="button" class="boton-secundario" @click="closeModal">
                Cerrar
              </button>
              <button type="button" class="boton-primario" @click="createWaterBill" :disabled="creatingBill" style="width: auto;">
                {{ creatingBill ? 'Creando...' : 'Crear Factura' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Gas Service Modal -->
      <div v-if="showGasModal" class="modal" @click.self="closeModal">
        <div class="modal-content" style="max-width: 900px;">
          <div class="modal-header">
            <span class="modal-title">🔥 Factura de Gas</span>
            <button class="close-button" @click="closeModal">×</button>
          </div>
          <div class="modal-body p-4" style="overflow-y: auto;">
            <div class="row">
              <div class="col-12">
                <div class="contenedor-campos grupo-campos">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="campo">
                        <label class="etiqueta">Fecha Inicio</label>
                        <input type="date" class="entrada" v-model="gasForm.start_date" placeholder="Fecha Inicio">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="campo">
                        <label class="etiqueta">Fecha Fin</label>
                        <input type="date" class="entrada" v-model="gasForm.end_date" placeholder="Fecha Fin">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="campo">
                        <label class="etiqueta">Fecha Vencimiento</label>
                        <input type="date" class="entrada" v-model="gasForm.due_date" placeholder="Fecha Vencimiento">
                      </div>
                    </div>
                  </div>

                  <div class="row mt-4">
                    <div class="col-md-4">
                      <div class="campo">
                        <label class="etiqueta">Total</label>
                        <input type="number" class="entrada" v-model="gasForm.total" placeholder="Total">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="campo">
                        <label class="etiqueta">Cabezas FO-APTO-201</label>
                        <input type="number" class="entrada" v-model="gasForm.heads_201" placeholder="Cabezas FO-APTO-201">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="campo">
                        <label class="etiqueta">Cabezas FO-APTO-301</label>
                        <input type="number" class="entrada" v-model="gasForm.heads_301" placeholder="Cabezas FO-APTO-301">
                      </div>
                    </div>
                  </div>

                  <div class="campo mt-4">
                    <label class="etiqueta">Recibo (PDF)</label>
                    <input type="file" class="entrada" @change="handleGasFileUpload" >
                  </div>
                </div>
              </div>
            </div>

            <div class="modal-footer mt-0">
              <button type="button" class="boton-secundario" @click="closeModal">
                Cerrar
              </button>
              <button type="button" class="boton-primario" @click="createGasBill" :disabled="creatingBill" style="width: auto;">
                {{ creatingBill ? 'Creando...' : 'Crear Factura' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Electricity Service Modal -->
      <div v-if="showElectricityModal" class="modal" @click.self="closeModal">
        <div class="modal-content" style="max-width: 900px;">
          <div class="modal-header">
            <span class="modal-title">💡 Factura de Luz</span>
            <button class="close-button" @click="closeModal">×</button>
          </div>
          <div class="modal-body p-4" style="overflow-y: auto;">
            <div class="row">
              <div class="col-12">
                <div class="contenedor-campos grupo-campos">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="campo">
                        <label class="etiqueta">Fecha Inicio</label>
                        <input type="date" class="entrada" v-model="electricityForm.start_date" placeholder="Fecha Inicio">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="campo">
                        <label class="etiqueta">Fecha Fin</label>
                        <input type="date" class="entrada" v-model="electricityForm.end_date" placeholder="Fecha Fin">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="campo">
                        <label class="etiqueta">Fecha Vencimiento</label>
                        <input type="date" class="entrada" v-model="electricityForm.due_date" placeholder="Fecha Vencimiento">
                      </div>
                    </div>
                  </div>

                  <div class="row mt-4">
                    <div class="col-md-6">
                      <div class="campo">
                        <label class="etiqueta">Total</label>
                        <input type="number" class="entrada" v-model="electricityForm.total" placeholder="Total">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="campo">
                        <label class="etiqueta">Aseo</label>
                        <input type="number" class="entrada" v-model="electricityForm.aseo" placeholder="Aseo">
                      </div>
                    </div>
                    <div class="col-md-4 mt-4">
                      <div class="campo">
                        <label class="etiqueta">Cabezas FO-APTO-201</label>
                        <input type="number" class="entrada" v-model="electricityForm.heads_201" placeholder="Cabezas FO-APTO-201">
                      </div>
                    </div>
                    <div class="col-md-4 mt-4">
                      <div class="campo">
                        <label class="etiqueta">Cabezas FO-APTO-301</label>
                        <input type="number" class="entrada" v-model="electricityForm.heads_301" placeholder="Cabezas FO-APTO-301">
                      </div>
                    </div>
                    <div class="col-md-4 mt-4">
                      <div class="campo">
                        <label class="etiqueta">Cabezas FO-APTO-401</label>
                        <input type="number" class="entrada" v-model="electricityForm.heads_401" placeholder="Cabezas FO-APTO-401">
                      </div>
                    </div>

                    <div class="campo mt-4">
                      <label class="etiqueta">Recibo (PDF)</label>
                      <input type="file" class="entrada" @change="handleLuzFileUpload" >
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal-footer mt-0">
              <button type="button" class="boton-secundario" @click="closeModal">
                Cerrar
              </button>
              <button type="button" class="boton-primario" @click="createElectricityBill" :disabled="creatingBill" style="width: auto;">
                {{ creatingBill ? 'Creando...' : 'Crear Factura' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="row seccion mainSeccion mt-4">
        <!-- Header -->
        <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
          <h2 class="titulo-seccion mb-2 mb-md-0" style="margin: 0;">🔧 Servicios</h2>
          <div class="d-flex gap-2">
            <button class="boton-primario d-flex align-items-center" @click="openWaterModal" style="height: 55px;">
              <i class="fas fa-tint "></i> 
              <span class="d-none d-md-inline ms-2">Agua</span>
            </button>
            <button class="boton-primario d-flex align-items-center" @click="openGasModal" style="height: 55px;">
              <i class="fas fa-fire"></i> 
              <span class="d-none d-md-inline ms-2">Gas</span>
            </button>
            <button class="boton-primario d-flex align-items-center" @click="openElectricityModal" style="height: 55px;">
              <i class="fas fa-bolt"></i> 
              <span class="d-none d-md-inline ms-2">Luz</span>
            </button>
          </div>
        </div>

        <!-- Filters -->
        <div class="col-12 mb-2">
          <div class="row">
            <div class="col-md-6 mb-3">
              <select class="entrada" v-model="propertyFilter" style="background-color: #f8f5f0;">
                <option value="">Todas las Propiedades</option>
                <option v-for="property in properties" :key="property" :value="property.sku">{{ property.sku }}</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <select class="entrada" v-model="serviceFilter" style="background-color: #f8f5f0;">
                <option value="">Todos los Servicios</option>
                <option value="Agua">Agua</option>
                <option value="Gas">Gas</option>
                <option value="Luz">Luz</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Services Table -->
        <div class="col-12">
          <div style="overflow-x: auto;">
            <table class="table table-bordered table-striped table-hover align-middle text-center custom-big-table" style="border-radius: 25px; overflow: hidden;">
              <thead style="background-color: #8f754f; color: white;">
                <tr>
                  <th>Propiedad</th>
                  <th>Servicio</th>
                  <th>Identificador</th>
                  <th>Fecha Revisión Periódica</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(service, index) in paginatedServices" :key="index">
                  <td>{{ service.Propiedad }}</td>
                  <td>{{ service.Servicio }}</td>
                  <td>{{ service.Identificador }}</td>
                  <td>{{ service['Fecha Revision Periodica'] ? formatDate(service['Fecha Revision Periodica']) : 'N/A' }}</td>
                </tr>
                <tr v-if="filteredServices.length === 0">
                  <td colspan="4" class="text-center py-4">No se encontraron servicios</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Pagination -->
        <div class="col-12 mt-3">
          <nav>
            <ul class="pagination">
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <a class="page-link" @click="changePage(currentPage - 1)" href="#"><i class="fa fa-arrow-left"></i></a>
              </li>
              <li class="page-item"
                  v-for="page in totalPages"
                  :key="page"
                  :class="{ active: currentPage === page }">
                <a class="page-link" @click="changePage(page)" href="#">{{ page }}</a>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === totalPages || totalPages === 0 }">
                <a class="page-link" @click="changePage(currentPage + 1)" href="#"><i class="fa fa-arrow-right"></i></a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </template>

  <script>

    import billPayment from '@/api/billPayment.js';
    const billPaymentResource = new billPayment();
    import { useStore } from 'vuex';
    import property from '@/api/property.js';
  import Swal from 'sweetalert2';
  import { mapGetters } from 'vuex';

  export default {
    data() {
      return {
        loading: false,
        creatingBill: false,
        showWaterModal: false,
        showGasModal: false,
        showElectricityModal: false,
        propertyFilter: '',
        serviceFilter: '',
        currentPage: 1,
        itemsPerPage: 10,
        properties: [],
        services: [
          {
            "Propiedad": "FO-APTO-301",
            "Servicio": "Agua",
            "Identificador": 38475
          },
          {
            "Propiedad": "SE-APTO-201",
            "Servicio": "Gas",
            "Identificador": 12930,
            "Fecha Revision Periodica": "2025-02-18"
          },
          {
            "Propiedad": "PE-APTO-101",
            "Servicio": "Luz",
            "Identificador": 58291
          },
          {
            "Propiedad": "FO-APTO-201",
            "Servicio": "Gas",
            "Identificador": 84720,
            "Fecha Revision Periodica": "2024-11-03"
          },
          {
            "Propiedad": "FO-APTO-301",
            "Servicio": "Luz",
            "Identificador": 73928
          },
          {
            "Propiedad": "SE-APTO-201",
            "Servicio": "Agua",
            "Identificador": 20495
          },
          {
            "Propiedad": "PE-APTO-101",
            "Servicio": "Gas",
            "Identificador": 98234,
            "Fecha Revision Periodica": "2025-01-10"
          }
        ],
        waterForm: {
          start_date: '',
          end_date: '',
          due_date: '',
          metros_cubicos_201: null,
          metros_cubicos_301: null,
          acueducto_cargo_fijo: null,
          acueducto_consumo_residencial_basico_quantity: null,
          acueducto_consumo_residencial_basico_subtotal: null,
          acueducto_consumo_residencial_superior_quantity: null,
          acueducto_consumo_residencial_superior_subtotal: null,
          alcantarillado_cargo_fijo: null,
          alcantarillado_consumo_residencial_basico_quantity: null,
          alcantarillado_consumo_residencial_basico_subtotal: null,
          alcantarillado_consumo_residencial_superior_quantity: null,
          alcantarillado_consumo_residencial_superior_subtotal: null,
          bill_doc: null
        },
        gasForm: {
          start_date: '',
          end_date: '',
          due_date: '',
          total: null,
          heads_201: null,
          heads_301: null,
          bill_doc: null
        },
        electricityForm: {
          start_date: '',
          end_date: '',
          due_date: '',
          total: null,
          aseo: null,
          heads_201: null,
          heads_301: null,
          heads_401: null,
          bill_doc: null
        }
      }
    },
    computed: {
      ...mapGetters(['getProperties']),
      filteredServices() {
        return this.services.filter(service => {
          const matchesProperty = !this.propertyFilter ||
            service.Propiedad === this.propertyFilter;
          const matchesService = !this.serviceFilter ||
            service.Servicio === this.serviceFilter;
          return matchesProperty && matchesService;
        });
      },
      totalPages() {
        return Math.ceil(this.filteredServices.length / this.itemsPerPage);
      },
      paginatedServices() {
        const start = (this.currentPage - 1) * this.itemsPerPage;
        return this.filteredServices.slice(start, start + this.itemsPerPage);
      }
    },
    created() {
      this.fetchProperties();
    },
    methods: {
      async fetchProperties() {
        this.loading = true;
        this.properties = this.getProperties;

        if (this.properties.length === 0) {
          try {
            const propertyResource = new property();
            const response = await propertyResource.getAllProperties();

            if (response && response.success) {
              this.$store.commit('setProperties', response.data);
              this.properties = this.getProperties;
              console.log(this.properties);
            } else {
              Swal.fire(
                'Error',
                'Hubo un error, intenta de nuevo',
                'error'
              );
            }
          } catch (error) {
            Swal.fire(
              'Error',
              'Hubo un error, intenta de nuevo',
              'error'
            );
          }
        }

        this.loading = false;
      },
      openWaterModal() {
        this.resetWaterForm();
        this.showWaterModal = true;
      },
      openGasModal() {
        this.resetGasForm();
        this.showGasModal = true;
      },
      openElectricityModal() {
        this.resetElectricityForm();
        this.showElectricityModal = true;
      },
      closeModal() {
        this.showWaterModal = false;
        this.showGasModal = false;
        this.showElectricityModal = false;
      },
      resetWaterForm() {
        this.waterForm = {
          start_date: '',
          end_date: '',
          due_date: '',
          metros_cubicos_201: null,
          metros_cubicos_301: null,
          acueducto_cargo_fijo: null,
          acueducto_consumo_residencial_basico_quantity: null,
          acueducto_consumo_residencial_basico_subtotal: null,
          acueducto_consumo_residencial_superior_quantity: null,
          acueducto_consumo_residencial_superior_subtotal: null,
          alcantarillado_cargo_fijo: null,
          alcantarillado_consumo_residencial_basico_quantity: null,
          alcantarillado_consumo_residencial_basico_subtotal: null,
          alcantarillado_consumo_residencial_superior_quantity: null,
          alcantarillado_consumo_residencial_superior_subtotal: null,
          bill_doc: null
        };
      },
      resetGasForm() {
        this.gasForm = {
          start_date: '',
          end_date: '',
          due_date: '',
          total: null,
          heads_201: null,
          heads_301: null,
          bill_doc: null
        };
      },
      resetElectricityForm() {
        this.electricityForm = {
          start_date: '',
          end_date: '',
          due_date: '',
          total: null,
          aseo: null,
          heads_201: null,
          heads_301: null,
          heads_401: null,
          bill_doc: null
        };
      },
      handleWaterFileUpload(event) {
        this.waterForm.bill_doc = event.target.files[0];
      },
      handleGasFileUpload(event) {
        this.gasForm.bill_doc = event.target.files[0];
      },
      handleLuzFileUpload(event) {
        this.electricityForm.bill_doc = event.target.files[0];
      },
      async createWaterBill() {

        if (
          !this.waterForm.start_date ||
          !this.waterForm.end_date ||
          !this.waterForm.due_date ||
          this.waterForm.metros_cubicos_201 == null ||
          this.waterForm.metros_cubicos_301 == null ||
          this.waterForm.acueducto_cargo_fijo == null ||
          this.waterForm.acueducto_consumo_residencial_basico_quantity == null ||
          this.waterForm.acueducto_consumo_residencial_basico_subtotal == null ||
          this.waterForm.acueducto_consumo_residencial_superior_quantity == null ||
          this.waterForm.acueducto_consumo_residencial_superior_subtotal == null ||
          this.waterForm.alcantarillado_cargo_fijo == null ||
          this.waterForm.alcantarillado_consumo_residencial_basico_quantity == null ||
          this.waterForm.alcantarillado_consumo_residencial_basico_subtotal == null ||
          this.waterForm.alcantarillado_consumo_residencial_superior_quantity == null ||
          this.waterForm.alcantarillado_consumo_residencial_superior_subtotal == null
        ) {
          Swal.fire(
              "Error",
              "Todos los campos deben ser completados con el formato correcto.",
              "error"
          );
          return;
        }

        this.creatingBill = true;
        await billPaymentResource.createWaterBill(this.waterForm)
            .then(response => {
                if(response && response.success){
                    Swal.fire(
                        "Exitó!",
                        "La factura de agua fue creada exitosamente",
                        "success"
                    );
                }else{
                    Swal.fire(
                        "Error",
                        "Hubo un error, intenta de nuevo",
                        "error"
                    );
                }
            })
            .catch(error => {
                Swal.fire(
                    "Error",
                    "Hubo un error, intenta de nuevo",
                    "error"
                );
            });

        this.creatingBill = false;
        this.closeModal();

      },
      async createGasBill() {

        if (    !this.gasForm.start_date ||    !this.gasForm.end_date ||    !this.gasForm.due_date ||    this.gasForm.total == null ||    this.gasForm.heads_201 == null ||    this.gasForm.heads_301 == null  ) {
          Swal.fire(
              "Error",
              "Todos los campos deben ser completados con el formato correcto.",
              "error"
          );
          return;
        }

        this.creatingBill = true;
        await billPaymentResource.createGasBill(this.gasForm)
            .then(response => {
                if(response && response.success){
                    Swal.fire(
                        "Exitó!",
                        "La factura de gas fue creada exitosamente",
                        "success"
                    );
                }else{
                    Swal.fire(
                        "Error",
                        "Hubo un error, intenta de nuevo",
                        "error"
                    );
                }
            })
            .catch(error => {
                Swal.fire(
                    "Error",
                    "Hubo un error, intenta de nuevo",
                    "error"
                );
            });

        this.creatingBill = false;
        this.closeModal();

      },
      async createElectricityBill() {

        if (
          !this.electricityForm.start_date ||
          !this.electricityForm.end_date ||
          !this.electricityForm.due_date ||
          this.electricityForm.total == null ||
          this.electricityForm.aseo == null ||
          this.electricityForm.heads_201 == null ||
          this.electricityForm.heads_301 == null ||
          this.electricityForm.heads_401 == null
        ) {
          Swal.fire(
              "Error",
              "Todos los campos deben ser completados con el formato correcto.",
              "error"
          );
          return;
        }

        this.creatingBill = true;
        await billPaymentResource.createElectricityBill(this.electricityForm)
            .then(response => {
                if(response && response.success){
                    Swal.fire(
                        "Exitó!",
                        "La factura de luz fue creada exitosamente",
                        "success"
                    );
                }else{
                    Swal.fire(
                        "Error",
                        "Hubo un error, intenta de nuevo",
                        "error"
                    );
                }
            })
            .catch(error => {
                Swal.fire(
                    "Error",
                    "Hubo un error, intenta de nuevo",
                    "error"
                );
            });

        this.creatingBill = false;
        this.closeModal();

      },
      formatDate(dateString) {
        if (!dateString) return 'N/A';
        const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
        return new Date(dateString).toLocaleDateString('es-CO', options);
      },
      changePage(page) {
        if (page >= 1 && page <= this.totalPages) {
          this.currentPage = page;
        }
      }
    }
  }
</script>

<style scoped>
  .badge {
    padding: 0.5em 1em;
    border-radius: 25px;
    font-size: 0.9em;
    font-weight: 600;
  }

  .bg-verified {
    background-color: #d4edda;
    color: #155724;
  }

  .bg-pending {
    background-color: #fff3cd;
    color: #856404;
  }

  .modal-content {
    max-height: 90vh;
    overflow-y: auto;
  }

  .modal-body {
    padding: 0;
    height: auto;
  }

  .fa-info-circle {
    cursor: pointer;
    transition: transform 0.2s;
  }

  .fa-info-circle:hover {
    transform: scale(1.2);
  }

  .custom-border-table th,
  .custom-border-table td {
    border: 1px solid #acacac;
  }

  .custom-border-table{
    margin-bottom: 0px;
  }

  .custom-big-table thead tr th {
    vertical-align: middle;
    text-align: center;
  }

  .custom-big-table th,
  .custom-big-table td {
    border-top: 1px solid #8f754f;
    border-bottom: 1px solid #8f754f;
    border-left: 0;
    border-right: 0;
  }

  .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 0.2rem;
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .modal-content {
      width: 95%;
    }

    .row {
      flex-direction: column;
    }

    .col-md-6 {
      width: 100%;
    }
  }

  .badge {
      padding: 0.5em 1em;
      border-radius: 25px;
      font-size: 0.9em;
      font-weight: 600;
  }

  .bg-pendiente {
      background-color: #fff3cd;
      color: #856404;
  }

  .bg-pagado {
      background-color: #d4edda;
      color: #155724;
  }

  .bg-vencido {
      background-color: #f8d7da;
      color: #721c24;
  }

  .is-invalid {
      border-color: #dc3545 !important;
  }

  .text-danger {
      color: #dc3545;
      font-size: 0.875em;
  }

  .modal-footer {
      display: flex;
      justify-content: center;
      gap: 1rem;
      padding: 0;
      flex-direction: row;
      border: 0px;
      margin-top: 20px;
  }

  .container {
      width: 100%;
      margin: 0 auto;
      padding: 2rem;
      background-color: #f8f5f0;
      height: auto;
      border-radius: 25px;
  }

  .titulo-principal {
      color: #8f754f;
      text-align: center;
      margin-bottom: 2rem;
      font-size: 2.5rem;
      font-weight: 800;
  }

  .pagination {
      justify-content: center;
      margin-top: 0px;
  }

  .page-item {
      border-radius: 25px !important;
      margin: 0 5px;
  }

  .page-link {
      background-color: #f8f5f0;
      color: #8f754f;
      border: none;
      border-radius: 25px;
      padding: 8px 16px;
      font-size: 16px;
      text-align: center;
      transition: all 0.3s ease;
  }

  .page-link:hover {
      background-color: #f5ebd8;
      color: black;
  }

  .page-item.active .page-link {
      background-color: #8f754f;
      color: white;
  }

  .page-item.disabled .page-link {
      display: none;
  }

  .page-link:focus {
      outline: none;
  }

  .page-item:first-child .page-link {
      border-radius: 25px;
  }

  .page-item:last-child .page-link {
      border-radius: 25px;
  }

  .seccion {
      background: white;
      border-radius: 36px;
      padding: 1.5rem;
      margin-bottom: 2rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }

  .titulo-seccion {
      color: #8f754f;
      font-size: 1.5rem;
      margin-bottom: 1.5rem;
      font-weight: 800;
      display: flex;
      align-items: center;
      gap: 0.5rem;
  }

  .contenedor-campos {
      display: grid;
      gap: 0px;
  }

  .grupo-campos {
      overflow: hidden;
      border: 0px solid #d7b377;
      border-radius: 25px;
      background-color: #f8f5f0;
      padding: 24px;
      margin-bottom: 1.5rem;
  }

  .modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
  }

  .modal-content {
      background-color: white;
      width: 90%;
      height: auto;
      display: flex;
      flex-direction: column;
      border-radius: 8px;
      overflow: hidden;
      position: relative;
  }

  .modal-header {
      padding: 10px;
      background-color: #f2f2f2;
      display: flex;
      justify-content: flex-end;
  }

  .close-button {
      font-size: 24px;
      border: none;
      background: none;
      cursor: pointer;
      line-height: 1;
  }

  .modal-header {
      padding: 10px;
      background-color: #f2f2f2;
      display: flex;
      justify-content: space-between;
      align-items: center;
  }

  .modal-title {
      font-size: 16px;
      font-weight: bold;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      max-width: 80%;
  }

  .close:hover,
  .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
  }

  .subtitulo {
      color: #8f754f;
      font-size: 1.3rem;
      margin-bottom: 1rem;
      font-weight: 700;
  }

  .campos-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1rem;
      background-color: #f8f5f0;
      border-radius: 25px;
  }

  .custom-border-table th,
  .custom-border-table td {
      border: 1px solid #acacac;
  }

  .custom-border-table{
      margin-bottom: 0px;
  }

  .custom-big-table thead tr th {
      vertical-align: middle;
      text-align: center;
  }

  .custom-big-table th,
  .custom-big-table td {
      border-top: 1px solid #8f754f;
      border-bottom: 1px solid #8f754f;
      border-left: 0;
      border-right: 0;
  }

  .campo {
      margin-bottom: 0rem;
  }

  .etiqueta {
      display: block;
      margin-bottom: 0.5rem;
      color: #000000;
      font-weight: 500;
  }

  .table{
      margin-bottom: 0px;
  }

  .ingreso{
      background-color: #ffffff !important;
      border-radius: 25px;
  }

  .ingreso .campo .entrada {
      background-color: #f8f5f0 !important;
  }

  .entrada {
      width: 100%;
      padding: 0.75rem;
      border: 0px solid #d7b377;
      border-radius: 25px;
      background-color: white;
  }

  .entrada:focus {
      outline: 0px solid #8f754f;
      border-color: transparent;
  }

  .boton-primario {
      background-color: #8f754f;
      color: white;
      border: none;
      padding: 1rem 2rem;
      border-radius: 25px;
      cursor: pointer;
      font-size: 1rem;
      transition: background-color 0.3s;
      /* width: 100%; */
  }

  .boton-primario:hover {
      background-color: #6d5a3f;
  }

  .boton-primario:disabled {
      background-color: #cccccc;
      cursor: not-allowed;
  }

  .boton-secundario {
      background-color: #d7b377;
      color: white;
      border: none;
      padding: 1rem 2rem;
      border-radius: 25px;
      cursor: pointer;
      transition: background-color 0.3s;
  }

  .boton-secundario:hover {
      background-color: #b59460;
  }

  .boton-eliminar {
      background: #d7b377;
      border: none;
      border-radius: 25px;
      color: white;
      height: 51px;
      cursor: pointer;
      padding: 0 0.5rem;
      align-self: center;
  }

  .boton-eliminar:hover {
      background-color: #b59460 ;
  }

  .boton-enviar {
      background-color: #8f754f;
      color: white;
      border: none;
      padding: 1.25rem;
      border-radius: 25px;
      font-size: 1.25rem;
      cursor: pointer;
      transition: background-color 0.3s;
      width: 100%;
      margin-top: 0;
  }

  .boton-enviar:hover {
      background-color: #6d5a3f;
  }

  .ayuda {
      color: #666;
      font-size: 0.875rem;
      margin-top: 0.25rem;
  }

  @media (max-width: 768px) {
      .container {
          border-radius: 0px;
      }

      .titulo-principal {
          font-size: 2rem;
      }

      .campos-grid {
          grid-template-columns: 1fr;
      }
  }
  </style>