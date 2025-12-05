<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import Tasks from '@/components/app/Tasks.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tasks',
        href: dashboard().url,
    },
];

const props = defineProps<{ tasks: any[], projects: any[], users: any[] }>();
</script>

<template>
    <Head title="Tasks" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <Tasks :tasks="props.tasks" :projects="props.projects" :users="props.users" />
            </div>
        </div>
    </AppLayout>
</template>
