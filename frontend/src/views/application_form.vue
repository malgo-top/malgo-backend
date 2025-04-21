<template>

    <div v-if="loading" class="position-fixed start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0, 0, 0, 0.5); z-index: 1050; top: 0px;">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden"></span>
        </div>
    </div>

    <div class="container">
        <h1 class="titulo-principal">Formulario de Aplicación para Arrendamiento</h1>

        <form @submit.prevent="validarFormulario" class="formulario">
            <div class="seccion">
                <h2 class="titulo-seccion">📌 Información de la Propiedad</h2>
                <div class="contenedor-campos" style=" gap: 10px !important;">
                    <div class="grupo-campos">
                        <div class="campo" style="margin-bottom: 10px;">
                            <label class="etiqueta">Propiedad de interés</label>
                            <input v-model="formData.property_id" type="text" class="entrada" disabled required placeholder="Propiedad de interés">
                            <!-- <select v-model="formData.property_id" class="entrada" required>
                                <option v-for="propiedad in propiedades" :key="propiedad.id" :value="propiedad.id">
                                    {{ propiedad.direccion }}
                                </option>
                            </select> -->
                            <!-- <p class="ayuda" >Seleccione la propiedad que desea arrendar</p> -->
                        </div>
                        <div class="campo">
                            <label class="etiqueta">Fecha deseada de mudanza</label>
                            <input type="date" v-model="formData.move_in_date" class="entrada" :min="today" required>
                            <p class="ayuda"  style="margin-bottom: 0;">Fecha aproximada en que planea ocupar la propiedad</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="seccion" v-for="(responsable, index) in formData.FinancialResponsible" :key="index">

                <div class="encabezado-seccion row"  style="display: flex; flex-direction: row; justify-content: flex-start; align-content: center;">
                        <h2 class="titulo-seccion" v-if="index == 0" style="padding-left: 18px;">👤 Aplicante principal</h2>
                        <h2 class="titulo-seccion" v-if="index > 0" style="padding-left: 18px; width: auto;">👤 Co-solicitante #{{ index }}</h2>
                        <button v-if="index > 0" type="button" class="boton-eliminar" style="margin-bottom: 24px !important; height: 28px !important; width: 28px !important; margin-left: 10px; display: flex; flex-direction: column; align-content: center; justify-content: center;" @click="eliminarResponsable(index)">
                            <div style="font-size: 20px; margin-top: -2px; margin-left: -3px;">x</div>
                        </button>
                </div>


                <div class="contenedor-campos">
                    <div class="grupo-campos">
                        <h3 class="subtitulo">Información Personal</h3>
                        <div class="campos-grid">
                            <div class="campo">
                                <label class="etiqueta">Nombre completo</label>
                                <input v-model="responsable.full_name" type="text" class="entrada" required placeholder="Nombre completo">
                            </div>

                            <div class="campo">
                                <label class="etiqueta">Correo electrónico</label>
                                <input v-model="responsable.email" type="email" class="entrada" required placeholder="Correo electrónico">
                            </div>

                            <div class="campo">
                                <label class="etiqueta">Numero de telefono</label>
                                <input v-model="responsable.phone_number" type="number" class="entrada" required placeholder="Numero de telefono">
                            </div>

                            <div class="campo">
                                <label class="etiqueta">Tipo de documento</label>
                                <select v-model="responsable.document_type" class="entrada" required>
                                    <option value="" disabled selected>Seleccione un tipo de documento</option>
                                    <option value="Cédula de Ciudadanía">Cédula de Ciudadanía</option>
                                    <option value="Pasaporte">Pasaporte</option>
                                    <option value="Cédula de Extranjería">Cédula de Extranjería</option>
                                    <option value="Permiso por Protección Temporal">Permiso por Protección Temporal</option>
                                </select>
                            </div>

                            <div class="campo">
                                <label class="etiqueta">Número de documento</label>
                                <input v-model="responsable.document_number" type="text" class="entrada" required placeholder="Número de documento">
                            </div>

                            <div class="campo">
                                <label class="etiqueta">Fecha de nacimiento</label>
                                <input v-model="responsable.birthdate" type="date" class="entrada" :max="today" required placeholder="Fecha de nacimiento">
                            </div>

                            <div class="campo">
                                <label class="etiqueta">Nacionalidad</label>
                                <input v-model="responsable.nationality" type="text" class="entrada" required placeholder="Nacionalidad">
                            </div>
                        </div>
                    </div>

                    <div class="grupo-campos">
                        <h3 class="subtitulo">Situación Laboral</h3>
                        <div class="campos-grid">
                            <div class="campo">
                                <label class="etiqueta">Tipo de empleo</label>
                                <select v-model="responsable.employment_status" class="entrada" required>
                                    <option value="" disabled selected>Seleccione un tipo de empleo</option>
                                    <option value="Empleado - Termino Fijo">Empleado - Termino Fijo</option>
                                    <option value="Empleado - Termino Indefinido">Empleado - Termino Indefinido</option>
                                    <option value="Empleado - Prestacion de Servicios">Empleado - Prestacion de Servicios</option>
                                    <option value="Independiente">Independiente</option>
                                    <option value="Pensionado">Pensionado</option>
                                </select>
                            </div>

                            <div class="campo">
                                <label class="etiqueta">Salario mensual (COP)</label>
                                <input v-model="responsable.monthly_salary" type="number" class="entrada" required placeholder="Salario mensual (COP)">
                            </div>

                            <div class="campo">
                                <label class="etiqueta">Fecha de inicio en el trabajo actual</label>
                                <input v-model="responsable.start_current_job_date" type="date" :max="today" class="entrada" required>
                                <!-- <p class="ayuda"  style="margin-bottom: 0;">Si tiene menos de 2 años en este trabajo, necesitamos un fiador con finca raiz</p> -->
                            </div>
                        </div>
                    </div>

                    <div class="grupo-campos">
                        <h3 class="subtitulo">Documentos Requeridos (PDF)</h3>
                        <div class="campos-grid">
                            <div class="campo">
                                <label class="etiqueta">Documento de identidad</label>
                                <input type="file" class="entrada pl-3" accept=".pdf" required
                                        @change="e => manejarArchivo(e, responsable, 'document_id')">
                            </div>

                            <!-- <template v-if="responsable.employment_status === 'Empleado'"> -->
                            <template v-if="['Empleado - Termino Fijo', 'Empleado - Termino Indefinido', 'Empleado - Prestacion de Servicios'].includes(responsable.employment_status)">
                                <div class="campo">
                                    <label class="etiqueta">Certificado laboral</label>
                                    <input type="file" class="entrada pl-3" accept=".pdf" required
                                        @change="e => manejarArchivo(e, responsable, 'document_certf')">
                                    <p class="ayuda"  style="margin-bottom: 0;">Debe incluir salario y antigüedad</p>
                                </div>

                                <div v-for="n in 3" :key="n" class="campo">
                                    <label class="etiqueta">Desprendible de pago {{ n }}</label>
                                    <input type="file" class="entrada pl-3" accept=".pdf" required
                                        @change="e => manejarArchivo(e, responsable, `document_pay_${n}`)">
                                </div>
                            </template>

                            <template v-else-if="responsable.employment_status === 'Independiente'">
                                <div class="campo">
                                    <label class="etiqueta">Certificado de cámara de comercio</label>
                                    <input type="file" class="entrada pl-3" accept=".pdf" required
                                        @change="e => manejarArchivo(e, responsable, 'document_certf')">
                                </div>

                                <div v-for="n in 3" :key="n" class="campo">
                                    <label class="etiqueta">Extracto bancario {{ n }}</label>
                                    <input type="file" class="entrada pl-3" accept=".pdf" required
                                        @change="e => manejarArchivo(e, responsable, `document_pay_${n}`)">
                                </div>
                            </template>

                            <template v-else-if="responsable.employment_status === 'Pensionado'">
                                <div class="campo">
                                    <label class="etiqueta">Constancia de Pensión</label>
                                    <input type="file" class="entrada pl-3" accept=".pdf" required
                                        @change="e => manejarArchivo(e, responsable, 'document_certf')">
                                </div>

                                <div v-for="n in 3" :key="n" class="campo">
                                    <label class="etiqueta">Extracto bancario {{ n }}</label>
                                    <input type="file" class="entrada pl-3" accept=".pdf" required
                                        @change="e => manejarArchivo(e, responsable, `document_pay_${n}`)">
                                </div>
                            </template>

                        </div>
                    </div>

                    <div v-if="necesitaFiador(responsable)" class="grupo-campos fiador">
                        <h3 class="subtitulo">📝 Información del Fiador</h3>
                        <div class="campos-grid">
                            <div class="campo">
                                <label class="etiqueta">Nombre completo del fiador</label>
                                <input v-model="responsable.guarantor_full_name" type="text" class="entrada" required placeholder="Nombre completo del fiador">
                            </div>

                            <div class="campo">
                                <label class="etiqueta">Tipo de documento</label>
                                <select v-model="responsable.guarantor_document_type" class="entrada" required>
                                    <option value="" disabled selected>Seleccione un tipo de documento</option>
                                    <option value="Cédula de Ciudadanía">Cédula de Ciudadanía</option>
                                    <option value="Pasaporte">Pasaporte</option>
                                    <option value="Cédula de Extranjería">Cédula de Extranjería</option>
                                    <option value="Permiso por Protección Temporal">Permiso por Protección Temporal</option>
                                </select>
                            </div>

                            <div class="campo">
                                <label class="etiqueta">Número de documento</label>
                                <input v-model="responsable.guarantor_document_number" type="text" class="entrada" required placeholder="Número de documento">
                            </div>

                            <div class="campo">
                                <label class="etiqueta">Certificado de libertad (PDF)</label>
                                <input type="file" class="entrada pl-3" accept=".pdf" required
                                        @change="e => manejarArchivo(e, responsable, 'guarantor_property_cert')">
                                <p class="ayuda">Certificado de libertad y tradición de la propiedad</p>
                            </div>
                        </div>
                    </div>

                    <div class="grupo-campos">
                        <h3 class="subtitulo" style="margin-bottom: 0;">➕ Ingresos Adicionales</h3>
                        <p class="ayuda" style="margin-top: 0; margin-left: 30px;">Usa este botón si cuentas con ingresos adicionales a tu {{ responsable.employment_status === 'Pensionado' ? 'pension' : responsable.employment_status === 'Independiente' ? 'negocio' : "empleo" }}.</p>

                        <div v-for="(ingreso, i) in responsable.AdditionalIncome" :key="i" style="margin-bottom: 20px;">
                            <div class="campos-grid ingreso" style="padding: 24px;">
                                <div class="campo">
                                    <input v-model="ingreso.monthly_amount" type="number" class="entrada" placeholder="Monto mensual (COP)">
                                </div>

                                <div class="campo">
                                    <input v-model="ingreso.description" type="text" class="entrada" placeholder="Fuente de ingreso">
                                </div>

                                <div class="campo">
                                    <input type="file" class="entrada  pl-3" accept=".pdf"
                                        @change="e => manejarArchivo(e, ingreso, 'income_cert')">
                                    <p class="ayuda"  style="margin-bottom: 0;">Adjunta un comprobante (recibo, estado de cuenta, contrato, etc.) que respalde este ingreso adicional</p>
                                </div>

                                <button type="button" class="boton-eliminar" @click="eliminarIngreso(index, i)">Eliminar</button>
                            </div>
                            <!-- <hr style="width: 100%; border: 1px dashed #d7b377;" v-if="i < responsable.AdditionalIncome.length - 1"> -->
                        </div>
                        <button type="button" class="boton-secundario" @click="agregarIngreso(index)" v-if=" responsable.AdditionalIncome.length <= 2">
                            Añadir Ingreso
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" class="boton-primario" @click="agregarResponsable" v-if="formData.FinancialResponsible.length <= 2">
                ➕ Añadir co-solicitante
            </button>
            <p class="ayuda " v-if="formData.FinancialResponsible.length <= 2" style="margin-bottom: 2rem;">Utiliza este botón si más de una persona será responsable del arriendo.</p>

            <div class="seccion">
                <h2 class="titulo-seccion" style="margin-bottom: 0px;">👥 Personas que Vivirán en la Propiedad</h2>
                <p class="ayuda" style="margin-left: 0px;">Importante: Esta sección debe incluir a todas las personas que residirán en el inmueble, aunque ya hayan sido ingresadas como solicitantes principales o co-solicitantes.</p>
                <div v-for="(conviviente, i) in formData.Cohabitant" :key="i" class="conviviente" style="margin-bottom: 20px;">
                    <div class="campos-grid" style="padding: 24px;">
                        <div class="campo">
                            <input v-model="conviviente.first_name" type="text" class="entrada" placeholder="Nombre" required>
                        </div>

                        <div class="campo">
                            <input v-model="conviviente.last_name" type="text" class="entrada" placeholder="Apellido" required>
                        </div>

                        <div class="campo">
                            <input v-model="conviviente.document_number" type="text" class="entrada" placeholder="Número de documento" required>
                        </div>

                        <div class="campo">
                            <select v-model="conviviente.occupation" class="entrada" required>
                                <option value="" disabled selected>Seleccione una ocupación</option>
                                <option value="Hogar">Hogar</option>
                                <option value="Empleado">Empleado</option>
                                <option value="Independiente">Independiente</option>
                                <option value="Estudiante">Estudiante</option>
                                <option value="Pensionado">Pensionado</option>
                            </select>
                        </div>

                        <div class="campo">
                            <input v-model="conviviente.age" type="number" :max="today" class="entrada" placeholder="Edad(años)" required>
                        </div>

                        <div class="campo">
                            <input v-model="conviviente.relationship" type="text" class="entrada" placeholder="Parentesco con el aplicante principal" required>
                        </div>

                        <button type="button" class="boton-eliminar" @click="eliminarConviviente(i)">Eliminar</button>

                    </div>
                    <!-- <hr style="width: 100%; border: 1px dashed #d7b377;" v-if="i < formData.Cohabitant.length - 1"> -->
                </div>
                <button type="button" class="boton-secundario" @click="agregarConviviente"  v-if="formData.Cohabitant.length <= 6">
                    Añadir Residente
                </button>
            </div>

            <div class="seccion">
                <h2 class="titulo-seccion" style="margin-bottom: 0px;">🐾 Mascotas</h2>
                <p class="ayuda" style="margin-left: 0px;">¿Tienes más de una mascota? Haz clic en "añadir mascota" por cada una. 🐶🐱</p>
                <div v-for="(mascota, i) in formData.Pet" :key="i" class="mascota" style="margin-bottom: 20px;">
                    <div class="campos-grid" style="padding: 24px;">
                        <div class="campo">
                            <select v-model="mascota.type" class="entrada" required>
                                <option value="" disabled selected>Seleccione una mascota</option>
                                <option value="Perro">Perro</option>
                                <option value="Gato">Gato</option>
                                <option value="Pez">Pez</option>
                                <option value="Ave">Ave</option>
                                <option value="Conejo">Conejo</option>
                                <option value="Tortuga">Tortuga</option>
                                <option value="Iguana">Iguana</option>
                                <option value="Gallina">Gallina</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>

                        <div class="campo">
                            <select v-model="mascota.sex" class="entrada" required>
                                <option value="" disabled selected>Seleccione un sexo</option>
                                <option value="Hembra">Hembra</option>
                                <option value="Macho">Macho</option>
                            </select>
                        </div>

                        <button type="button" class="boton-eliminar" @click="eliminarMascota(i)">Eliminar</button>
                    </div>
                    <!-- <hr style="width: 100%; border: 1px dashed #d7b377;" v-if="i < formData.Pet.length - 1"> -->
                </div>
                <button type="button" class="boton-secundario" @click="agregarMascota" v-if="formData.Pet.length <= 2">
                    Añadir Mascota
                </button>
            </div>

            <div class="seccion">
                <h2 class="titulo-seccion" style="margin-bottom: 0;">🚗 Parqueaderos Requeridos</h2>
                <p class="ayuda" style="margin-left: 0px;">Haz clic en "Añadir Vehículo" para cada vehículo que necesita parqueadero.</p>
                <div v-for="(parqueadero, i) in formData.ParkingNeed" :key="i" class="parqueadero" style="margin-bottom: 20px;">
                    <div class="campos-grid" style="padding: 24px;">
                        <div class="campo">
                            <select v-model="parqueadero.vehicle_type" class="entrada" required>
                                <option value="" disabled selected>Seleccione un vehiculo</option>
                                <option value="Carro">Carro</option>
                                <option value="Moto">Moto</option>
                            </select>
                        </div>

                        <button type="button" class="boton-eliminar" @click="eliminarParqueadero(i)">Eliminar</button>
                    </div>
                    <!-- <hr style="width: 100%; border: 1px dashed #d7b377;" v-if="i < formData.ParkingNeed.length - 1"> -->
                </div>
                <button type="button" class="boton-secundario" @click="agregarParqueadero" v-if="formData.ParkingNeed.length <= 2">
                    Añadir Vehículo
                </button>
            </div>

            <!-- <button type="submit" class="boton-enviar" style="margin-bottom: 50px;"> -->
            <button class="boton-enviar" style="margin-bottom: 50px;" @click.prevent="validarFormulario">
                📤 Enviar Solicitud
            </button>
        </form>
    </div>
  </template>



  <script setup>

    import { ref, onMounted } from 'vue';
    import Swal from 'sweetalert2';
    import tenantApplicationApi from '@/api/tenantApplication';
    import { useRoute } from 'vue-router'
    import property from '@/api/property.js';
    import { useRouter } from 'vue-router'

    const router = useRouter()
    const route = useRoute()

    const tenantApplicationResource = new tenantApplicationApi();
    const propertyResource = new property();

    onMounted(async () => {

        loading.value = true;

        await propertyResource.checkIfPropertyApplicationOpen({sku: route.params.propertysku}).then(response => {
            if (response && response.success) {
            } else {
                router.push({ name: '404'})
            }
        }).catch(error => {
            router.push({ name: '404'})
        });

        loading.value = false;

    });

    const formData = ref({
        property_id: route.params.propertysku,
        move_in_date: '',
        FinancialResponsible: [crearResponsable()],
        Cohabitant: [],
        Pet: [],
        ParkingNeed: []
    });

    const today = new Date().toISOString().split('T')[0]

    // Funciones para manejar dinámicamente los elementos
    const agregarResponsable = () => {
        formData.value.FinancialResponsible.push(crearResponsable());
    };

    const eliminarResponsable = (index) => {
        formData.value.FinancialResponsible.splice(index, 1);
    };

    const agregarIngreso = (responsableIndex) => {
        formData.value.FinancialResponsible[responsableIndex].AdditionalIncome.push({
            monthly_amount: '',
            description: '',
            income_cert: null
        });
    };

    const eliminarIngreso = (responsableIndex, ingresoIndex) => {
        formData.value.FinancialResponsible[responsableIndex].AdditionalIncome.splice(ingresoIndex, 1);
    };

    const agregarConviviente = () => {
        formData.value.Cohabitant.push({
            first_name: '',
            last_name: '',
            document_number: '',
            occupation: '',
            age: ''
        });
    };

    const eliminarConviviente = (index) => {
        formData.value.Cohabitant.splice(index, 1);
    };

    const agregarMascota = () => {
        formData.value.Pet.push({
            type: '',
            sex: ''
        });
    };

    const eliminarMascota = (index) => {
        formData.value.Pet.splice(index, 1);
    };

    const agregarParqueadero = () => {
        formData.value.ParkingNeed.push({
            vehicle_type: ''
        });
    };

    const eliminarParqueadero = (index) => {
        formData.value.ParkingNeed.splice(index, 1);
    };

    const manejarArchivo = (event, objetivo, campo) => {
        objetivo[campo] = event.target.files[0];
    };

    const necesitaFiador = (responsable) => {
        if (!responsable.start_current_job_date) return false;
        const inicio = new Date(responsable.start_current_job_date);
        const diff = Date.now() - inicio.getTime();
        return (diff / (1000 * 60 * 60 * 24 * 365)) < 2;
    };

    const loading = ref(false);

    const validarFormulario = async () => {

        const form = document.querySelector('.formulario');
        // if (!form.checkValidity()) {
        //     await Swal.fire(
        //         'Error',
        //         'Por favor complete todos los campos requeridos',
        //         'error',
        //     );
        //     form.reportValidity();
        //     return;
        // }

        if (!form.reportValidity()) {
            Swal.fire(
                'Error',
                'Todos los campos deben ser completados con el formato correcto.',
                'error',
            );
            return;
        }

        loading.value = true;

        await tenantApplicationResource.createTenantApplication(formData.value).then((response) => {
            if(response && response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Solicitud enviada!',
                    text: 'Su aplicación ha sido recibida exitosamente'
                });
                formData.value = {
                    property_id: route.params.propertysku,
                    move_in_date: '',
                    FinancialResponsible: [crearResponsable()],
                    Cohabitant: [],
                    Pet: [],
                    ParkingNeed: []
                };
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al enviar la solicitud'
                });
            }
        }).catch((error) => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al enviar la solicitud'
            });
        });

        loading.value = false;

    };

    const construirFormData = (formDataObj, data, parentKey = '') => {
        if (data && typeof data === 'object' && !(data instanceof File)) {
            Object.entries(data).forEach(([key, value]) => {
                const formKey = parentKey ? `${parentKey}[${key}]` : key;
                if (Array.isArray(value)) {
                    value.forEach((item, index) => {
                        construirFormData(formDataObj, item, `${formKey}[${index}]`);
                    });
                } else if (typeof value === 'object' && value !== null) {
                    construirFormData(formDataObj, value, formKey);
                } else if (value instanceof File) {
                    formDataObj.append(formKey, value);
                } else {
                    formDataObj.append(formKey, value);
                }
            });
        }
    };

    function crearResponsable() {
        return {
            full_name: '',
            email: '',
            phone_number: '',
            document_type: '',
            document_number: '',
            birthdate: '',
            nationality: '',
            employment_status: '',
            monthly_salary: '',
            start_current_job_date: '',
            document_id: null,
            document_certf: null,
            document_pay_1: null,
            document_pay_2: null,
            document_pay_3: null,
            guarantor_full_name: '',
            guarantor_document_type: '',
            guarantor_document_number: '',
            guarantor_property_cert: null,
            AdditionalIncome: []
        };
    }
  </script>

  <style scoped>


    .container {
        margin: 0 auto;
        margin-top: 50px;
        /* padding: 2rem; */
        background-color: #f8f5f0;
        min-height: 100vh;
        /* border-radius: 25px; */
    }

    .titulo-principal {
        color: #8f754f;
        text-align: center;
        margin-bottom: 2rem;
        font-size: 2.5rem;
        font-weight: 800;
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
        gap: 1.5rem;
    }

    .grupo-campos {
        border: 0px solid #d7b377;
        border-radius: 25px;
        background-color: #f8f5f0;
        padding: 24px;
        margin-bottom: 1.5rem;
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
        /* .container {
            padding: 1rem;
            border-radius: 0px;
        } */

        .titulo-principal {
            font-size: 2rem;
        }

        .campos-grid {
            grid-template-columns: 1fr;
        }
    }
  </style>
