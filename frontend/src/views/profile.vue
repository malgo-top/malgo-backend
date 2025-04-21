<template>
    <div class="container">
      <!-- Loading Overlay -->
      <div v-if="loading" class="position-fixed start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0, 0, 0, 0.5); z-index: 1050; top: 0px;">
        <div class="spinner-border text-light" role="status">
          <span class="visually-hidden"></span>
        </div>
      </div>

      <!-- Main Content -->
      <div class="row">
        <!-- User Information Section -->
        <div class="col-md-6 mb-4">
          <div class="seccion" style="margin-bottom: 0px !important;">
            <h2 class="titulo-seccion">👤 Información del Usuario</h2>

            <div class="contenedor-campos">
              <div class="grupo-campos">
                <div class="campo mb-3">
                  <label class="etiqueta">Nombre</label>
                  <input type="text" class="entrada" v-model="userInfo.name" disabled>
                </div>

                <div class="campo mb-3">
                  <label class="etiqueta">Correo Electrónico</label>
                  <input type="email" class="entrada" v-model="userInfo.email" disabled>
                </div>

                <div class="campo">
                  <label class="etiqueta">Numero de telefono</label>
                  <input type="text" class="entrada" v-model="userInfo.phone_number" disabled>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Change Password Section -->
        <div class="col-md-6 mb-4">
          <div class="seccion">
            <h2 class="titulo-seccion">🔒 Cambiar Contraseña</h2>

            <div class="contenedor-campos">
              <div class="grupo-campos">
                <div class="campo mb-3">
                  <label class="etiqueta">Contraseña Actual</label>
                  <input
                    type="password"
                    class="entrada"
                    v-model="passwordForm.currentPassword"
                    placeholder="Ingresa tu contraseña actual"
                  >
                </div>

                <div class="campo mb-3">
                  <label class="etiqueta">Nueva Contraseña</label>
                  <input
                    type="password"
                    class="entrada"
                    v-model="passwordForm.newPassword"
                    placeholder="Ingresa tu nueva contraseña"
                  >
                </div>

                <div class="campo mb-4">
                  <label class="etiqueta">Repetir Nueva Contraseña</label>
                  <input
                    type="password"
                    class="entrada"
                    v-model="passwordForm.repeatNewPassword"
                    placeholder="Repite tu nueva contraseña"
                    @keyup.enter="changePassword"
                  >
                </div>

                <button
                  class="boton-primario w-100"
                  @click="changePassword"
                  :disabled="changingPassword"
                >
                  <span v-if="!changingPassword">Cambiar Contraseña</span>
                  <span v-else>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Procesando...
                  </span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>

<script setup>
  import { ref, onMounted } from 'vue';
  import Swal from 'sweetalert2';
  import { useStore } from 'vuex'
  import auth from '@/api/auth';

    const authResource = new auth();
  // User information
  const userInfo = ref({
    name: "",
    email: "",
    phone_number: ""
 });
 const store = useStore()

  // Password form
  const passwordForm = ref({
    currentPassword: '',
    newPassword: '',
    repeatNewPassword: ''
  });

  onMounted(() => {
    userInfo.value = store.getters.getUser;
  })

  // Loading states
  const loading = ref(false);
  const changingPassword = ref(false);

  const changePassword = async () => {
    // Validate if passwords match
    if (passwordForm.value.newPassword !== passwordForm.value.repeatNewPassword) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Las contraseñas nuevas no coinciden',
        confirmButtonColor: '#8f754f'
      });
      return;
    }

    // Validate if current password is empty
    if (!passwordForm.value.currentPassword) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Por favor ingresa tu contraseña actual',
        confirmButtonColor: '#8f754f'
      });
      return;
    }

    changingPassword.value = true;

    await authResource.changePassword({ current_password: passwordForm.value.currentPassword, new_password_confirmation: passwordForm.value.repeatNewPassword  ,new_password: passwordForm.value.newPassword })
            .then((response) => {
                if (response && response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Contraseña cambiada correctamente',
                        confirmButtonColor: '#8f754f'
                    });
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: "Asegúrate de que la nueva contraseña tenga al menos 8 caracteres y que la contraseña actual sea correcta.",
                        confirmButtonColor: '#8f754f'
                    });
                }
            })
            .catch((error) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "Asegúrate de que la contraseña tenga al menos 8 caracteres y que la contraseña actual sea correcta.",
                    confirmButtonColor: '#8f754f'
                });
            })

        passwordForm.value = {
            currentPassword: '',
            newPassword: '',
            repeatNewPassword: ''
        };

      changingPassword.value = false;

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
      height: 100%;
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
  }

  .campo {
      margin-bottom: 1rem;
  }

  .etiqueta {
      display: block;
      margin-bottom: 0.5rem;
      color: #000000;
      font-weight: 500;
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

  .entrada[readonly] {
      background-color: #f8f5f0;
      cursor: not-allowed;
  }

  .boton-primario {
      background-color: #8f754f;
      color: white;
      border: none;
      padding: 1rem;
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

  @media (max-width: 768px) {
      .container {
          border-radius: 0px;
          padding: 1rem;
      }

      .seccion {
          padding: 1rem;
      }

      .row {
          flex-direction: column;
      }
  }
</style>
