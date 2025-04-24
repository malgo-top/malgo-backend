<!-- components/NavBar.vue -->
<template>
    <nav class="navbar-container">
            <div class="navbar-content">
            <div class="navbar-logo"><i class="fa-solid fa-people-roof"style="margin-right: 10px; color: #8f754f;"></i>Malgo</div>
            <button class="hamburger" @click="toggleDrawer" aria-label="Toggle Menu">
                <i class="fas fa-bars"></i>
            </button>
            <ul class="nav-list desktop-only">
                <li v-for="route in filteredRoutes" :key="route.path" class="nav-item">
                    <router-link :to="route.path" class="nav-link" active-class="active-link">
                        <i :class="route.meta.icon"></i>
                        <span style="margin-right: 15px;">{{ route.meta.label }}</span>
                    </router-link>
                </li>
                <li class="nav-item">
                    <button @click="logout" class="nav-link" style="background: none; border: none; cursor: pointer;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span style="margin-right: 15px;">Salir</span>
                    </button>
                </li>
            </ul>
        </div>

    <!-- Slide-in drawer menu
    <transition name="slide">
      <div v-if="isDrawerOpen" class="mobile-drawer">
        <ul class="nav-list">
          <li v-for="route in filteredRoutes" :key="route.path" class="nav-item">
            <router-link @click="toggleDrawer" :to="route.path" class="nav-link" active-class="active-link">
              <i :class="route.meta.icon"  style="width: 22px !important;" ></i>
              <span >{{ route.meta.label }}</span>
            </router-link>
          </li>
        </ul>
      </div>
    </transition> -->

    <transition @before-enter="beforeEnter" @enter="enter" @leave="leave" >
        <div v-if="isDrawerOpen" ref="drawerContent" class="mobile-drawer">
            <ul class="nav-list">
                <li v-for="route in filteredRoutes" :key="route.path" class="nav-item">
                    <router-link @click="toggleDrawer" :to="route.path" class="nav-link" active-class="active-link">
                    <i :class="route.meta.icon" style="width: 22px !important;"></i>
                    <span>{{ route.meta.label }}</span>
                    </router-link>
                </li>
                <li class="nav-item">
                    <button @click="logout" class="nav-link" style="background: none; border: none; cursor: pointer;">
                        <i class="fas fa-sign-out-alt" style="width: 22px !important;"></i>
                        <span>Salir</span>
                    </button>
                </li>
            </ul>
        </div>
    </transition>

  </nav>
</template>

<script setup>
import { ref, computed, nextTick  } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'
import auth from '@/api/auth';

const authResource = new auth();

const drawerContent = ref(null)


const beforeEnter = (el) => {
  el.style.height = '0'
  el.style.opacity = '0'
  el.style.overflow = 'hidden'
}

const enter = (el, done) => {
  const height = el.scrollHeight

  el.style.transition = 'height 0.3s ease, opacity 0.3s ease'
  el.style.height = height + 'px'
  el.style.opacity = '1'

  const cleanup = () => {
    el.style.height = 'auto'
    el.style.overflow = ''
    el.removeEventListener('transitionend', cleanup)
    done()
  }

  el.addEventListener('transitionend', cleanup)
}

const leave = (el, done) => {
  const height = el.scrollHeight

  el.style.height = height + 'px'
  el.style.opacity = '1'
  el.style.overflow = 'hidden'

  // Force reflow
  void el.offsetHeight

  el.style.transition = 'height 0.3s ease, opacity 0.3s ease'
  el.style.height = '0'
  el.style.opacity = '0'

  const cleanup = () => {
    done()
    el.removeEventListener('transitionend', cleanup)
  }

  el.addEventListener('transitionend', cleanup)
}

const logout = async () => {

    localStorage.removeItem('token');
    localStorage.removeItem('user');
    store.commit('setToken', null);
    store.commit('setUser', {});
    router.push({ name: 'login' });
    authResource.logout();

}


const router = useRouter()
const store = useStore()
const isDrawerOpen = ref(false)

const toggleDrawer = () => {
  isDrawerOpen.value = !isDrawerOpen.value
}

const userRole = computed(() => store.getters.userRole)

// const filteredRoutes = computed(() => {
//   return router.getRoutes().filter(route => {
//     return (
//       route.meta &&
//       route.meta.roles &&
//       route.meta.roles.includes(userRole.value)
//     )
//   })
// })

const filteredRoutes = computed(() => {
  return router.getRoutes().filter(route => {
    const roles = route.meta?.roles
    return Array.isArray(roles) && roles.includes(userRole.value)
  })
})

</script>

<style scoped>
/* === Container & General === */
.navbar-container {
  background-color: #ffffff;
  position: fixed;
  top: 10px;
  left: 50%;
  transform: translateX(-50%);
  width: 90vw;
  max-width: 1300px;
  border-radius: 25px;
  padding: 0.8rem 1.2rem;
  box-shadow: 0px 4px 14px rgba(0, 0, 0, 0.12);
  z-index: 1000;
}

/* === Inner Navbar Layout === */
.navbar-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.navbar-logo {
  font-weight: bold;
  font-size: 1.4rem;
  color: black;
}

/* === Hamburger Button === */
.hamburger {
  background: none;
  border: none;
  font-size: 1.5rem;
  display: none;
  color: #8f754f;
  cursor: pointer;
}

/* === Nav Items === */
.nav-list {
  list-style: none;
  display: flex;
  gap: 1.2rem;
  margin: 0;
  padding: 0;
}

.nav-item {
  display: flex;
  align-items: center;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  text-decoration: none;
  color: #4b4b4b;
  font-weight: 600;
  transition: color 0.2s ease;
}

.active-link{
  color: #d7b377  !important;
}

.nav-link:hover {
  color: #705125 !important;
}

.nav-link i {
  font-size: 1.1rem;
}

/* === Responsive Behavior === */
.desktop-only {
  display: flex;
}

/* .mobile-drawer {
  background-color: #ffffff;
  border-top: 1px solid #d7b377;
  margin-top: 1rem;
  padding: 1rem;
  border-radius: 8px;
   box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
} */

@media (max-width: 1280px) {
  .desktop-only {
    display: none;
  }

  .hamburger {
    display: block;
  }

  .nav-list {
    flex-direction: column;
    gap: 0.75rem;
  }

  .nav-link {
    font-size: 1rem;
  }
}

.mobile-drawer {
  overflow: hidden;
  will-change: height, opacity;
  padding: 1rem;
}


/* === Transition === */
.slide-enter-active,
.slide-leave-active {
  transition: all 0.2s ease;
}
.slide-enter-from,
.slide-leave-to {
  transform: translateY(-20px);
  opacity: 0;
}



</style>
