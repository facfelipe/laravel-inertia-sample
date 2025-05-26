import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { createPinia } from 'pinia'
import MainLayout from '@/Layouts/MainLayout.vue'

// Import CSS
import '../css/app.css'

// Import Flowbite for initialization
import 'flowbite'

// Import Echo for WebSocket support
import './echo'

// Initialize Flowbite
document.addEventListener('DOMContentLoaded', () => {
  // Flowbite is automatically initialized through the imported script
  // No need for manual initialization
})

createInertiaApp({
  resolve: async name => {
    try {
      let page
      
      // Handle different page patterns for better Vite compatibility
      if (name.includes('/')) {
        // For nested pages like MedicalRecords/Index, Patients/Show, etc.
        const parts = name.split('/')
        const directory = parts[0]
        const file = parts[1]
        
        // Use specific import patterns that Vite can analyze
        switch (directory) {
          case 'MedicalRecords':
            page = await import(`./Pages/MedicalRecords/${file}.vue`)
            break
          case 'Patients':
            page = await import(`./Pages/Patients/${file}.vue`)
            break
          case 'Anamneses':
            page = await import(`./Pages/Anamneses/${file}.vue`)
            break
          default:
            // Fallback for other directories
            page = await import(`./Pages/${name}.vue`)
        }
      } else {
        // For top-level pages like Dashboard, Home, etc.
        page = await import(`./Pages/${name}.vue`)
      }
      
      // Don't apply automatic layout - pages should explicitly use MainLayout in their templates
      // if (page.default.layout === undefined) {
      //   page.default.layout = MainLayout
      // }
      
      return page
    } catch (error) {
      console.error(`Could not load page: ${name}`, error)
      
      // Return a simple error page as fallback
      return {
        default: {
          // Don't apply layout here either to avoid duplication
          // layout: MainLayout,
          render: () => h('div', { class: 'p-8 text-center' }, [
            h('h1', { class: 'text-xl font-bold mb-4 text-red-600' }, `Page "${name}" could not be loaded`),
            h('p', { class: 'mb-4 text-gray-600' }, 'There was an error loading this page.'),
            h('div', { class: 'bg-red-50 border border-red-200 rounded p-4 mb-4' }, [
              h('h3', { class: 'font-medium text-red-800 mb-2' }, 'Error Details:'),
              h('pre', { class: 'text-left text-red-700 text-sm whitespace-pre-wrap' }, error.message)
            ]),
            h('button', { 
              class: 'px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors',
              onClick: () => window.location.reload()
            }, 'Reload Page')
          ])
        }
      }
    }
  },
  setup({ el, App, props, plugin }) {
    const pinia = createPinia()
    
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(pinia)
      .mount(el)
  },
  title: title => title
})