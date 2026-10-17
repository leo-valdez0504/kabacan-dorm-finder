<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Item = {
    id: number;
    name: string;
    address: string;
    is_verified: boolean;
    min_price: string | null;
    cover: string | null;
};

defineProps<{
    properties: Item[];
    filters: { q: string; max_price: string | number; room_type: string };
}>();
</script>

<template>
    <Head title="Find a Dorm" />

    <div class="mx-auto max-w-5xl p-6">
        <h1 class="mb-4 text-2xl font-semibold">Find a Dorm</h1>

        <Form
            action="/student/dorms"
            method="get"
            class="mb-6 grid gap-4 sm:grid-cols-4"
        >
            <div class="grid gap-2">
                <Label for="q">Search</Label>
                <Input
                    id="q"
                    name="q"
                    :default-value="filters.q"
                    placeholder="Name or address"
                />
            </div>
            <div class="grid gap-2">
                <Label for="room_type">Room type</Label>
                <select
                    id="room_type"
                    name="room_type"
                    :value="filters.room_type"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm"
                >
                    <option value="">Any</option>
                    <option value="single">Single</option>
                    <option value="shared">Shared</option>
                    <option value="bedspace">Bedspace</option>
                </select>
            </div>
            <div class="grid gap-2">
                <Label for="max_price">Max price (₱/month)</Label>
                <Input
                    id="max_price"
                    name="max_price"
                    type="number"
                    min="0"
                    :default-value="filters.max_price"
                />
            </div>
            <div class="flex items-end">
                <Button type="submit">Search</Button>
            </div>
        </Form>

        <p v-if="properties.length === 0" class="text-muted-foreground">
            No dorms match your search yet.
        </p>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="p in properties"
                :key="p.id"
                :href="`/student/dorms/${p.id}`"
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
                    <div class="mt-2 text-sm">
                        From
                        <span class="font-semibold">₱{{ p.min_price }}</span
                        >/month
                        <span v-if="p.is_verified" class="ml-2 text-xs"
                            >· Verified</span
                        >
                    </div>
                </div>
            </Link>
        </div>
    </div>
</template>
