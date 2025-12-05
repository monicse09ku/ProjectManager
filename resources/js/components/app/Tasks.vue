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
        <h1 class="text-3xl font-bold text-gray-900">Tasks</h1>
        <button
          @click="openCreateModal"
          class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
        >
          Add Task
        </button>
      </div>

      <!-- Tasks Table -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="task in tasks" :key="task.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ task.id }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ task.title || task.Title || '—' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ task.project?.title || '—' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ findUserName(task.assigned_user_id) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ task.deadline }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ task.status }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                <button @click="openEditModal(task)" class="text-blue-600 hover:text-blue-900 font-semibold">Edit</button>
                <button @click="confirmDelete(task)" class="text-red-600 hover:text-red-900 font-semibold">Delete</button>
              </td>
            </tr>
            <tr v-if="tasks.length === 0">
              <td colspan="7" class="px-6 py-4 text-center text-gray-500">No tasks found. <a href="#" @click="openCreateModal" class="text-blue-600 hover:text-blue-900">Create one</a>.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Modal (create/edit) -->
      <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">{{ editingTask ? 'Edit Task' : 'Create Task' }}</h3>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600"><span class="text-2xl">&times;</span></button>
          </div>

          <form @submit.prevent="saveTask">
            <div class="mb-3">
              <label class="block text-gray-700 text-sm font-bold mb-2">Title</label>
              <input v-model="form.title" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required />
              <span v-if="errors.title" class="text-red-600 text-sm">{{ errors.title }}</span>
            </div>

            <div class="mb-3">
              <label class="block text-gray-700 text-sm font-bold mb-2">Project</label>
              <select v-model="form.project_id" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.title }}</option>
              </select>
              <span v-if="errors.project_id" class="text-red-600 text-sm">{{ errors.project_id }}</span>
            </div>

            <div class="mb-3">
              <label class="block text-gray-700 text-sm font-bold mb-2">Assigned User</label>
              <select v-model="form.assigned_user_id" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
              </select>
              <span v-if="errors.assigned_user_id" class="text-red-600 text-sm">{{ errors.assigned_user_id }}</span>
            </div>

            <div class="mb-3">
              <label class="block text-gray-700 text-sm font-bold mb-2">Deadline</label>
              <input v-model="form.deadline" type="date" :min="today" class="shadow border rounded w-full py-2 px-3 text-gray-700" required />
              <span v-if="errors.deadline" class="text-red-600 text-sm">{{ errors.deadline }}</span>
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
                <span>{{ editingTask ? 'Update' : 'Create' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Delete Confirmation -->
      <div v-if="showDeleteConfirm" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Confirm Delete</h3>
          <p class="text-gray-600 mb-6">Are you sure you want to delete <strong>{{ editingTask?.title || editingTask?.Title }}</strong>?</p>
          <div class="flex justify-end space-x-2">
            <button @click="showDeleteConfirm = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Cancel</button>
              <button
                @click="deleteTask"
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
import { ref, watch, computed } from 'vue'
import { router } from '@inertiajs/vue3'

interface Task {
  id: number
  project_id: number
  assigned_user_id: number
  title?: string
  Title?: string
  slug?: string
  deadline?: string
  status?: string
  project?: { id: number; title: string }
  assignedUser?: { id: number; name: string }
}

const props = defineProps<{
  tasks: Task[]
  projects: { id: number; title: string }[]
  users: { id: number; name: string }[]
}>()

const tasks = ref<Task[]>(props.tasks || [])
const projects = ref(props.projects || [])
const users = ref(props.users || [])

watch(() => props.tasks, (newTasks) => {
  tasks.value = newTasks || []
}, { deep: true })

watch(() => props.projects, (newProjects) => {
  projects.value = newProjects || []
}, { deep: true })

watch(() => props.users, (newUsers) => {
  users.value = newUsers || []
}, { deep: true })

const showModal = ref(false)
const showDeleteConfirm = ref(false)
const editingTask = ref<Task | null>(null)
const taskToDelete = ref<Task | null>(null)
const loading = ref(false)
const deleteLoading = ref(false)

const form = ref({
  project_id: projects.value.length ? projects.value[0].id : null,
  assigned_user_id: users.value.length ? users.value[0].id : null,
  title: '',
  slug: '',
  deadline: '',
  status: 'active',
})
const errors = ref<Record<string, any>>({})
const toast = ref({
  show: false,
  message: '',
  type: 'success' as 'success' | 'error',
})

const today = computed(() => {
  const now = new Date()
  return now.toISOString().split('T')[0]
})

const findUserName = (id: number | undefined) => {
  console.log('Finding user name for ID:', id)
  if (!id) return '—'
  const u = users.value.find((x) => Number(x.id) === Number(id))
  return u ? u.name : '—'
}

const openCreateModal = () => {
  editingTask.value = null
  form.value = {
    project_id: projects.value.length ? projects.value[0].id : null,
    assigned_user_id: users.value.length ? users.value[0].id : null,
    title: '',
    slug: '',
    deadline: '',
    status: 'active',
  }
  errors.value = {}
  showModal.value = true
}

const openEditModal = (task: Task) => {
  editingTask.value = task
  form.value = {
    project_id: task.project_id,
    assigned_user_id: task.assigned_user_id ?? (task as any).assigned_user ?? (task as any).assignedUser?.id,
    title: task.title || task.Title || '',
    slug: task.slug || '',
    deadline: task.deadline || '',
    status: task.status || 'active',
  }
  errors.value = {}
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingTask.value = null
  errors.value = {}
}

const showToast = (message: string, type: 'success' | 'error' = 'success') => {
  toast.value = { show: true, message, type }
  setTimeout(() => {
    toast.value.show = false
  }, 3000)
}

const saveTask = () => {
  errors.value = {}
  const payload = { ...form.value }

  if (editingTask.value) {
    router.put(`/tasks/${editingTask.value.id}`, payload, {
      onStart: () => (loading.value = true),
      onFinish: () => (loading.value = false),
      onSuccess: (page) => {
        const message = (page.props.flash as any)?.success || 'Task updated successfully'
        showToast(message, 'success')
        router.reload()
        closeModal()
      },
      onError: (errs) => {
        errors.value = errs as Record<string, any>
      }
    })
  } else {
    router.post('/tasks', payload, {
      onStart: () => (loading.value = true),
      onFinish: () => (loading.value = false),
      onSuccess: (page) => {
        const message = (page.props.flash as any)?.success || 'Task created successfully'
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

const confirmDelete = (task: Task) => {
  taskToDelete.value = task
  showDeleteConfirm.value = true
}

const deleteTask = () => {
  if (!taskToDelete.value) return

  router.delete(`/tasks/${taskToDelete.value.id}`, {
    onStart: () => (deleteLoading.value = true),
    onFinish: () => (deleteLoading.value = false),
    onSuccess: (page) => {
      const message = (page.props.flash as any)?.success || 'Task deleted successfully'
      showToast(message, 'success')
      router.reload()
      showDeleteConfirm.value = false
      taskToDelete.value = null
    }
  })
}
</script>
