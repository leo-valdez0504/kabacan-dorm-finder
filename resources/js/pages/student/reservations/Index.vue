<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

type Item = {
    id: number;
    property: string;
    address: string;
    room_type: string;
    price: string;
    move_in_date: string;
    status: string;
    message: string | null;
};

defineProps<{ reservations: Item[] }>();

const badge: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-green-100 text-green-800',
    declined: 'bg-red-100 text-red-800',
    cancelled: 'bg-gray-100 text-gray-700',
};
</script>

<template>
    <Head title="My Reservations" />

    <div class="mx-auto max-w-4xl p-6">
        <h1 class="mb-6 text-2xl font-semibold">My Reservations</h1>

        <p v-if="reservations.length === 0" class="text-muted-foreground">
            You have no reservation requests yet.
        </p>

        <div class="grid gap-3">
            <div
                v-for="r in reservations"
                :key="r.id"
                class="flex items-center justify-between rounded-lg border p-4"
            >
                <div>
                    <div class="font-medium">{{ r.property }}</div>
                    <div class="text-sm text-muted-foreground">
                        {{ r.address }}
                    </div>
                    <div class="mt-1 text-sm capitalize">
                        {{ r.room_type }} · ₱{{ r.price }}/month · Move-in
                        {{ r.move_in_date }}
                    </div>
                    <span
                        :class="[
                            'mt-2 inline-block rounded px-2 py-0.5 text-xs capitalize',
                            badge[r.status],
                        ]"
                    >
                        {{ r.status }}
                    </span>
                </div>
                <Form
                    v-if="r.status === 'pending' || r.status === 'approved'"
                    :action="`/student/reservations/${r.id}/cancel`"
                    method="post"
                >
                    <Button type="submit" variant="outline" size="sm"
                        >Cancel</Button
                    >
                </Form>
            </div>
        </div>
    </div>
</template>
