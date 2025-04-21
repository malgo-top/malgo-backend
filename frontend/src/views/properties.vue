<template>
    <div class="container">
      <!-- Loading Overlay -->
      <div v-if="loading" class="position-fixed start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0, 0, 0, 0.5); z-index: 1050; top: 0px;">
        <div class="spinner-border text-light" role="status">
          <span class="visually-hidden"></span>
        </div>
      </div>

      <!-- Main Content -->
      <div class="row seccion mainSeccion mt-4">
        <!-- Header -->
        <div class="col-12">
          <h2 class="titulo-seccion">🏠 Propiedades</h2>
        </div>

        <!-- Properties Table -->
        <div class="col-12">
          <div style="overflow-x: auto;">
            <table class="table table-bordered table-hover align-middle text-center custom-big-table" style="border-radius: 25px; overflow: hidden;">
              <thead style="background-color: #8f754f; color: white;">
                <tr>
                  <th>SKU</th>
                  <th>Aceptando Aplicaciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="property in properties" :key="property.sku">
                  <td>{{ property.sku }}</td>
                  <td>
                    <div class="form-check form-switch d-flex justify-content-center">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        role="switch"
                        v-model="property.accepting_applications"
                        :checked="property.accepting_applications == 1"
                        @change="toggleApplications(property)"
                        style="width: 3em; height: 1.5em;"
                      >
                      <!-- <span v-if="updatingProperty === property.sku" class="ms-2">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                      </span> -->
                    </div>
                  </td>
                </tr>
                <tr v-if="properties.length === 0">
                  <td colspan="2" class="text-center py-4">No se encontraron propiedades</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
</template>

<script setup>
    import { ref, onMounted } from 'vue';
    import property from '@/api/property.js';
    import { useStore } from 'vuex'

    const store = useStore()
    const propertyResource = new property();

    const properties = ref([]);

    const loading = ref(false);
    const updatingProperty = ref(null);

    onMounted(async () => {
        loading.value = true;
        properties.value = store.getters.getProperties;
        if(properties.value.length == 0){
            await propertyResource.getAllProperties()
                .then(response => {
                    if(response && response.success){
                        store.commit('setProperties', response.data)
                        properties.value = store.getters.getProperties;
                        console.log(properties.value);
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
        }
        loading.value = false;
    });

    const toggleApplications = async (property) => {
        loading.value = true;
        await propertyResource.switchPropertyApplications({accepting_applications: property.accepting_applications, id: property.id})
                .then(response => {
                    if(response && response.success){
                        property.accepting_applications = response.data;
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

        loading.value = false;
    };

</script>

<style scoped>
  .container {
      width: 100%;
      margin: 0 auto;
      padding: 2rem;
      background-color: #f8f5f0;
      min-height: 100vh;
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

  .form-check-input:checked {
      background-color: #8f754f;
      border-color: #8f754f;
  }

  .form-check-input:focus {
      box-shadow: 0 0 0 0.25rem rgba(143, 117, 79, 0.25);
      border-color: #8f754f;
  }

  @media (max-width: 768px) {
      .container {
          border-radius: 0px;
          padding: 1rem;
      }
  }
</style>
