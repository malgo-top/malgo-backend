<template>
    <div class="container d-flex justify-content-center align-items-center" style="height: auto;">
      <!-- Loading Overlay -->
      <div v-if="loading" class="position-fixed start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0, 0, 0, 0.5); z-index: 1050; top: 0px;">
        <div class="spinner-border text-light" role="status">
          <span class="visually-hidden"></span>
        </div>
      </div>

      <!-- Login Card -->
      <div class="seccion" style="width: 100%; max-width: 500px;">
        <div class="col-12 text-center mb-4">
          <h2 class="titulo-seccion">🔐 Iniciar Sesión</h2>
        </div>

        <div class="contenedor-campos">
          <div class="grupo-campos">
            <div class="campo mb-4">
              <label class="etiqueta">Correo Electrónico</label>
              <input
                type="email"
                class="entrada"
                v-model="email"
                placeholder="tu@email.com"
                @keyup.enter="login"
              >
            </div>

            <div class="campo mb-4 position-relative">
                <label class="etiqueta">Contraseña</label>
                <input
                    :type="showPassword ? 'text' : 'password'"
                    class="entrada pe-5"
                    v-model="password"
                    placeholder="••••••••"
                    @keyup.enter="login"
                >
                <span
                    class="toggle-eye"
                    @click="showPassword = !showPassword"
                    style="position: absolute; top: 38px; right: 16px; cursor: pointer; font-size: 1.2rem; color: #aaa;"
                >
                    <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </span>
            </div>

            <button
              class="boton-primario w-100"
              @click="login"
              :disabled="loading"
            >
              <span v-if="!loading">Iniciar Sesión</span>
              <span v-else>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Cargando...
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
</template>

<script setup>

    import { ref } from 'vue';
    import Swal from 'sweetalert2'
    import auth from '@/api/auth';
    import { useStore } from 'vuex'
    import { useRouter } from 'vue-router';

    const router = useRouter();
    const authResource = new auth();
    const store = useStore()

    const email = ref('');
    const password = ref('');
    const loading = ref(false);
    const showPassword = ref(false);


    const login = async () => {

        if (!email.value || !password.value) {
            Swal.fire(
                'Error',
                'Por favor ingresa tu correo y contraseña.',
                'error'
            );
            return;
        }

        loading.value = true;

        await authResource.login({ email: email.value, password: password.value })
            .then((response) => {
                if (response && response.success) {

                    localStorage.setItem('token', response.token);
                    localStorage.setItem('user', JSON.stringify(response.user));

                    store.commit('setToken', response.token);
                    store.commit('setUser', response.user);


                    router.push({ name: 'facturas' });
                }else{
                    Swal.fire(
                        'Error',
                        'El correo o la contraseña son incorrectos.',
                        'error'
                    );
                }
            })
            .catch((error) => {
                Swal.fire(
                        'Error',
                        'El correo o la contraseña son incorrectos.',
                        'error'
                    );
            })
            .finally(() => {
                loading.value = false;
            });


    };

</script>

  <style scoped>
  .container {
      width: 100%;
      margin: 0 auto;
      padding: 2rem;
      background-color: #f8f5f0;
      height: 80vh !important;
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
      justify-content: center;
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
  }
</style>
