<template>
    <div class="container d-flex justify-content-center align-items-center" style="height: auto;">
      <div class="seccion text-center" style="max-width: 600px;">
        <div class="error-icon mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#8f754f" viewBox="0 0 16 16">
            <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
            <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.55.55 0 0 1-1.1 0z"/>
          </svg>
        </div>
        
        <h1 class="titulo-seccion mb-3">404 - Página no encontrada</h1>
        
        <p class="mb-4">Lo sentimos, la página que estás buscando no existe o ha sido movida.</p>
        
        <!-- <div class="d-flex justify-content-center gap-3">
          <router-link to="/malgo-frontend/facturas" class="boton-primario">
            <i class="fas fa-home me-2"></i> Ir al Inicio
          </router-link>
          <button @click="goBack" class="boton-secundario">
            <i class="fas fa-arrow-left me-2"></i> Volver Atrás
          </button>
        </div> -->
      </div>
    </div>
  </template>
  
  <script setup>
  import { useRouter } from 'vue-router';
  
  const router = useRouter();
  
  const goBack = () => {
    router.go(-1);
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
      padding: 2.5rem;
      margin-bottom: 2rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
  
  .titulo-seccion {
      color: #8f754f;
      font-size: 2rem;
      margin-bottom: 1.5rem;
      font-weight: 800;
  }
  
  .error-icon {
      color: #8f754f;
      font-size: 5rem;
  }
  
  .boton-primario {
      background-color: #8f754f;
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 25px;
      cursor: pointer;
      font-size: 1rem;
      transition: background-color 0.3s;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
  }
  
  .boton-primario:hover {
      background-color: #6d5a3f;
      color: white;
  }
  
  .boton-secundario {
      background-color: #d7b377;
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 25px;
      cursor: pointer;
      font-size: 1rem;
      transition: background-color 0.3s;
      display: inline-flex;
      align-items: center;
  }
  
  .boton-secundario:hover {
      background-color: #b59460;
      color: white;
  }
  
  @media (max-width: 768px) {
      .container {
          border-radius: 0px;
          padding: 1rem;
      }
      
      .seccion {
          padding: 1.5rem;
      }
      
      .d-flex {
          flex-direction: column;
          gap: 1rem;
      }
      
      .boton-primario,
      .boton-secundario {
          width: 100%;
          justify-content: center;
      }
  }
  </style>