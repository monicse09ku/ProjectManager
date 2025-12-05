<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { Users, FolderOpen, CheckSquare } from 'lucide-vue-next';

defineProps<{
    clientCount: number
    projectCount: number
    taskCount: number
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <!-- Clients Count Card -->
                <Link href="/clients" class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-white p-6 hover:shadow-lg transition-shadow cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Total Clients</p>
                            <p class="text-4xl font-bold text-gray-900 mt-2">{{ clientCount }}</p>
                        </div>
                        <Users class="w-12 h-12 text-blue-500 opacity-20" />
                    </div>
                </Link>

                <!-- Projects Count Card -->
                <Link href="/projects" class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-white p-6 hover:shadow-lg transition-shadow cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Total Projects</p>
                            <p class="text-4xl font-bold text-gray-900 mt-2">{{ projectCount }}</p>
                        </div>
                        <FolderOpen class="w-12 h-12 text-green-500 opacity-20" />
                    </div>
                </Link>

                <!-- Tasks Count Card -->
                <Link href="/tasks" class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-white p-6 hover:shadow-lg transition-shadow cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Total Tasks</p>
                            <p class="text-4xl font-bold text-gray-900 mt-2">{{ taskCount }}</p>
                        </div>
                        <CheckSquare class="w-12 h-12 text-purple-500 opacity-20" />
                    </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
