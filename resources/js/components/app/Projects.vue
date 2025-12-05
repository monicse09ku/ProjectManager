<template>
  <div class="min-h-screen bg-gray-100 py-8">
    <!-- Toast Notification -->
    <div v-if="toast.show" class="fixed top-4 right-4 z-50">
      <div
        :class="{
          'bg-green-50 border border-green-200': toast.type === 'success',
          'bg-red-50 border border-red-200': toast.type === 'error',
        }"
        class="rounded-lg p-4 shadow-lg"
      >
        <p
          :class="{
            'text-green-700': toast.type === 'success',
            'text-red-700': toast.type === 'error',
          }"
          class="text-sm font-medium"
        >
          {{ toast.message }}
        </p>
      </div>
    </div>

    <div class="max-w-6xl mx-auto px-4">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Projects</h1>
        <button
          @click="openCreateModal"
          class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
        >
          Add Project
        </button>
      </div>

      <!-- Projects Table -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="project in projects" :key="project.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ project.id }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ project.title }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ project.client?.client_name || '—' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ project.start_date }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ project.end_date }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ project.status }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                <button @click="openEditModal(project)" class="text-blue-600 hover:text-blue-900 font-semibold">Edit</button>
                <button @click="confirmDelete(project)" class="text-red-600 hover:text-red-900 font-semibold">Delete</button>
              </td>
            </tr>
            <tr v-if="projects.length === 0">
              <td colspan="7" class="px-6 py-4 text-center text-gray-500">No projects found. <a href="#" @click="openCreateModal" class="text-blue-600 hover:text-blue-900">Create one</a>.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Modal (create/edit) -->
      <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">{{ editingProject ? 'Edit Project' : 'Create Project' }}</h3>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600"><span class="text-2xl">&times;</span></button>
          </div>

          <form @submit.prevent="saveProject">
            <div class="mb-3">
              <label class="block text-gray-700 text-sm font-bold mb-2">Title</label>
              <input v-model="form.title" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required />
              <span v-if="errors.title" class="text-red-600 text-sm">{{ errors.title }}</span>
            </div>

            <div class="mb-3">
              <label class="block text-gray-700 text-sm font-bold mb-2">Client</label>
              <select v-model="form.client_id" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.client_name }}</option>
              </select>
              <span v-if="errors.client_id" class="text-red-600 text-sm">{{ errors.client_id }}</span>
            </div>

            <div class="mb-3">
              <label class="block text-gray-700 text-sm font-bold mb-2">Start Date</label>
              <input v-model="form.start_date" type="date" class="shadow border rounded w-full py-2 px-3 text-gray-700" required />
              <span v-if="errors.start_date" class="text-red-600 text-sm">{{ errors.start_date }}</span>
            </div>

            <div class="mb-3">
              <label class="block text-gray-700 text-sm font-bold mb-2">End Date</label>
              <input v-model="form.end_date" type="date" class="shadow border rounded w-full py-2 px-3 text-gray-700" required />
              <span v-if="errors.end_date" class="text-red-600 text-sm">{{ errors.end_date }}</span>
            </div>

            <div class="mb-3">
              <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
              <select v-model="form.status" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                <option value="active">active</option>
                <option value="inactive">inactive</option>
                <option value="archived">archived</option>
              </select>
              <span v-if="errors.status" class="text-red-600 text-sm">{{ errors.status }}</span>
            </div>

            <div class="flex justify-end space-x-2">
              <button type="button" @click="closeModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Cancel</button>
              <button
                type="submit"
                :disabled="loading"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              >
                <svg v-if="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span>{{ editingProject ? 'Update' : 'Create' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Delete Confirmation -->
      <div v-if="showDeleteConfirm" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Confirm Delete</h3>
          <p class="text-gray-600 mb-6">Are you sure you want to delete <strong>{{ projectToDelete?.title }}</strong>?</p>
          <div class="flex justify-end space-x-2">
            <button @click="showDeleteConfirm = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Cancel</button>
              <button
                @click="deleteProject"
                :disabled="deleteLoading"
                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              >
                <svg v-if="deleteLoading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span>Delete</span>
              </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'

interface Project {
  id: number
  client_id: number
  title: string
  slug: string
  start_date: string
  end_date: string
  status: string
  client?: { id: number; client_name: string }
}

const props = defineProps<{
  projects: Project[]
  clients: { id: number; client_name: string }[]
}>()

const projects = ref<Project[]>(props.projects || [])
const clients = ref(props.clients || [])

// Watch for prop changes and update local refs
watch(() => props.projects, (newProjects) => {
  projects.value = newProjects || []
}, { deep: true })

watch(() => props.clients, (newClients) => {
  clients.value = newClients || []
}, { deep: true })

const showModal = ref(false)
const showDeleteConfirm = ref(false)
const editingProject = ref<Project | null>(null)
const projectToDelete = ref<Project | null>(null)
const loading = ref(false)
const deleteLoading = ref(false)

const form = ref({
  client_id: clients.value.length ? clients.value[0].id : null,
  title: '',
  slug: '',
  start_date: '',
  end_date: '',
  status: 'active',
})
const errors = ref<Record<string, any>>({})
const toast = ref({
  show: false,
  message: '',
  type: 'success' as 'success' | 'error',
})

const openCreateModal = () => {
  editingProject.value = null
  form.value = {
    client_id: clients.value.length ? clients.value[0].id : null,
    title: '',
    slug: '',
    start_date: '',
    end_date: '',
    status: 'active',
  }
  errors.value = {}
  showModal.value = true
}

const openEditModal = (project: Project) => {
  editingProject.value = project
  form.value = {
    client_id: project.client_id,
    title: project.title,
    slug: project.slug,
    start_date: project.start_date,
    end_date: project.end_date,
    status: project.status,
  }
  errors.value = {}
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingProject.value = null
  errors.value = {}
}

const showToast = (message: string, type: 'success' | 'error' = 'success') => {
  toast.value = { show: true, message, type }
  setTimeout(() => {
    toast.value.show = false
  }, 3000)
}

const saveProject = () => {
  errors.value = {}

  const payload = { ...form.value }

  if (editingProject.value) {
    router.put(`/projects/${editingProject.value.id}`, payload, {
      onStart: () => (loading.value = true),
      onFinish: () => (loading.value = false),
      onSuccess: (page) => {
        const message = (page.props.flash as any)?.success || 'Project updated successfully'
        showToast(message, 'success')
        router.reload()
        closeModal()
      },
      onError: (errs) => {
        errors.value = errs as Record<string, any>
      }
    })
  } else {
    router.post('/projects', payload, {
      onStart: () => (loading.value = true),
      onFinish: () => (loading.value = false),
      onSuccess: (page) => {
        const message = (page.props.flash as any)?.success || 'Project created successfully'
        showToast(message, 'success')
        router.reload()
        closeModal()
      },
      onError: (errs) => {
        errors.value = errs as Record<string, any>
      }
    })
  }
}

const confirmDelete = (project: Project) => {
  projectToDelete.value = project
  showDeleteConfirm.value = true
}

const deleteProject = () => {
  if (!projectToDelete.value) return

  router.delete(`/projects/${projectToDelete.value.id}`, {
    onStart: () => (deleteLoading.value = true),
    onFinish: () => (deleteLoading.value = false),
    onSuccess: (page) => {
      const message = (page.props.flash as any)?.success || 'Project deleted successfully'
      showToast(message, 'success')
      router.reload()
      showDeleteConfirm.value = false
      projectToDelete.value = null
    }
  })
}
</script>
