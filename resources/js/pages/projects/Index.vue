<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import Projects from '@/components/app/Projects.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Projects',
        href: dashboard().url,
    },
];

const props = defineProps<{ projects: any[], clients: any[] }>();
</script>

<template>
    <Head title="Projects" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <Projects :projects="props.projects" :clients="props.clients" />
            </div>
        </div>
    </AppLayout>
</template>
