<template>

    <div v-if="loading" class="position-fixed start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0, 0, 0, 0.5); z-index: 1050; top: 0px;">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden"></span>
        </div>
    </div>

    <div class="container">

        <div class="row seccion mt-4 mainSeccion" v-if="!tenantId">
            <div class="col-12">
                <h2 class="titulo-seccion">📄Solicitudes de Arrendamiento</h2>
            </div>
            <div class="col-12 mb-4">
                <div class="row">
                    <div class="col-6">
                        <div class="campo">
                            <select class="entrada pr-2" v-model="sortByStatus" required style="background-color: #f8f5f0 ;">
                                <option value="" selected disabled>Status</option>
                                <option v-for="item in statuses" :key="item" :value="item">{{ item }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="campo">
                            <select class="entrada" v-model="sortByInmueble" required style="background-color: #f8f5f0 ;">
                                <option value="" selected disabled>Propiedades</option>
                                <option value="Todas">Todas</option>
                                <option v-for="item in properties" :key="item" :value="item.sku">{{ item.sku }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="grupo-campos">
                    <div style="overflow-x: auto; background-color: transparent;">
                        <table class="table table-bordered table-hover table-striped align-middle text-center custom-big-table" style="border-radius: 25px; overflow: hidden;">
                            <thead style="background-color: gray !important; ">
                                <tr>
                                    <th>Aplicante Principal</th>
                                    <th>Co-solicitantes</th>
                                    <th>Propiedad</th>
                                    <th>Mudanza</th>
                                    <th>Ingreso Total</th>
                                    <th>Mascotas</th>
                                    <th>Inquilinos</th>
                                    <th>Parqueadero</th>
                                    <th>Status</th>
                                    <th>Fecha de solicitud</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in paginatedApplications" :key="item.id" @click="tenantApplicationClicked(item)" style="cursor: pointer;">
                                <!-- Aplicante Principal -->
                                    <td>
                                        <div>
                                            <strong>{{ getPrincipal(item)?.full_name }}</strong><br>
                                            <small>{{ getPrincipal(item)?.birthdate }}<br>{{ getPrincipal(item)?.nationality }}</small>
                                        </div>
                                    </td>

                                    <!-- Co-solicitantes -->
                                    <td>
                                        <div v-if="item.financial_responsibles.length > 1">
                                            <i class="fa-solid fa-handshake mr-1" style="color: black"></i>
                                            {{ item.financial_responsibles.length - 1 }}
                                        </div>
                                    </td>

                                    <!-- Inmueble -->
                                    <td>{{ item.property.sku }}</td>

                                    <!-- Mudanza -->
                                    <td>{{ item.move_in_date }}</td>

                                    <!-- Ingreso Total -->
                                    <td>
                                        {{ formatCurrency(totalIncome(item.financial_responsibles)) }}
                                    </td>

                                    <!-- Mascotas -->
                                    <td>
                                        <span v-for="(pets, index) in item.pets" :key="index" class="mx-1">
                                        <i :class="getPetIcon(pets.type)"
                                            :style="{ color: pets.sex === 'Macho' ? '#007bff' : '#e83e8c' }"></i>
                                        </span>
                                    </td>

                                    <!-- Inquilinos -->
                                    <td >
                                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0px; justify-items: center;">
                                            <span v-for="(coh, index) in item.cohabitants"
                                                :key="index"
                                                class="occupation"
                                                style="display: flex; align-items: center; justify-content: center;">
                                                <i :class="getOccupationIcon(coh.occupation)" style="font-size: 16px; color: black;"></i>
                                                <span style="font-size: 12px; line-height: 1; background-color: white; color:black; padding: 3px; border-radius: 25px; margin-left: 5px; font-weight: 700;">{{ coh.age }}</span>
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Parqueadero -->
                                    <td>
                                        <span v-for="(park, index) in item.parking_needs" :key="index" class="mx-1">
                                        <i :class="getVehicleIcon(park.vehicle_type)" class="text-dark"></i>
                                        </span>
                                    </td>

                                    <!-- Status -->
                                    <td>{{ item.status }}</td>

                                    <!-- Fecha de solicitud -->
                                    <td>{{ formatDate(item.created_at) ?? '—' }}</td>
                                </tr>
                                <tr v-if="paginatedApplications.length === 0">
                                    <td colspan="10" class="text-center py-4">No hay aplicaciones</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <nav>
                    <ul class="pagination" >
                        <li class="page-item" :class="{ disabled: currentPage === 1 }">
                            <a class="page-link" @click="changePage(currentPage - 1)" href="#"><i class="fa fa-arrow-left"></i></a>
                        </li>
                        <li class="page-item"
                            v-for="page in totalPages"
                            :key="page"
                            :class="{ active: currentPage === page }"
                            >
                            <a class="page-link" @click="changePage(page)" href="#">{{ page }}</a>
                        </li>
                        <li class="page-item" :class="{ disabled: currentPage === totalPages || totalPages === 0 }">
                            <a class="page-link" @click="changePage(currentPage + 1)" href="#"><i class="fa fa-arrow-right"></i></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="row" v-if="tenantId">
            <!-- <div class="col-12 mb-3">
                <button  style="width: 40px; height: 40px; border: 0px solid; border-radius: 25px; margin-bottom: 10px; background-color: #8f754f; color: white; font-weight: 600;" @click="returnToMain"><i class="fa fa-arrow-left"></i></button>
            </div> -->
            <div class="col-12 mb-3 d-flex justify-content-between align-items-center">
                <button
                    style="width: 40px; height: 40px; border: 0px solid; border-radius: 25px; margin-bottom: 10px; background-color: #8f754f; color: white; font-weight: 600;"
                    @click="returnToMain">
                    <i class="fa fa-arrow-left"></i>
                </button>
                <div>
                    <button
                        style="margin-right: 10px; background-color: #28a745; color: white; border: none; padding: 8px 16px; border-radius: 25px; font-weight: 600;"
                        @click="approveTenantApplication" v-if="formData.status == 'Pendiente' || formData.status == 'Guardado'">
                        Aprobar solicitud
                    </button>
                    <button
                        style="margin-right: 10px; background-color: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 25px; font-weight: 600;"
                        @click="denyTenantApplication" v-if="formData.status == 'Pendiente' || formData.status == 'Guardado'">
                        Denegar solicitud
                    </button>
                    <button
                        style="background-color: #e29d36; color: white; border: none; padding: 8px 16px; border-radius: 25px; font-weight: 600;"
                        @click="saveTenantApplication" v-if="formData.status == 'Pendiente' || formData.status == 'Guardado'">
                        Guardar solicitud
                    </button>
                </div>
            </div>
            <div class="col-12">
                <div class="formulario" >
                    <div class="seccion">
                        <h2 class="titulo-seccion">📌 Información de la Propiedad</h2>
                        <div class="contenedor-campos" style="gap: 10px !important;">
                            <div class="grupo-campos ">
                                <div style="overflow-x: auto;">
                                    <table class="table table-striped table-bordered custom-border-table" style="border-radius: 25px; overflow: hidden;">
                                        <tbody>
                                            <tr>
                                                <th style="width: 50%;">Propiedad de interés</th>
                                                <td>{{ formData.property.sku }}</td>
                                            </tr>
                                            <tr>
                                                <th>Fecha deseada de mudanza</th>
                                                <td>
                                                    {{ formData.move_in_date }}
                                                    <br>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="seccion" v-for="(responsable, index) in formData.financial_responsibles" :key="index">

                        <div class="encabezado-seccion row">
                                <h2 class="titulo-seccion" v-if="index == 0" style="padding-left: 18px;">👤 Aplicante principal</h2>
                                <h2 class="titulo-seccion" v-if="index > 0" style="padding-left: 18px;">👤 Co-solicitante #{{ index }}</h2>
                        </div>


                        <div class="contenedor-campos">

                            <div class="grupo-campos">
                                <h3 class="subtitulo">Información Personal</h3>
                                <div style="overflow-x: auto;">
                                    <table class="table table-striped custom-border-table table-bordered " style="border-radius: 25px; overflow: hidden; ">
                                        <tbody>
                                            <tr>
                                                <th style="width: 50%;">Nombre completo</th>
                                                <td>{{ responsable.full_name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Correo electrónico</th>
                                                <td>{{ responsable.email }}</td>
                                            </tr>
                                            <tr>
                                                <th>Número de teléfono</th>
                                                <td>{{ responsable.phone_number }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tipo de documento</th>
                                                <td>{{ responsable.document_type }}</td>
                                            </tr>
                                            <tr>
                                                <th>Número de documento</th>
                                                <td>{{ responsable.document_number }}</td>
                                            </tr>
                                            <tr>
                                                <th>Fecha de nacimiento</th>
                                                <td>{{ responsable.birthdate }}</td>
                                            </tr>
                                            <tr>
                                                <th>Nacionalidad</th>
                                                <td>{{ responsable.nationality }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="grupo-campos">
                                <h3 class="subtitulo">Situación Laboral</h3>
                                <div class="campos-grid">
                                    <div style="overflow-x: auto;">
                                        <table class="table table-striped custom-border-table table-bordered" style="border-radius: 25px; overflow: hidden;">
                                            <tbody>
                                                <tr>
                                                    <th style="width: 50%;">Tipo de empleo</th>
                                                    <td>{{ responsable.employment_status }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Salario mensual (COP)</th>
                                                    <td>{{ responsable.monthly_salary }}$</td>
                                                </tr>
                                                <tr>
                                                    <th>Fecha de inicio en el trabajo actual</th>
                                                    <td>{{ responsable.start_current_job_date }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="grupo-campos">
                                <h3 class="subtitulo">Documentos Requeridos (PDF)</h3>
                                <div class="campos-grid">
                                    <div style="overflow-x: auto;">
                                        <table class="table table-striped custom-border-table table-bordered table-docs" style="border-radius: 25px; overflow: hidden;">
                                            <tbody>
                                                <!-- Documento de identidad -->
                                                <tr>
                                                    <th>Documento de identidad</th>
                                                    <td style="width: auto;">
                                                        <a v-if="responsable.document_id" @click.prevent="openModal(responsable.document_id_url, `Documento de identidad`)">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                    </td>
                                                </tr>

                                                <!-- Show documents based on employment status -->
                                                <template v-if="responsable.employment_status === 'Empleado'">
                                                    <!-- Certificado laboral -->
                                                    <tr>
                                                        <th>Certificado laboral</th>
                                                        <td>
                                                            <a v-if="responsable.document_certf" @click.prevent="openModal(responsable.document_certf_url, `Certificado laboral`)">
                                                                <i class="fas fa-file-pdf"></i>
                                                            </a>
                                                        </td>
                                                    </tr>

                                                    <!-- Desprendible de pago 1, 2, 3 -->
                                                    <tr v-for="n in 3" :key="n">
                                                        <th>Desprendible de pago {{ n }}</th>
                                                        <td>
                                                            <a v-if="responsable[`document_pay_${n}`]" @click.prevent="openModal(responsable[`document_pay_${n}_url`] , `Desprendible de pago ${n}`)">
                                                                <i class="fas fa-file-pdf"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </template>

                                                <template v-else-if="responsable.employment_status === 'Pensionado'">
                                                    <!-- Certificado de cámara de comercio -->
                                                    <tr>
                                                        <th>Constancia de Pension</th>
                                                        <td>
                                                            <a v-if="responsable.document_certf" @click.prevent="openModal(responsable.document_certf_url, `Constancia de Pension`)">
                                                                <i class="fas fa-file-pdf"></i>
                                                            </a>
                                                        </td>
                                                    </tr>

                                                    <!-- Extracto bancario 1, 2, 3 -->
                                                    <tr v-for="n in 3" :key="n">
                                                        <th>Extracto bancario {{ n }}</th>
                                                        <td>
                                                            <a v-if="responsable[`document_pay_${n}`]" @click.prevent="openModal(responsable[`document_pay_${n}_url`], `Extracto bancario ${n}`)">
                                                                <i class="fas fa-file-pdf"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </template>

                                                <template v-else>
                                                    <!-- Constancia de Pension -->
                                                    <tr>
                                                        <th>Certificado de cámara de comercio</th>
                                                        <td>
                                                            <a v-if="responsable.document_certf" @click.prevent="openModal(responsable.document_certf_url, `Certificado de cámara de comercio`)">
                                                                <i class="fas fa-file-pdf"></i>
                                                            </a>
                                                        </td>
                                                    </tr>

                                                    <!-- Extracto bancario 1, 2, 3 -->
                                                    <tr v-for="n in 3" :key="n">
                                                        <th>Extracto bancario {{ n }}</th>
                                                        <td>
                                                            <a v-if="responsable[`document_pay_${n}`]" @click.prevent="openModal(responsable[`document_pay_${n}_url`], `Extracto bancario ${n}`)">
                                                                <i class="fas fa-file-pdf"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="grupo-campos fiador" v-if="responsable.guarantor_full_name!=''">
                            <!-- <div v-if="necesitaFiador(responsable)" class="grupo-campos fiador"> -->
                                <h3 class="subtitulo">📝 Información del Fiador</h3>
                                <div class="campos-grid">
                                    <div style="overflow-x: auto;">
                                        <table class="table table-striped table-bordered custom-border-table" style="border-radius: 25px; overflow: hidden;">
                                            <tbody>
                                            <tr>
                                                <th style="width: 50%;">Nombre completo del fiador</th>
                                                <td>{{ responsable.guarantor_full_name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tipo de documento</th>
                                                <td>{{ responsable.guarantor_document_type }}</td>
                                            </tr>
                                            <tr>
                                                <th>Número de documento</th>
                                                <td>{{ responsable.guarantor_document_number }}</td>
                                            </tr>
                                            <tr>
                                                <th>Certificado de libertad</th>
                                                <td>
                                                    <a v-if="responsable.guarantor_property_cert" @click.prevent="openModal(responsable.guarantor_property_cert_url, `Certificado de libertad`)">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="grupo-campos" v-if="responsable.additional_incomes.length > 0">
                                <h3 class="subtitulo" style="margin-bottom: 0;">➕ Ingresos Adicionales</h3>
                                <div class="campos-grid" style="margin-top: 20px;">
                                    <div style="overflow-x: auto;">
                                        <table class="table table-striped table-bordered custom-border-table" style="border-radius: 25px; overflow: hidden; margin-bottom: 0;">
                                            <thead>
                                                <tr>
                                                    <th>Monto mensual</th>
                                                    <th>Fuente de ingreso</th>
                                                    <th>Certificado de ingreso (PDF)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(ingreso, i) in responsable.additional_incomes" :key="i">
                                                    <td>{{ ingreso.monthly_amount }}$</td>
                                                    <td>{{ ingreso.description }}</td>
                                                    <td>
                                                        <a v-if="ingreso.income_cert" @click.prevent="openModal(ingreso.income_cert_url, `Certificado de ingreso`)">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="seccion">
                        <h2 class="titulo-seccion" style="margin-bottom: 0px;">👥 Personas que Vivirán en la Propiedad</h2>
                        <div class="campos-grid" style="padding: 24px; margin-top: 20px;">
                            <div style="overflow-x: auto;">
                                <table class="table table-striped table-bordered custom-border-table" style="border-radius: 25px; overflow: hidden; margin-bottom: 0px;">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>Número de documento</th>
                                            <th>Ocupación</th>
                                            <th>Edad (años)</th>
                                            <th>Parentesco</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(conviviente, i) in formData.cohabitants" :key="i">
                                            <td>{{ conviviente.first_name }}</td>
                                            <td>{{ conviviente.last_name }}</td>
                                            <td>{{ conviviente.document_number }}</td>
                                            <td>{{ conviviente.occupation }}</td>
                                            <td>{{ conviviente.age }}</td>
                                            <td>{{ conviviente.relationship }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="seccion" v-if="formData.pets.length > 0">
                        <h2 class="titulo-seccion" style="margin-bottom: 0px;">🐾 Mascotas</h2>
                        <div class="campos-grid" style="padding: 24px; margin-top: 20px;">
                            <div style="overflow-x: auto;">
                                <table class="table table-striped table-bordered custom-border-table" style="border-radius: 25px; overflow: hidden; margin-bottom: 0px;">
                                    <thead>
                                        <tr>
                                            <th>Tipo de mascota</th>
                                            <th>Sexo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(mascota, i) in formData.pets" :key="i">
                                            <td>{{ mascota.type }}</td>
                                            <td>{{ mascota.sex }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="seccion" v-if="formData.parking_needs.length > 0">
                        <h2 class="titulo-seccion" style="margin-bottom: 0;">🚗 Parqueaderos Requeridos</h2>
                        <div class="campos-grid" style="padding: 24px; margin-top: 20px;">
                            <div style="overflow-x: auto;">
                                <table class="table table-striped table-bordered custom-border-table" style="border-radius: 25px; overflow: hidden; margin-bottom: 0px;">
                                    <thead>
                                        <tr>
                                            <th>Tipo de Vehiculo</th>
                                            <th>Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="countVehicles('Carro') > 0">
                                            <td>Carro</td>
                                            <td>{{ countVehicles('Carro') }}</td>
                                        </tr>
                                        <tr v-if="countVehicles('Moto') > 0">
                                            <td>Moto</td>
                                            <td>{{ countVehicles('Moto') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>

    <div v-if="showModal" class="modal" @click.self="closeModal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title">{{ documentSelected }}</span>
                <button class="close-button" @click="closeModal">×</button>
            </div>
            <div class="modal-body">
                <iframe :src="pdfUrl" width="100%" height="100%" style="border: none;"></iframe>
            </div>
        </div>
    </div>

</template>



<script setup>

    import { ref, computed, onMounted } from 'vue';
    import axios from 'axios';
    import Swal from 'sweetalert2';

    import tenantApplicationApi from '@/api/tenantApplication';
    import { useStore } from 'vuex'
    import property from '@/api/property.js';

    const propertyResource = new property();

    const tenantApplicationResource = new tenantApplicationApi();
    const store = useStore()

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

        allTenantApplications.value = store.getters.getTenantApplications;
        if(allTenantApplications.value.length == 0){
            await tenantApplicationResource.getTenantApplications().then(response => {
                if (response.success) {
                    allTenantApplications.value = response.data;
                    store.commit('setTenantApplications', allTenantApplications.value)
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
    });

    function formatDate(date) {
        if (!date) return null;
        return new Date(date).toLocaleString();
    };

    async function approveTenantApplication(){

        const resultado = await Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Deseas aprobar este aplicante?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, aprobar',
            cancelButtonText: 'No, cancelar',
            reverseButtons: true,
        })

        loading.value = true;

        if (resultado.isConfirmed) {

            await tenantApplicationResource.approveTenantApplication({tenantID: tenantId.value})
                .then((response) => {
                    if(response && response.success){
                        if (response.success) {
                            allTenantApplications.value = response.data;
                            store.commit('setTenantApplications', allTenantApplications.value)
                            Swal.fire({
                                icon: 'success',
                                title: 'Aplicacion Aprobada',
                                text: 'La aplicacion ha sido aprobada exitosamente y el usuario ha sido notificado',
                                confirmButtonText: 'Aceptar'
                            });
                        } else {
                            Swal.fire(
                                'Error',
                                'Hubo un error, intenta despues.',
                                'error'
                            );
                        }
                    }else{
                        Swal.fire(
                                'Error',
                                'Hubo un error, intenta despues.',
                                'error'
                            );
                    }
                })
                .catch((error) => {
                    Swal.fire(
                                'Error',
                                'Hubo un error, intenta despues.',
                                'error'
                            );
                });

        }

        loading.value = false;

    }

    async function denyTenantApplication(){

        const resultado = await Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Deseas rechazar este aplicante? Esto eliminara toda su informacion de la base de datos y notificara el usario de la decision',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, rechazar',
            cancelButtonText: 'No, cancelar',
            reverseButtons: true,
        })

        loading.value = true;

        if (resultado.isConfirmed) {

            await tenantApplicationResource.denyTenantApplication({tenantID: tenantId.value})
                .then((response) => {
                    if(response && response.success){
                        if (response.success) {
                            allTenantApplications.value = response.data;
                            store.commit('setTenantApplications', allTenantApplications.value)
                            Swal.fire({
                                icon: 'success',
                                title: 'Aplicacion Rechazada',
                                text: 'La aplicacion ha sido rechazada exitosamente y el usuario ha sido notificado',
                                confirmButtonText: 'Aceptar'
                            });
                        } else {
                            Swal.fire(
                                'Error',
                                'Hubo un error, intenta despues.',
                                'error'
                            );
                        }
                    }else{
                        Swal.fire(
                                'Error',
                                'Hubo un error, intenta despues.',
                                'error'
                            );
                    }
                })
                .catch((error) => {
                    Swal.fire(
                            'Error',
                            'Hubo un error, intenta despues.',
                            'error'
                        );
                });

        }

        loading.value = false;

    }

    async function saveTenantApplication(){

        loading.value = true;

        await tenantApplicationResource.saveTenantApplication({tenantID: tenantId.value})
            .then((response) => {
                if(response && response.success){
                    if (response.success) {
                        allTenantApplications.value = response.data;
                        store.commit('setTenantApplications', allTenantApplications.value)
                        Swal.fire({
                            icon: 'success',
                            title: 'Aplicacion Guardada',
                            text: 'La aplicacion ha sido guardada exitosamente',
                            confirmButtonText: 'Aceptar'
                        });
                    } else {
                        Swal.fire(
                            'Error',
                            'Hubo un error, intenta despues.',
                            'error'
                        );
                    }
                }else{
                    Swal.fire(
                            'Error',
                            'Hubo un error, intenta despues.',
                            'error'
                        );
                }
            })
            .catch((error) => {
                Swal.fire(
                        'Error',
                        'Hubo un error, intenta despues.',
                        'error'
                    );
            });

        loading.value = false;

    }

    const filteredTenantApplications = computed(() => {
        currentPage.value = 1;
        return allTenantApplications.value.filter(app => {
            const matchStatus = !sortByStatus.value || sortByStatus.value === 'Todas' || app.status === sortByStatus.value;
            const matchProperty = !sortByInmueble.value || sortByInmueble.value === 'Todas' || app.property_id === sortByInmueble.value;
            return matchStatus && matchProperty;
        });
    });

    const itemsPerPage = ref(10);
    const currentPage = ref(1);

    const totalPages = computed(() => {
        return Math.ceil(filteredTenantApplications.value.length / itemsPerPage.value);
    });

    const paginatedApplications = computed(() => {
        const startIndex = (currentPage.value - 1) * itemsPerPage.value;
        const endIndex = startIndex + itemsPerPage.value;
        return filteredTenantApplications.value.slice(startIndex, endIndex);
    });

    const changePage = (page) => {
        if (page >= 1 && page <= totalPages.value) {
            currentPage.value = page;
        }
    };

    const loading = ref(false);

    const formData = ref([]);
    const tenantId = ref(null);

    const showModal = ref(false);
    const pdfUrl = ref('');
    const documentSelected = ref('')

    function openModal(pdf_Url, name) {
        pdfUrl.value = pdf_Url;
        documentSelected.value = name;
        showModal.value = true;
    }

    function closeModal() {
        showModal.value = false;
        pdfUrl.value = '';
    }

    function countVehicles(vehicleType) {
        return formData.value.parking_needs.filter(parqueadero => parqueadero.vehicle_type === vehicleType).length;
    }

    function tenantApplicationClicked(item){
        formData.value = item;
        tenantId.value = item.id;
    }

    function returnToMain(){
        formData.value = null;
        tenantId.value = null;
    }

    // Total income calculation
    function totalIncome(financials) {
        return financials.reduce((sum, f) => {
            let base = parseFloat(f.monthly_salary || 0);
            let additional = f.additional_incomes?.reduce((acc, inc) => acc + parseFloat(inc.monthly_amount || 0), 0) || 0;
            return sum + base + additional;
        }, 0);
    }

    // Format as currency
    function formatCurrency(amount) {
        return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(amount);
    }

    // Get the principal applicant
    function getPrincipal(item) {
        return item.financial_responsibles.find(fr => fr.principal);
    }

    function getPetIcon(type) {
        switch (type.toLowerCase()) {
            case 'perro': return 'fa-solid fa-dog';
            case 'gato': return 'fa-solid fa-cat';
            case 'pez': return 'fa-solid fa-fish';
            case 'ave': return 'fa-solid fa-dove';
            case 'conejo': return 'fa-solid fa-carrot';
            case 'tortuga': return 'fa-solid fa-shield-cat';
            case 'iguana': return 'fa-solid fa-dragon';
            case 'gallina': return 'fa-solid fa-kiwi-bird';
            default: return 'fa-solid fa-paw';
        }
    }

    function getOccupationIcon(occupation) {
        switch (occupation.toLowerCase()) {
            case 'hogar': return 'fa-solid fa-house-user';
            case 'empleado': return 'fa-solid fa-briefcase';
            case 'independiente': return 'fa-solid fa-user-tie';
            case 'estudiante': return 'fa-solid fa-user-graduate';
            case 'pensionado': return 'fa-solid fa-person-cane';
            default: return 'fa-solid fa-user';
        }
    }

    function getVehicleIcon(type) {
        switch (type.toLowerCase()) {
            case 'carro': return 'fa-solid fa-car';
            case 'moto': return 'fa-solid fa-motorcycle';
            default: return 'fa-solid fa-car-side';
        }
    }

    const sortByStatus = ref('');
    const sortByInmueble = ref('');

    const statuses = ref(["Todos", "Pendiente", "Pendiente Contrato", "Pendiente Rechazado", "Guardado", "Guardado Rechazado", "En Curso", "Terminado" ]);
    const properties = ref();


    //DELETE FROM HERE

    function getRandomItem(arr) {
        return arr[Math.floor(Math.random() * arr.length)];
    }

    const property_ids = [
        "SE-APTO-201", "SE-APTO-202", "SE-APTO-301", "SE-APTO-302", "SE-APTO-401", "SE-APTO-402", "SE-APTO-501", "SE-APTO-502",
        "SE-PARQ-A1", "SE-PARQ-A2", "SE-PARQ-B1", "SE-PARQ-B2", "SE-PARQ-C1", "SE-PARQ-C2", "SE-PARQ-D1", "SE-PARQ-D2",
        "SE-LOC-1", "FO-APTO-201", "FO-APTO-301", "FO-PARQ-1", "FO-PARQ-2", "PE-APTO-101", "PE-APTO-102", "PE-APTO-103"
    ];

    const statuse = ["Pendiente", "Pendiente Rechazado", "Pendiente Contrato", "Guardado", "Guardado Rechazado", "En Curso", "Terminado"];
    const occupations = ['hogar', 'empleado', 'independiente', 'estudiante', 'pensionado'];
    const pet_types = ['perro', 'gato', 'pez', 'ave', 'conejo', 'tortuga', 'iguana', 'gallina', "otro"];
    const pet_sexes = ["Macho", "Hembra"];
    const vehicle_types = ["Carro", "Moto"];
    const namePool = [
        "Carlos Pérez", "María Rodríguez", "Andrés Gómez", "Luisa Martínez", "Javier López",
        "Camila Torres", "Mateo Ramírez", "Valentina Castro", "Juan Herrera", "Sofía Morales"
        ];


    function generateRandomDate(startYear = 2023, endYear = 2025) {
        const start = new Date(`${startYear}-01-01`);
        const end = new Date(`${endYear}-12-31`);
        return new Date(start.getTime() + Math.random() * (end.getTime() - start.getTime())).toISOString().split('T')[0];
    }

    function createApplication(id) {
        const financialCount = Math.floor(Math.random() * 4) + 1;
        const financials = Array.from({ length: financialCount }).map((_, i) => ({
            full_name: getRandomItem(namePool),
            email: `person${id}${i}@example.com`,
            phone_number: `+1${Math.floor(Math.random() * 1000000000)}`,
            document_type: "Passport",
            document_number: `ID-${id}${i}`,
            birthdate: `${Math.floor(Math.random() * 41) + 20} años`, // Random between 20 and 60
            nationality: getRandomItem(["Chamo", "Veneco", "Costeño", "Cobenero", "Colombiano"]),
            employment_status: getRandomItem(['Empleado - Termino Fijo', 'Empleado - Termino Indefinido', 'Empleado - Prestacion de Servicios', "Independiente", "Independiente"]),
            monthly_salary: `${Math.floor(Math.random() * 3000 + 4000)}`,
            start_current_job_date: generateRandomDate(2015, 2023),
            document_id: 'https://pdfobject.com/pdf/sample.pdf',
            document_certf: 'https://pdfobject.com/pdf/sample.pdf',
            document_pay_1: 'https://pdfobject.com/pdf/sample.pdf',
            document_pay_2: 'https://pdfobject.com/pdf/sample.pdf',
            document_pay_3: 'https://pdfobject.com/pdf/sample.pdf',
            guarantor_full_name: `Guarantor ${id}`,
            guarantor_document_type: "ID Card",
            guarantor_document_number: `G-${id}${i}`,
            guarantor_property_cert: 'https://pdfobject.com/pdf/sample.pdf',
            principal: i === 0,
            additional_incomes: Math.random() > 0.4 ? [
            {
                monthly_amount: `${Math.floor(Math.random() * 2000 + 500)}`,
                description: getRandomItem(["Freelance Work", "Rental Income", "Investments"]),
                income_cert: 'https://pdfobject.com/pdf/sample.pdf'
            }
            ] : []
        }));

        return {
            id,
            property_id: getRandomItem(property_ids),
            property: {
                sku: getRandomItem(property_ids),
            },
            move_in_date: generateRandomDate(),
            created_at: generateRandomDate(2023, 2024),
            status: getRandomItem(statuse),
            financial_responsibles: financials,
            cohabitants: Array.from({ length: Math.floor(Math.random() * 6 + 1) }).map((_, i) => ({
            first_name: `CoHabit${i}`,
            last_name: `User${id}`,
            document_number: `DOC-${id}-${i}`,
            occupation: getRandomItem(occupations),
            age: `${Math.floor(Math.random() * 50 + 18)}`,
            relationship: "Friend"
            })),
            pets: Array.from({ length: Math.floor(Math.random() * 4) }).map(() => ({
            type: getRandomItem(pet_types),
            sex: getRandomItem(pet_sexes)
            })),
            parking_needs: Array.from({ length: Math.floor(Math.random() * 3) }).map(() => ({
            vehicle_type: getRandomItem(vehicle_types)
            }))
        };
    }

    //TO HERE

    const allTenantApplications = ref(Array.from({ length: 15 }).map((_, i) => createApplication(i + 6)));

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

    .table {
        --bs-table-bg: #f8f5f0;
        --bs-table-striped-bg: #f5efe8;
        --bs-table-hover-bg: #b0673c1c;
        margin-bottom: 0px !important;
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
        border-radius: 25px;
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



    .campo {
        margin-bottom: 0rem;
    }

    .etiqueta {
        display: block;
        margin-bottom: 0.5rem;
        color: #000000;
        font-weight: 500;
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
        padding: 0.75rem 1.5rem;
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

