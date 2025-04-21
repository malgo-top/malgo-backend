// directives/adminOnly.js
export function createAdminOnlyDirective(store) {
    return {
      mounted(el) {
        const userRole = store.state.user?.role;
  
        if (userRole !== 'admin') {
          el.style.display = 'none';
        }
      }
    }
  }
  