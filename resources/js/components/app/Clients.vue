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
        <h1 class="text-3xl font-bold text-gray-900">Clients</h1>
        <button
          @click="openCreateModal"
          class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
        >
          Add Client
        </button>
      </div>

      <!-- Clients Table -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                ID
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Client Name
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Created At
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="client in clients" :key="client.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ client.id }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ client.client_name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(client.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                <button
                  @click="openEditModal(client)"
                  class="text-blue-600 hover:text-blue-900 font-semibold"
                >
                  Edit
                </button>
                <button
                  @click="confirmDelete(client)"
                  class="text-red-600 hover:text-red-900 font-semibold"
                >
                  Delete
                </button>
              </td>
            </tr>
            <tr v-if="clients.length === 0">
              <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                No clients found. <a href="#" @click="openCreateModal" class="text-blue-600 hover:text-blue-900">Create one</a>.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Create/Edit Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">
              {{ editingClient ? 'Edit Client' : 'Create Client' }}
            </h3>
            <button
              @click="closeModal"
              class="text-gray-400 hover:text-gray-600"
            >
              <span class="text-2xl">&times;</span>
            </button>
          </div>

          <form @submit.prevent="saveClient">
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2">
                Client Name
              </label>
              <input
                v-model="form.client_name"
                type="text"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                placeholder="Enter client name"
                required
              />
              <span v-if="errors.client_name" class="text-red-600 text-sm">
                {{ errors.client_name }}
              </span>
            </div>

            <div class="flex justify-end space-x-2">
              <button
                type="button"
                @click="closeModal"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="loading"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              >
                <svg v-if="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span>{{ editingClient ? 'Update' : 'Create' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <div v-if="showDeleteConfirm" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Confirm Delete
          </h3>
          <p class="text-gray-600 mb-6">
            Are you sure you want to delete <strong>{{ clientToDelete?.client_name }}</strong>? This action cannot be undone.
          </p>
          <div class="flex justify-end space-x-2">
            <button
              @click="showDeleteConfirm = false"
              class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
            >
              Cancel
            </button>
            <button
              @click="deleteClient"
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
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'

interface Client {
  id: number
  client_name: string
  created_at: string
  updated_at: string
}

defineProps<{
  clients: Client[]
}>();

const showModal = ref(false)
const showDeleteConfirm = ref(false)
const editingClient = ref<Client | null>(null)
const clientToDelete = ref<Client | null>(null)
// Loading states for API calls
const loading = ref(false)
const deleteLoading = ref(false)
const form = ref({
  client_name: '',
})
const errors = ref<Record<string, any>>({})
const toast = ref({
  show: false,
  message: '',
  type: 'success' as 'success' | 'error',
})

const openCreateModal = () => {
  editingClient.value = null
  form.value = { client_name: '' }
  errors.value = {}
  showModal.value = true
}

const openEditModal = (client: Client) => {
  editingClient.value = client
  form.value = { client_name: client.client_name }
  errors.value = {}
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingClient.value = null
  form.value = { client_name: '' }
  errors.value = {}
}

const showToast = (message: string, type: 'success' | 'error' = 'success') => {
  toast.value = { show: true, message, type }
  setTimeout(() => {
    toast.value.show = false
  }, 3000)
}

const saveClient = () => {
  errors.value = {}

  if (!form.value.client_name.trim()) {
    errors.value.client_name = 'Client name is required'
    return
  }

  loading.value = true

  if (editingClient.value) {
    router.put(`/clients/${editingClient.value.id}`, form.value, {
      onStart: () => (loading.value = true),
      onFinish: () => (loading.value = false),
      onSuccess: (page) => {
        const message = (page.props.flash as any)?.success || 'Client updated successfully'
        showToast(message, 'success')
        closeModal()
      },
      onError: (errs) => {
        errors.value = errs as Record<string, any>
      },
    })
  } else {
    router.post('/clients', form.value, {
      onStart: () => (loading.value = true),
      onFinish: () => (loading.value = false),
      onSuccess: (page) => {
        const message = (page.props.flash as any)?.success || 'Client created successfully'
        showToast(message, 'success')
        closeModal()
      },
      onError: (errs) => {
        errors.value = errs as Record<string, any>
      },
    })
  }
}

const confirmDelete = (client: Client) => {
  clientToDelete.value = client
  showDeleteConfirm.value = true
}

const deleteClient = () => {
  if (!clientToDelete.value) return

  deleteLoading.value = true

  router.delete(`/clients/${clientToDelete.value.id}`, {
    onStart: () => (deleteLoading.value = true),
    onFinish: () => (deleteLoading.value = false),
    onSuccess: (page) => {
      const message = (page.props.flash as any)?.success || 'Client deleted successfully'
      showToast(message, 'success')
      showDeleteConfirm.value = false
      clientToDelete.value = null
    },
    onError: () => {
      // keep modal open; user may retry
    },
  })
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}
</script>
