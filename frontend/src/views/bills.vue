

<template>
    <div class="container">
        <!-- Loading Spinner -->
        <div v-if="loading" class="position-fixed start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0, 0, 0, 0.5); z-index: 1050; top: 0px;">
            <div class="spinner-border text-light" role="status">
                <span class="visually-hidden"></span>
            </div>
        </div>

        <!-- Payment Modal -->
        <div v-if="showPaymentModal" class="modal" @click.self="closePaymentModal">
            <div class="modal-content" style="overflow-y: auto; height: auto; max-width: 880px;">
                <div class="modal-header">
                    <span class="modal-title" >💳 Confirmar Pago de Factura(s)</span>
                    <button class="close-button" @click="closePaymentModal">×</button>
                </div>
                <div class="modal-body p-4">
                    <form @submit.prevent="submitPayment">
                        <div>
                            <div class="contenedor-campos" style="gap: 10px !important;">
                                <div class="grupo-campos">
                                    <div class="campos-grid">
                                        <div class="campo">
                                            <label class="etiqueta">Monto Total (COP)</label>
                                            <input :value="formatCurrency(paymentData.amount)" type="text" class="entrada" disabled >
                                        </div>

                                        <div class="campo">
                                            <label class="etiqueta">Método de Pago</label>
                                            <input v-model="paymentData.payment_method" type="text" class="entrada" :class="{ 'is-invalid': errors.payment_method }" placeholder="Ej: Transferencia Bancaria">
                                            <div v-if="errors.payment_method" class="text-danger mt-1">{{ errors.payment_method }}</div>
                                        </div>

                                        <div class="campo">
                                            <label class="etiqueta">Comprobante de Pago (Imagen)</label>
                                            <input type="file" @change="handlePaymentProofUpload"   accept="image/*" class="entrada" :class="{ 'is-invalid': errors.payment_proof }">
                                            <div v-if="errors.payment_proof" class="text-danger mt-1">{{ errors.payment_proof }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="boton-secundario" @click="closePaymentModal">
                                Cerrar
                            </button>
                            <button type="submit" class="boton-primario" :disabled="processingPayment" style="width: auto;">
                                {{ processingPayment ? 'Procesando...' : 'Pagar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row seccion mainSeccion mt-4">
            <!-- <div class="col-12 d-flex justify-content-between align-items-center mb-4">
                <h2 class="titulo-seccion" style="margin: 0;">📄 Facturas</h2>
                <button
                    class="boton-primario"
                    @click="openPaymentModal"
                    style="width: auto;"
                    :disabled="selectedBills.length === 0"
                >
                    <i class="fas fa-money-bill-wave" style="margin-right: 10px; width: auto;"></i>Confirmar pago de factura
                </button>
            </div> -->

            <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <h2 class="titulo-seccion mb-2 mb-md-0" style="margin: 0;">📄 Facturas</h2>
                <button
                    class="boton-primario mt-md-0 mt-2"
                    @click="openPaymentModal"
                    style="width: auto;"
                    :disabled="selectedBills.length === 0"
                >
                    <i class="fas fa-money-bill-wave" style="margin-right: 10px; width: auto;"></i>
                    Confirmar pago de factura
                </button>
            </div>

<!--
            <div class="col-12 d-flex align-items-center mb-0" style="justify-content: flex-start;">
                <h1 class="titulo-seccion">📄 Facturas</h1>

            </div>

            <div class="col-12 d-flex align-items-center mt-0 mb-4" style="justify-content: flex-end;">
                <button
                    class="boton-primario"
                    @click="openPaymentModal"
                    style="width: auto;"
                    :disabled="selectedBills.length === 0"
                >
                    <i class="fas fa-money-bill-wave mr-2"></i>Pagar Factura
                </button>
            </div> -->


            <!-- <div class="col-12">
                <div style="overflow-x: auto;">
                    <table class="table table-bordered table-striped table-hover align-middle text-center custom-big-table" style="border-radius: 25px; overflow: hidden;">
                        <thead style="background-color: #8f754f; color: white;">
                            <tr>
                                <th></th>
                                <th>Nombre</th>
                                <th>Inmueble</th>
                                <th>Desde</th>
                                <th>Hasta</th>
                                <th>Vence</th>
                                <th>Monto</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="bill in bills" :key="bill.id">
                                <td>
                                    <input type="checkbox" v-model="selectedBills" :value="bill.id" style="height:auto;">
                                </td>
                                <td>{{ bill.bill_type.name }}</td>
                                <td>{{ bill.property.sku }}</td>
                                <td>{{ formatDate(bill.start_date) }}</td>
                                <td>{{ formatDate(bill.end_date) }}</td>
                                <td>{{ formatDate(bill.due_date) }}</td>
                                <td>{{ formatCurrency(bill.amount) }}</td>
                                <td>
                                    <span :class="['badge', statusClass(bill.status)]">
                                        {{ bill.status }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="bills.length === 0">
                                <td colspan="8" class="text-center py-4">No hay facturas pendientes</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> -->


            <div class="col-12">
                <!-- Desktop Table -->
                <div class="d-none d-md-block" style="overflow-x: auto;">
                    <table class="table table-bordered table-striped table-hover align-middle text-center custom-big-table" style="border-radius: 25px; overflow: hidden;">
                        <thead style="background-color: #8f754f; color: white;">
                            <tr>
                                <th></th>
                                <th>Nombre</th>
                                <th>Inmueble</th>
                                <th>Desde</th>
                                <th>Hasta</th>
                                <th>Vence</th>
                                <th>Monto</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="bill in bills" :key="bill.id" @click="toggleBillSelection(bill.id)" :class="{ 'selected-row': selectedBills.includes(bill.id) }">
                                <td @click.stop>
                                    <input type="checkbox" v-model="selectedBills" :value="bill.id" style="height:auto;">
                                </td>
                                <td>{{ bill.bill_type.name }}</td>
                                <td>{{ bill.property.sku }}</td>
                                <td>{{ formatDate(bill.start_date) }}</td>
                                <td>{{ formatDate(bill.end_date) }}</td>
                                <td>{{ formatDate(bill.due_date) }}</td>
                                <td>{{ formatCurrency(bill.amount) }}</td>
                                <td>
                                    <span :class="['badge', statusClass(bill.status)]">
                                        {{ bill.status }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="bills.length === 0">
                                <td colspan="8" class="text-center py-4">No hay facturas pendientes</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="d-md-none">
                    <div class="mobile-bill-card"
                         v-for="bill in bills"
                         :key="'mobile-' + bill.id"
                         @click="toggleBillSelection(bill.id)"
                         :class="{ 'selected-card': selectedBills.includes(bill.id) }">
                        <div class="card-header">
                            <div class="card-checkbox" @click.stop>
                                <input type="checkbox" v-model="selectedBills" :value="bill.id">
                            </div>
                            <div class="card-title">
                                <strong>{{ bill.bill_type.name }}</strong>
                                <span :class="['badge', statusClass(bill.status)]">
                                    {{ bill.status }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-row">
                                <span class="card-label">Inmueble:</span>
                                <span class="card-value">{{ bill.property.sku }}</span>
                            </div>
                            <div class="card-row">
                                <span class="card-label">Periodo:</span>
                                <span class="card-value">{{ formatDate(bill.start_date) }} - {{ formatDate(bill.end_date) }}</span>
                            </div>
                            <div class="card-row">
                                <span class="card-label">Vence:</span>
                                <span class="card-value">{{ formatDate(bill.due_date) }}</span>
                            </div>
                            <div class="card-row">
                                <span class="card-label">Monto:</span>
                                <span class="card-value">{{ formatCurrency(bill.amount) }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-if="bills.length === 0" class="text-center py-4">No hay facturas pendientes</div>
                </div>
            </div>


            <div class="col-12 mt-4">
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
    import billPayment from '@/api/billPayment.js';
    import { useStore } from 'vuex';

    const store = useStore();
    const billPaymentResource = new billPayment();

    const loading = ref(false);
    const processingPayment = ref(false);
    const showPaymentModal = ref(false);
    const selectedBills = ref([]);

    const bills = ref([
        {
            "id": 21,
            "bill_type_id": 4,
            "property_id": 4,
            "start_date": "2025-03-01",
            "end_date": "2025-03-31",
            "due_date": "2025-04-10",
            "amount": "950000.00",
            "status": "Atrasado",
            "property": {
                "id": 4,
                "sku": "FO-APTO-201",
            },
            "bill_type": {
                "id": 4,
                "name": "Agua",
            }
        },
        {
            "id": 22,
            "bill_type_id": 3,
            "property_id": 5,
            "start_date": "2025-03-01",
            "end_date": "2025-03-31",
            "due_date": "2025-04-10",
            "amount": "1200000.00",
            "status": "Pendiente",
            "property": {
                "id": 5,
                "sku": "SE-APTO-301",
            },
            "bill_type": {
                "id": 3,
                "name": "Renta",
            }
        },
        {
            "id": 23,
            "bill_type_id": 2,
            "property_id": 4,
            "start_date": "2025-04-01",
            "end_date": "2025-04-30",
            "due_date": "2025-05-10",
            "amount": "850000.00",
            "status": "Pagado",
            "property": {
                "id": 4,
                "sku": "FO-APTO-201",
            },
            "bill_type": {
                "id": 2,
                "name": "Renta",
            }
        }
    ]);

    const paymentData = ref({
        amount: 0,
        payment_method: '',
        payment_proof: null
    });

    const errors = ref({});

    // Pagination
    const currentPage = ref(1);
    const itemsPerPage = 10;

    const totalPages = computed(() => Math.ceil(bills.value.length / itemsPerPage));
    const paginatedBills = computed(() => {
        const start = (currentPage.value - 1) * itemsPerPage;
        return bills.value.slice(start, start + itemsPerPage);
    });

    onMounted(async () => {

        loading.value = true;

        bills.value = store.getters.getBillsUnpaid;
        if(bills.value.length == 0){
            await billPaymentResource.getBillsUnpaid().then(response => {
                if (response && response.success) {
                    bills.value = response.data;
                    store.commit('setBillsUnpaid', bills.value)
                } else {
                    Swal.fire(
                        'Error',
                        'Hubo un error, intenta despues.',
                        'error'
                    );
                }
            }).catch(error => {
                Swal.fire(
                    'Error',
                    'Hubo un error, intenta despues.',
                    'error'
                );
            });
        }

        loading.value = false;
    });

    const openPaymentModal = () => {
        if (selectedBills.value.length === 0) return;

        // Calculate total amount
        paymentData.value.amount = selectedBills.value.reduce((total, billId) => {
            const bill = bills.value.find(b => b.id === billId);
            return total + parseFloat(bill.amount);
        }, 0);

        showPaymentModal.value = true;
    };

    const closePaymentModal = () => {
        showPaymentModal.value = false;
        paymentData.value = {
            amount: 0,
            payment_method: '',
            payment_proof: null
        };
        errors.value = {};
    };

    const handlePaymentProofUpload = (event) => {
        const file = event.target.files[0];
        if (file && (file.type.startsWith('image/'))) {
            paymentData.value.payment_proof = file;
        } else {
            errors.value.payment_proof = 'Solo se permiten imágenes';
        }
    };

    const validatePayment = () => {
        errors.value = {};
        let isValid = true;

        if (!paymentData.value.payment_method) {
            errors.value.payment_method = 'Método de pago requerido';
            isValid = false;
        }

        if (!paymentData.value.payment_proof) {
            errors.value.payment_proof = 'Comprobante de pago valido requerido';
            isValid = false;
        }

        return isValid;
    };

    const submitPayment = async () => {
        if (!validatePayment()) return;

        processingPayment.value = true;
        loading.value= true;

        await billPaymentResource.payBills({"paymentData": paymentData.value, "billsPaid": selectedBills.value}).then(response => {
            if (response.success) {
                bills.value = bills.value.filter(bill => !selectedBills.value.includes(bill.id));
                Swal.fire(
                    '¡Pago exitoso!',
                    'Gracias por su pago, se le notificara por via email cuando sea confirmado.',
                    'success'
                );
            } else {
                Swal.fire(
                    'Error',
                    'Hubo un problema al procesar el pago. Por favor intente nuevamente.',
                    'error'
                );
            }
        }).catch(error => {
            console.log(error);
            Swal.fire(
                'Error',
                'Hubo un problema al procesar el pago. Por favor intente nuevamente.',
                'error'
            );
        });

        selectedBills.value = [];
        closePaymentModal();
        processingPayment.value = false;
        loading.value = false;

    };

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(amount);
    };

    const formatDate = (dateString) => {
        const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
        return new Date(dateString).toLocaleDateString('es-CO', options);
    };

    const toggleBillSelection = (billId) => {
        const index = selectedBills.value.indexOf(billId);
        if (index === -1) {
            selectedBills.value.push(billId);
        } else {
            selectedBills.value.splice(index, 1);
        }
    };

    const statusClass = (status) => {
        return {
            'Pendiente': 'bg-pendiente',
            'Pagado': 'bg-pagado',
            'Atrasado': 'bg-vencido'
        }[status];
    };

    const changePage = (page) => {
        if (page >= 1 && page <= totalPages.value) {
            currentPage.value = page;
        }
    };

</script>

<style scoped>

    .mobile-bill-card {
        background-color: white;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border: 1px solid #e0e0e0;
    }

    .selected-row {
        background-color: #f5ebd8 !important;
    }

    .selected-card {
        background-color: #f5ebd8;
        border: 1px solid #8f754f;
    }

    .card-header {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f0f0f0;
    }

    .card-checkbox {
        margin-right: 10px;
    }

    .card-title {
        flex: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-body {
        display: flex;
        flex-direction: column;
    }

    .card-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .card-label {
        font-weight: 500;
        color: #555;
    }

    .card-value {
        text-align: right;
    }

    tr {
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .badge {
            padding: 0.3em 0.8em;
            font-size: 0.8em;
        }

        .mobile-bill-card {
            padding: 12px;
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
        height: 90%;
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

    .modal-body {
        flex-grow: 1;
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
        width: 100%;
    }

    .boton-primario:hover {
        background-color: #6d5a3f;
    }

    .boton-primario:disabled {
        background-color: #efd19e;
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
