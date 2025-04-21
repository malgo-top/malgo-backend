<template>
    <div class="container">

        <div v-if="loading" class="position-fixed start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0, 0, 0, 0.5); z-index: 1050; top: 0px;">
            <div class="spinner-border text-light" role="status">
                <span class="visually-hidden"></span>
            </div>
        </div>

        <div v-if="showCreateModal" class="modal" @click.self="closeModal" >
            <div class="modal-content" style="overflow-y: auto; height: auto; max-width: 880px;">
                <div class="modal-header">
                    <span class="modal-title">📝 Crear Nuevo Contrato</span>
                    <button class="close-button" @click="closeModal">×</button>
                </div>
                <div class="modal-body p-4">
                    <form @submit.prevent="submitContract">
                        <div>
                            <div class="contenedor-campos" style="gap: 10px !important;">
                                <div class="grupo-campos">
                                    <div class="campos-grid">
                                        <div class="campo">
                                            <label class="etiqueta">Fecha de Inicio</label>
                                            <input v-model="newContract.start_date" type="date" class="entrada" :class="{ 'is-invalid': errors.start_date }" placeholder="Fecha de Inicio">
                                            <div v-if="errors.start_date" class="text-danger mt-1">{{ errors.start_date }}</div>
                                        </div>

                                        <div class="campo">
                                            <label class="etiqueta">Fecha de Fin</label>
                                            <input v-model="newContract.end_date" type="date" class="entrada" :class="{ 'is-invalid': errors.end_date }" placeholder="Fecha de Fin">
                                            <div v-if="errors.end_date" class="text-danger mt-1">{{ errors.end_date }}</div>
                                        </div>

                                        <div class="campo">
                                            <label class="etiqueta">Monto Mensual (COP)</label>
                                            <input v-model="newContract.monthly_amount" type="number" class="entrada" :class="{ 'is-invalid': errors.monthly_amount }"  placeholder="Monto Mensual">
                                            <div v-if="errors.monthly_amount" class="text-danger mt-1">{{ errors.monthly_amount }}</div>
                                        </div>

                                        <div class="campo">
                                            <label class="etiqueta">Depósito (COP)</label>
                                            <input v-model="newContract.deposit_amount" type="number" class="entrada" :class="{ 'is-invalid': errors.deposit_amount }" placeholder="Depósito">
                                            <div v-if="errors.deposit_amount" class="text-danger mt-1">{{ errors.deposit_amount }}</div>
                                        </div>

                                        <div class="campo">
                                            <label class="etiqueta">Solicitud de Arrendamiento</label>
                                            <select v-model="newContract.tenant_application_id" class="entrada" :class="{ 'is-invalid': errors.tenant_application_id }">
                                                <option value="" disabled selected>Seleccionar Solicitud</option>
                                                <option v-for="app in tenantApplications" :key="app.id" :value="app.id">
                                                    {{ app.property.sku }} - {{ app.financial_responsibles[0].full_name }}
                                                </option>
                                            </select>
                                            <div v-if="errors.tenant_application_id" class="text-danger mt-1">{{ errors.tenant_application_id }}</div>
                                        </div>

                                        <div class="campo">
                                            <label class="etiqueta">Inmueble</label>
                                            <select v-model="newContract.property_id" class="entrada" :class="{ 'is-invalid': errors.property_id }">
                                                <option value="" disabled selected>Seleccionar Inmueble</option>
                                                <option v-for="property in properties" :key="property.id" :value="property.id">
                                                    {{ property.sku }}
                                                </option>
                                            </select>
                                            <div v-if="errors.property_id" class="text-danger mt-1">{{ errors.property_id }}</div>
                                        </div>

                                        <div class="campo">
                                            <label class="etiqueta">Documento del Contrato (PDF)</label>
                                            <input type="file" @change="handleFileUpload" accept=".pdf" class="entrada" :class="{ 'is-invalid': errors.contract_doc }">
                                            <div v-if="errors.contract_doc" class="text-danger mt-1">{{ errors.contract_doc }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="boton-secundario" @click="closeModal">
                                Cancelar
                            </button>
                            <button type="submit" class="boton-primario" :disabled="loading" style="width: auto;">
                                {{ loading ? 'Creando...' : 'Crear Contrato' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div v-if="showDocModal" class="modal" @click.self="closeDocModal">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="modal-title">Documento del Contrato</span>
                    <button class="close-button" @click="closeDocModal">×</button>
                </div>
                <div class="modal-body">
                    <iframe :src="currentDocUrl" width="100%" height="100%" style="border: none;"></iframe>
                </div>
            </div>
        </div>

        <div class="row seccion mainSeccion mt-4">
            <div class="col-12 d-flex justify-content-between align-items-center mb-4">
                <h2 class="titulo-seccion" style="margin: 0;">📑 Contratos</h2>
                <button class="boton-primario" @click="showCreateModal = true" style="width: auto;">
                    <i class="fas fa-plus" style="margin-right: 10px;"></i>Crear Contrato
                </button>
            </div>

            <div class="col-12 mb-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <select class="entrada" v-model="propertyFilter" style="background-color: #f8f5f0 ;">
                            <option value="">Todos las Propiedades</option>
                            <option v-for="sku in properties" :key="sku" :value="sku.sku">{{ sku.sku }}</option>
                        </select>
                    </div>
                        <div class="col-md-6 mb-3">
                        <select class="entrada" v-model="statusFilter" style="background-color: #f8f5f0 ;">
                            <option value="">Todos los Estados</option>
                            <option v-for="status in statusOptions" :key="status" :value="status">{{ status }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div style="overflow-x: auto;">
                    <table class="table table-bordered table-striped table-hover align-middle text-center custom-big-table" style="border-radius: 25px; overflow: hidden;">
                    <thead style="background-color: #8f754f; color: white;">
                        <tr>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Arriendo</th>
                            <th>Depósito</th>
                            <th>Inquilino</th>
                            <th>Inmueble</th>
                            <th>Status</th>
                            <th>Contrato</th>
                            <th>Fecha Creación</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="contract in paginatedContracts" :key="contract.id">
                            <td>{{ formatDate(contract.start_date) }}</td>
                            <td>{{ formatDate(contract.end_date) }}</td>
                            <td>{{ formatCurrency(contract.monthly_amount) }}</td>
                            <td>{{ formatCurrency(contract.deposit_amount) }}</td>
                            <td>{{ contract.user.name }}</td>
                            <td>{{ contract.property.sku }}</td>
                            <td>
                                <span :class="['badge', statusClass(contract.status)]">
                                {{ contract.status }}
                                </span>
                            </td>
                            <td>
                                <a v-if="contract.contract_doc" @click.prevent="openDocModal(contract.contract_doc_url)">
                                <i class="fas fa-file-pdf" style="color: black;"></i>
                                </a>
                            </td>
                            <td>{{ formatDate(contract.created_at) }}</td>
                            <td><i class="fa-solid fa-trash" @click="terminarContrato(contract.id)" style="cursor: pointer;"></i></td>
                        </tr>
                        <tr v-if="paginatedContracts.length === 0">
                            <td colspan="10" class="text-center py-4">No hay contratos</td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>

            <div class="col-12" style="margin-top: 20px;">
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
    import contract from '@/api/contract';
    import tenantApplicationApi from '@/api/tenantApplication';
    import Swal from 'sweetalert2'
    import { useStore } from 'vuex'
    import property from '@/api/property.js';

    const propertyResource = new property();
    const contractResource = new contract();
    const tenantApplicationResource = new tenantApplicationApi();
    const store = useStore()

    const propertySkus = ['FO-APTO-301', 'SE-APTO-201', 'SE-APTO-301', 'FO-PARQ-1'];


    const generateFakeContracts = () => {
        const contracts = [];
        const statusOptions = ['En curso', 'Terminado'];
        const names = ['Laura Gómez', 'Carlos Pérez', 'María Rodríguez', 'Andrés López'];

        for (let i = 1; i <= 25; i++) {
        contracts.push({
            id: i,
            property_id: Math.floor(Math.random() * 4) + 1,
            user_id: Math.floor(Math.random() * 4) + 1,
            tenant_application_id: Math.floor(Math.random() * 10) + 1,
            start_date: `202${Math.floor(Math.random() * 5)}-${String(Math.floor(Math.random() * 12) + 1).padStart(2, '0')}-${String(Math.floor(Math.random() * 28) + 1).padStart(2, '0')}`,
            end_date: `202${Math.floor(Math.random() * 5) + 3}-${String(Math.floor(Math.random() * 12) + 1).padStart(2, '0')}-${String(Math.floor(Math.random() * 28) + 1).padStart(2, '0')}`,
            monthly_amount: (Math.random() * 3000000 + 1000000).toFixed(2),
            deposit_amount: (Math.random() * 3000000 + 1000000).toFixed(2),
            status: statusOptions[Math.floor(Math.random() * 2)],
            created_at: `${String(Math.floor(Math.random() * 28) + 1).padStart(2, '0')}/${String(Math.floor(Math.random() * 12) + 1).padStart(2, '0')}/2023`,
            contract_doc: 'https://pdfobject.com/pdf/sample.pdf',
            property: {
                sku: propertySkus[Math.floor(Math.random() * propertySkus.length)],
            },
            user: {
            name: names[Math.floor(Math.random() * names.length)]
            }
        });
        }
        return contracts;
    };

    const contracts = ref(generateFakeContracts());
    const properties = ref([{ id: 1, sku: 'a' }]);

    const tenantApplications = ref(Array.from({ length: 10 }, (_, i) => ({
        id: i + 1,
        property_id: properties.value[Math.floor(Math.random() * 1)].sku,
        financial_responsibles: [{ full_name: ['Laura', 'Carlos', 'María', 'Andrés'][Math.floor(Math.random() * 4)] }],
        property: {
            sku: propertySkus[Math.floor(Math.random() * propertySkus.length)],
        },
    })));

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

        properties.value = properties.value.filter(property => !property.is_occupied)

        tenantApplications.value = store.getters.getTenantApplications;
        if(tenantApplications.value.length == 0){
            await tenantApplicationResource.getTenantApplications().then(response => {
                if (response && response.success) {
                    tenantApplications.value = response.data;
                    store.commit('setTenantApplications', tenantApplications.value)
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

        tenantApplications.value = tenantApplications.value.filter(
            application => application.status !== 'En curso'
        )

        contracts.value = store.getters.getContracts;
        if(contracts.value.length == 0){
            await contractResource.getContracts().then(response => {
                if (response && response.success) {
                    contracts.value = response.data;
                    store.commit('setContracts', contracts.value)
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

        loading.value = false;
    })

    // Filters and Pagination
    const propertyFilter = ref('');
    const statusFilter = ref('');
    const currentPage = ref(1);
    const itemsPerPage = 10;

    const filteredContracts = computed(() => {
        return contracts.value.filter(contract => {
            const matchesProperty = !propertyFilter.value || contract.property.sku === propertyFilter.value;
            const matchesStatus = !statusFilter.value || contract.status === statusFilter.value;
            return matchesProperty && matchesStatus;
        });
    });

    const totalPages = computed(() => Math.ceil(filteredContracts.value.length / itemsPerPage));
    const paginatedContracts = computed(() => {
        console.log(filteredContracts.value);
        const start = (currentPage.value - 1) * itemsPerPage;
        return filteredContracts.value.slice(start, start + itemsPerPage);
    });

    const statusOptions = ['En curso', 'Terminado'];

    // Modal Handling
    const showCreateModal = ref(false);
    const showDocModal = ref(false);
    const currentDocUrl = ref('');
    const loading = ref(false);

    const newContract = ref({
        start_date: '',
        end_date: '',
        monthly_amount: '',
        deposit_amount: '',
        tenant_application_id: "",
        property_id: "",
        contract_doc: null
    });

    async function terminarContrato(id){

        const resultado = await Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Deseas terminar este contrato?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, terminar',
            cancelButtonText: 'No, cancelar',
            reverseButtons: true,
        })

        loading.value = true;

        if (resultado.isConfirmed) {

            await contractResource.terminarContrato({"id": id}).then(response => {
                if( response && response.success ){
                    const index = contracts.value.findIndex(c => c.id === id)
                    if (index !== -1) {
                        contracts.value[index].status = "Terminado";
                    }
                    store.commit('setContracts', contracts.value)
                }else{
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

        loading.value = false;

    }

    const errors = ref({});

    const validateForm = () => {
        errors.value = {};
        let isValid = true;

        if (!newContract.value.start_date) {
            errors.value.start_date = 'Fecha de inicio requerida';
            isValid = false;
        }
        if (!newContract.value.end_date) {
            errors.value.end_date = 'Fecha de fin requerida';
            isValid = false;
        }
        if (new Date(newContract.value.end_date) <= new Date(newContract.value.start_date)) {
            errors.value.end_date = 'La fecha de fin debe ser posterior a la fecha de inicio';
            isValid = false;
        }
        if (!newContract.value.monthly_amount || newContract.value.monthly_amount <= 0) {
            errors.value.monthly_amount = 'Monto mensual inválido';
            isValid = false;
        }
        if (!newContract.value.deposit_amount || newContract.value.deposit_amount <= 0) {
            errors.value.deposit_amount = 'Depósito inválido';
            isValid = false;
        }
        if (!newContract.value.tenant_application_id) {
            errors.value.tenant_application_id = 'Seleccione una solicitud';
            isValid = false;
        }
        if (!newContract.value.property_id) {
            errors.value.property_id = 'Seleccione un inmueble';
            isValid = false;
        }
        if (!newContract.value.contract_doc) {
            errors.value.contract_doc = 'Documento requerido';
            isValid = false;
        }

        return isValid;
    };

    const handleFileUpload = (event) => {
        newContract.value.contract_doc = event.target.files[0];
        // if (file && file.type === 'application/pdf') {
        //     newContract.value.contract_doc = URL.createObjectURL(file);
        // } else {
        //     errors.value.contract_doc = 'Solo se permiten archivos PDF';
        // }
    };

    const submitContract = async () => {
        if (!validateForm()) return;

        loading.value = true;

        await contractResource.createContract(newContract.value).then(response => {
            if( response && response.success ){
                if(response.error){
                    Swal.fire(
                        '¡Error!',
                        'Hay un contrato en curso de esta propiedad.',
                        'error'
                    )
                }else{
                    contracts.value.push(response.data);
                    store.commit('setContracts', contracts.value)
                    Swal.fire(
                        '¡Éxito!',
                        'El contrato ha sido creado exitosamente.',
                        'success'
                    )
                }
            }else{
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

        closeModal();
        loading.value = false;
    };

    const openDocModal = (url) => {
        currentDocUrl.value = url;
        showDocModal.value = true;
    };

    const closeModal = () => {
        showCreateModal.value = false;
        newContract.value = {
            start_date: '',
            end_date: '',
            monthly_amount: '',
            deposit_amount: '',
            tenant_application_id: "",
            property_id: "",
            contract_doc: null
        };
        errors.value = {};
    };

    const closeDocModal = () => {
        showDocModal.value = false;
        currentDocUrl.value = '';
    };

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(amount);
    };

    const formatDate = (dateString) => {
        const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
        return new Date(dateString).toLocaleDateString('es-CO', options);
    };

    const statusClass = (status) => {
        return {
            'En curso': 'bg-en-curso',
            Terminado: 'bg-terminado',
        }[status];
    };

    const changePage = (page) => {
        if (page >= 1 && page <= totalPages.value) {
            currentPage.value = page;
        }
    };

</script>


<style scoped>
    .badge {
        padding: 0.5em 1em;
        border-radius: 25px;
        font-size: 0.9em;
        font-weight: 600;
    }

    .badge {
        padding: 0.5em 1em;
        border-radius: 25px;
        font-size: 0.9em;
    }

        .bg-En curso { background-color: #d4edda; color: #155724; }
        .bg-Terminado { background-color: #f8d7da; color: #721c24; }
        .bg-Pendiente { background-color: #fff3cd; color: #856404; }

    .is-invalid {
        border-color: #dc3545 !important;
    }

    .text-danger {
        color: #dc3545;
        font-size: 0.875em;
    }

    .bg-en-curso {
        background-color: #d4edda;
        color: #155724;
    }

    .bg-terminado {
        background-color: #f8d7da;
        color: #721c24;
    }

    .bg-pendiente {
        background-color: #fff3cd;
        color: #856404;
    }

    .is-invalid {
        border-color: #dc3545 !important;
    }

    .text-danger {
        color: #dc3545;
        font-size: 0.875em;
        margin-top: 0.25em;
    }

    .modal-footer {
        display: flex;
        justify-content: center;
        gap: 1rem;
        padding: 0;
        flex-direction: row;
        border: 0px;
    }

    .fa-file-pdf {
        font-size: 1.5em;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .fa-file-pdf:hover {
        transform: scale(1.1);
    }

    .container {
        width: 100%;
        margin: 0 auto;
        padding: 2rem;
        background-color: #f8f5f0;
        min-height: 100vh;
        border-radius: 25px;
    }

    .table-docs td:nth-child(2) {
        text-align: center;
    }

    .fa-file-pdf{
        cursor: pointer;
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
        background-color: #f5ebd8; /* Lighter yellow on hover */
        color: black; /* Dark text on hover */
    }

    .page-item.active .page-link {
        background-color: #8f754f; /* Active page in black */
        color: white; /* White text for active page */
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

    .occupation{
        width: 55px;
        height: 35px;
        position: relative;
        border: 0px solid #ccc;
        margin: 4px;
        border-radius: 25%;
        background-color: #d7b377;
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
        border: 1px solid #acacac; /* Match table border */
    }

    .custom-border-table{
        margin-bottom: 0px;
    }

    .custom-big-table thead tr th {
        vertical-align: middle;
        text-align: center; /* Optional: for horizontal centering */
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

    .fiador {
        background-color: #f8f5f0;
        border-radius: 25px;
        padding: 24px;
    }

    @media (max-width: 768px) {
        .container {
           /* padding: 1rem;*/
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
