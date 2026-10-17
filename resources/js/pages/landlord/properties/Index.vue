<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

type PropertyItem = {
    id: number;
    name: string;
    address: string;
    is_verified: boolean;
    rooms_count: number;
    cover: string | null;
};

defineProps<{ properties: PropertyItem[] }>();
</script>

<template>
    <Head title="My Properties" />

    <div class="mx-auto max-w-5xl p-6">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold">My Properties</h1>
            <Button as-child>
                <Link href="/landlord/properties/create">Add property</Link>
            </Button>
        </div>

        <p v-if="properties.length === 0" class="text-muted-foreground">
            You have no listings yet. Click "Add property" to create one.
        </p>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="p in properties"
                :key="p.id"
                :href="`/landlord/properties/${p.id}`"
                class="overflow-hidden rounded-lg border transition hover:shadow-md"
            >
                <img
                    v-if="p.cover"
                    :src="p.cover"
                    alt=""
                    class="h-40 w-full object-cover"
                />
                <div
                    v-else
                    class="flex h-40 items-center justify-center bg-muted text-sm text-muted-foreground"
                >
                    No photo
                </div>
                <div class="p-4">
                    <div class="font-medium">{{ p.name }}</div>
                    <div class="text-sm text-muted-foreground">
                        {{ p.address }}
                    </div>
                    <div class="mt-2 text-xs">
                        {{ p.rooms_count }} room(s) ·
                        {{
                            p.is_verified ? 'Verified' : 'Pending verification'
                        }}
                    </div>
                </div>
            </Link>
        </div>
    </div>
</template>
