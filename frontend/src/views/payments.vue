
<template>
    <div class="container">
      <!-- Loading Overlay -->
      <div v-if="loading" class="position-fixed start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0, 0, 0, 0.5); z-index: 1050; top: 0px;">
        <div class="spinner-border text-light" role="status">
          <span class="visually-hidden"></span>
        </div>
      </div>

      <!-- Payment Details Modal -->
      <div v-if="showPaymentModal" class="modal" @click.self="closeModal">
        <div class="modal-content" style="max-width: 900px;">
          <div class="modal-header">
            <span class="modal-title">📄 Detalles del Pago</span>
            <button class="close-button" @click="closeModal">×</button>
          </div>
          <div class="modal-body p-4" style="overflow-y: auto;">
            <div class="row">
              <!-- Left Side - Bills Information -->
              <div class="col-md-6">
                <div class="seccion" style="height: 100%; padding-bottom: 0px;">
                  <h3 class="titulo-seccion">📋 Facturas Pagadas</h3>
                  <div class="contenedor-campos" style="gap: 10px !important;">
                    <div v-for="(bill, index) in selectedPayment.bills" :key="index" class="grupo-campos">
                      <div style="overflow-x: auto;">
                        <table class="table table-striped custom-border-table" style="border-radius: 25px; overflow: hidden;">
                          <tbody>
                            <tr>
                              <th style="width: 40%;">Nombre</th>
                              <td>{{ bill.bill_type.name }}</td>
                            </tr>
                            <tr>
                              <th>Inmueble</th>
                              <td>{{ bill.property.sku }}</td>
                            </tr>
                            <tr>
                              <th>Desde</th>
                              <td>{{ formatDate(bill.start_date) }}</td>
                            </tr>
                            <tr>
                              <th>Hasta</th>
                              <td>{{ formatDate(bill.end_date) }}</td>
                            </tr>
                            <tr>
                              <th>Vence</th>
                              <td>{{ formatDate(bill.due_date) }}</td>
                            </tr>
                            <tr>
                              <th>Monto</th>
                              <td>{{ formatCurrency(bill.amount) }}</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Right Side - Payment Certificate -->
              <div class="col-md-6">
                <div class="seccion" style="height: 100%;">
                  <h3 class="titulo-seccion">📄 Comprobante de Pago</h3>
                  <div class="contenedor-campos" style="height: calc(100% - 60px);">
                    <div class="grupo-campos" style="height: 100%;">
                      <img :src="selectedPayment.payment_cert_url" alt="Comprobante de pago" style="width: 100%; height: 100%; object-fit: contain; border: 0px solid #d7b377; border-radius: 15px;">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
              <button type="button" class="boton-secundario" @click="closeModal">
                Cancelar
              </button>
              <button v-admin-only v-if="selectedPayment.status !== 'Confirmado'" type="button" class="boton-primario" @click="verifyPayment" :disabled="verifying" style="width: auto;">
                {{ verifying ? 'Confirmando...' : 'Confirmar' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="row seccion mainSeccion mt-4">
        <!-- Header -->
        <div class="col-12">
          <h2 class="titulo-seccion">💵 Pagos</h2>
        </div>

        <!-- Filters -->
        <div class="col-12 mb-4" v-admin-only>
          <div class="row">
            <div class="col-md-6 mb-3">
              <select class="entrada" v-model="propertyFilter" style="background-color: #f8f5f0;">
                <option value="">Todas las Propiedades</option>
                <option v-for="sku in properties" :key="sku" :value="sku.sku">{{ sku.sku }}</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <select class="entrada" v-model="statusFilter" style="background-color: #f8f5f0;">
                <option value="">Todos los Estados</option>
                <option v-for="status in statusOptions" :key="status" :value="status">{{ status }}</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Payments Table -->
        <div class="col-12">
          <div style="overflow-x: auto;">
            <table class="table table-bordered table-hover table-striped align-middle text-center custom-big-table" style="border-radius: 25px; overflow: hidden;">
              <thead style="background-color: #8f754f; color: white;">
                <tr>
                  <th>Pagó</th>
                  <th>Monto</th>
                  <th>Método de Pago</th>
                  <th>Estado</th>
                  <th>Pagado el</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="payment in paginatedPayments" :key="payment.id">
                  <td>
                    <!-- <div v-for="(bill, index) in payment.payment_has_bill" :key="index">
                      {{ bill.property.sku }} {{ bill.bill_type.name }}
                    </div> -->
                    <div v-for="(bill, index) in payment.bills" :key="index">
                        {{ bill.property.sku }}
                        <i :class="getBillIcon(bill.bill_type.name)"></i><br>
                    </div>
                  </td>
                  <td>{{ formatCurrency(payment.amount) }}</td>
                  <td>{{ payment.payment_method }}</td>
                  <td>
                    <span :class="['badge', statusClass(payment.status)]">
                      {{ payment.status === 'Confirmado' ? 'Confirmado' : 'Pendiente' }}
                    </span>
                  </td>
                  <td>{{ formatDateTime(payment.paid_at) }}</td>
                  <td>
                    <button class="btn btn-sm" @click="showPaymentDetails(payment)" style="background: none; border: none;">
                      <i class="fas fa-info-circle" style="color: #8f754f; font-size: 1.2rem;"></i>
                    </button>
                  </td>
                </tr>
                <tr v-if="paginatedPayments.length === 0">
                    <td colspan="6" class="text-center py-4">No hay pagos todavia</td>
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

<script setup>
    import { ref, computed, onMounted } from 'vue';
    import Swal from 'sweetalert2';
    import billPaymentApi from '@/api/billPayment.js';
    import { useStore } from 'vuex';
    import property from '@/api/property.js';

    const propertyResource = new property();


    const store = useStore();
    const billPaymentResource = new billPaymentApi();


    onMounted(async () => {

    //   properties.value = store.getters.getProperties;

    // });

        loading.value = true;

        payments.value = store.getters.getPayments;
        if(payments.value.length == 0){
            await billPaymentResource.getPayments().then(response => {
                if (response && response.success) {
                    payments.value = response.data;
                    store.commit('setPayments', payments.value)
                } else {
                    Swal.fire(
                        'Error',
                        'Hubo un error, intenta despues.',
                        'error'
                    );
                }
            }).catch(error => {
                console.log(error);
                Swal.fire(
                    'Error',
                    'Hubo un error, intenta despues.',
                    'error'
                );
            });
        }

        properties.value = store.getters.getProperties;
        if(properties.value.length == 0 && store.getters.userRole == "admin"){
            await propertyResource.getAllProperties()
                .then(response => {
                    if(response && response.success){
                        store.commit('setProperties', response.data)
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


    const properties = ref();

  // Fake Data Generation
  const generateFakePayments = () => {
    const payments = [];
    const statusOptions = ['Confirmado', 'Pendiente'];
    const paymentMethods = ['Bank Transfer', 'Credit Card', 'Cash', 'PayPal'];
    const propertySkus = ['FO-APTO-201', 'SE-APTO-301', 'FO-PARQ-1', 'SE-LOC-1'];
    const billTypes = ['Agua', 'Luz', 'Gas', 'Arriendo'];

    for (let i = 1; i <= 25; i++) {
      const billCount = Math.floor(Math.random() * 3) + 1;
      const paymentBills = [];

      for (let j = 0; j < billCount; j++) {
        paymentBills.push({
          id: i * 10 + j,
          payment_id: i,
          bill_id: i * 10 + j,
          bills: {
            id: i * 10 + j,
            bill_type_id: j + 1,
            property_id: Math.floor(Math.random() * 4) + 1,
            start_date: `2025-${String(Math.floor(Math.random() * 12) + 1).padStart(2, '0')}-01`,
            end_date: `2025-${String(Math.floor(Math.random() * 12) + 1).padStart(2, '0')}-28`,
            due_date: `2025-${String(Math.floor(Math.random() * 12) + 1).padStart(2, '0')}-15`,
            amount: (Math.random() * 1000000 + 500000).toFixed(2),
            status: 'paid',
            property: {
              id: Math.floor(Math.random() * 4) + 1,
              sku: propertySkus[Math.floor(Math.random() * propertySkus.length)]
            },
            bill_type: {
              id: j + 1,
              name: billTypes[Math.floor(Math.random() * billTypes.length)]
            }
          }
        });
      }

      payments.push({
        id: i,
        amount: paymentBills.reduce((sum, bill) => sum + parseFloat(bill.amount), 0).toFixed(2),
        payment_method: paymentMethods[Math.floor(Math.random() * paymentMethods.length)],
        status: statusOptions[Math.floor(Math.random() * 2)],
        payment_cert_url: 'https://imgs.search.brave.com/Hm6ZRHFEIbcM43R0RIdfNrrEhO_mZu9dfcwZbuYR1Uc/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9pLmJs/b2dzLmVzL2U4Yjhi/ZC93aGF0c2FwcC1p/bWFnZS0yMDI1LTAx/LTEzLWF0LTEwLjE2/LjE0LWFtLzQ1MF8x/MDAwLmpwZWc',
        paid_at: `2025-${String(Math.floor(Math.random() * 12) + 1).padStart(2, '0')}-${String(Math.floor(Math.random() * 28) + 1).padStart(2, '0')}T${String(Math.floor(Math.random() * 12) + 1).padStart(2, '0')}:${String(Math.floor(Math.random() * 60)).padStart(2, '0')}:00`,
        verified_at: null,
        payment_has_bills: paymentBills
      });
    }
    return payments;
  };

  const payments = ref(generateFakePayments());
  const loading = ref(false);
  const verifying = ref(false);

  // Filters and Pagination
  const propertyFilter = ref('');
  const statusFilter = ref('');
  const currentPage = ref(1);
  const itemsPerPage = 10;

  const filteredPayments = computed(() => {
    return payments.value.filter(payment => {
      const matchesProperty = !propertyFilter.value ||
        payment.payment_has_bills.some(bill => bills.property.sku === propertyFilter.value);
      const matchesStatus = !statusFilter.value ||
        (statusFilter.value === 'Confirmado' ? payment.status === 'Confirmado' : payment.status === 'Pendiente');
      return matchesProperty && matchesStatus;
    });
  });

  const totalPages = computed(() => Math.ceil(filteredPayments.value.length / itemsPerPage));
  const paginatedPayments = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    return filteredPayments.value.slice(start, start + itemsPerPage);
  });

  const statusOptions = ['Confirmado', 'Pendiente'];

  // Modal Handling
  const showPaymentModal = ref(false);
  const selectedPayment = ref(null);

  const showPaymentDetails = (payment) => {
    selectedPayment.value = payment;
    showPaymentModal.value = true;
  };

  const closeModal = () => {
    showPaymentModal.value = false;
    selectedPayment.value = null;
  };

    const verifyPayment = async () => {

        verifying.value = true;
        loading.value = true;

        await billPaymentResource.verifyPayment({id: selectedPayment.value.id}).then(response => {
            if (response && response.success) {
                const index = payments.value.findIndex(p => p.id === selectedPayment.value.id);
                if (index !== -1) {
                    payments.value[index].status = 'Confirmado';
                    payments.value[index].verified_at = new Date().toISOString();
                }
                Swal.fire(
                    'Éxito',
                    'El pago fue Confirmado exitosamente.',
                    'success'
                );
            } else {
                Swal.fire(
                    'Error',
                    'Hubo un error, intenta despues.',
                    'error'
                );
            }
        }).catch(error => {
            console.log(error);
            Swal.fire(
                'Error',
                'Hubo un error, intenta despues.',
                'error'
            );
        });

        verifying.value = false;
        loading.value = false;

    };

  // Utility Functions
  const formatCurrency = (amount) => {
    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(amount);
  };

  const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
    return new Date(dateString).toLocaleDateString('es-CO', options);
  };

  const formatDateTime = (dateTimeString) => {
    if (!dateTimeString) return 'N/A';
    const options = {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit'
    };
    return new Date(dateTimeString).toLocaleDateString('es-CO', options);
  };

  const statusClass = (status) => {
    return {
      'Confirmado': 'bg-verified',
      'Pendiente': 'bg-pending'
    }[status];
  };

  const getBillIcon = (type) => {
    switch(type) {
      case 'Agua': return 'fa-solid fa-bottle-water';
      case 'Gas': return 'fa-solid fa-gas-pump';
      case 'Luz': return 'fa-solid fa-bolt';
      case 'Arriendo': return 'fa-solid fa-house-user';
      default: return '';
    }
  }



  const changePage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
      currentPage.value = page;
    }
  };
  </script>

  <style scoped>
  /* Reuse existing styles from previous example and add: */
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
/*
  .modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    padding: 1rem;
    border-top: 1px solid #dee2e6;
  } */

  .fa-info-circle {
    cursor: pointer;
    transition: transform 0.2s;
  }

  .fa-info-circle:hover {
    transform: scale(1.2);
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
  </style>

<style scoped>
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
        min-height: 100vh;
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

    /* .modal-body {
        flex-grow: 1;
    } */

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
        width: 100%;
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
