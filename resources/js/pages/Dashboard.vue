<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

type Stat = { label: string; value: number };
type LinkItem = { label: string; href: string };
type Recent = { id: number; title: string; subtitle: string; badge: string; href: string };

defineProps<{
    role: string;
    name: string;
    stats: Stat[];
    links: LinkItem[];
    recentTitle: string;
    recent: Recent[];
}>();

const badge: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-green-100 text-green-800',
    declined: 'bg-red-100 text-red-800',
    cancelled: 'bg-gray-100 text-gray-700',
};
</script>

<template>
    <Head title="Dashboard" />

    <div class="mx-auto max-w-5xl p-6">
        <h1 class="text-2xl font-semibold">Welcome, {{ name }}</h1>
        <p class="mb-6 text-sm capitalize text-muted-foreground">{{ role }} dashboard</p>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div v-for="s in stats" :key="s.label" class="rounded-lg border p-4">
                <div class="text-sm text-muted-foreground">{{ s.label }}</div>
                <div class="text-3xl font-semibold">{{ s.value }}</div>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-2">
            <Button v-for="l in links" :key="l.href" as-child variant="outline">
                <Link :href="l.href">{{ l.label }}</Link>
            </Button>
        </div>

        <h2 class="mb-3 mt-8 text-lg font-semibold">{{ recentTitle }}</h2>
        <p v-if="recent.length === 0" class="text-sm text-muted-foreground">Nothing here yet.</p>

        <div class="grid gap-3">
            <Link
                v-for="r in recent"
                :key="r.id"
                :href="r.href"
                class="flex items-center justify-between rounded-lg border p-4 transition hover:shadow-md"
            >
                <div>
                    <div class="font-medium">{{ r.title }}</div>
                    <div class="text-sm text-muted-foreground">{{ r.subtitle }}</div>
                </div>
                <span :class="['rounded px-2 py-0.5 text-xs capitalize', badge[r.badge]]">{{ r.badge }}</span>
            </Link>
        </div>
    </div>
</template>