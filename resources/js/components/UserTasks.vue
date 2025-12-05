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
      <h1 class="text-3xl font-bold text-gray-900 mb-6">My Tasks</h1>

      <!-- Empty State -->
      <div v-if="tasks.length === 0" class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-gray-500 text-lg">No tasks assigned to you yet.</p>
      </div>

      <!-- Tasks Table -->
      <div v-else class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                No.
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                Task
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                Project
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                Deadline
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                Status
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(task, index) in tasks" :key="task.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                {{ index + 1 }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ task.title }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                {{ task.project?.title || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                {{ formatDate(task.deadline) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="{
                    'bg-yellow-100 text-yellow-800': task.status === 'pending',
                    'bg-blue-100 text-blue-800': task.status === 'in_progress',
                    'bg-green-100 text-green-800': task.status === 'completed',
                  }"
                  class="px-2 py-1 text-xs font-semibold rounded-full"
                >
                  {{ formatStatus(task.status) }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

interface Task {
  id: number
  title: string
  slug: string
  project_id: number
  assigned_user_id: number
  deadline: string
  status: string
  created_at: string
  updated_at: string
  project?: {
    id: number
    title: string
    slug: string
  }
  assignedUser?: {
    id: number
    name: string
    email: string
  }
}

const page = usePage<any>()
const tasks = ref<Task[]>([])
const toast = ref({
  show: false,
  message: '',
  type: 'success' as 'success' | 'error',
})

onMounted(() => {
  tasks.value = page.props.tasks || []

  // Display flash messages
  if (page.props.flash?.success) {
    showToast(page.props.flash.success, 'success')
  } else if (page.props.flash?.error) {
    showToast(page.props.flash.error, 'error')
  }
})

watch(
  () => page.props.tasks,
  (newTasks) => {
    if (newTasks) {
      tasks.value = newTasks
    }
  }
)

const showToast = (message: string, type: 'success' | 'error' = 'success') => {
  toast.value = { show: true, message, type }
  setTimeout(() => {
    toast.value.show = false
  }, 3000)
}

const formatDate = (date: string): string => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const formatStatus = (status: string): string => {
  const statusMap: Record<string, string> = {
    pending: 'Pending',
    in_progress: 'In Progress',
    completed: 'Completed',
  }
  return statusMap[status] || status
}
</script>
