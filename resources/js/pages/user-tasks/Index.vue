<template>
  <AppLayout>
    <UserTasks :tasks="tasks" />
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import UserTasks from '@/components/UserTasks.vue'

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

defineProps<{
  tasks: Task[]
}>()
</script>
