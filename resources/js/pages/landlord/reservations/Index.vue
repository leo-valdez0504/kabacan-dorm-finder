<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

type Item = {
    id: number;
    student: string;
    student_email: string;
    student_phone: string | null;
    property: string;
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
    <Head title="Reservations" />

    <div class="mx-auto max-w-4xl p-6">
        <h1 class="mb-6 text-2xl font-semibold">Reservation Requests</h1>

        <p v-if="reservations.length === 0" class="text-muted-foreground">
            No requests yet.
        </p>

        <div class="grid gap-3">
            <div
                v-for="r in reservations"
                :key="r.id"
                class="flex items-start justify-between rounded-lg border p-4"
            >
                <div>
                    <div class="font-medium">
                        {{ r.student }}
                        <span class="text-sm text-muted-foreground"
                            >· {{ r.student_email }}</span
                        >
                    </div>
                    <div
                        v-if="r.student_phone"
                        class="text-sm text-muted-foreground"
                    >
                        {{ r.student_phone }}
                    </div>
                    <div class="mt-1 text-sm">
                        {{ r.property }} ·
                        <span class="capitalize">{{ r.room_type }}</span> · ₱{{
                            r.price
                        }}/month
                    </div>
                    <div class="text-sm">Move-in: {{ r.move_in_date }}</div>
                    <div
                        v-if="r.message"
                        class="mt-1 text-sm text-muted-foreground italic"
                    >
                        "{{ r.message }}"
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
                <div v-if="r.status === 'pending'" class="flex gap-2">
                    <Form
                        :action="`/landlord/reservations/${r.id}/approve`"
                        method="post"
                    >
                        <Button type="submit" size="sm">Approve</Button>
                    </Form>
                    <Form
                        :action="`/landlord/reservations/${r.id}/decline`"
                        method="post"
                    >
                        <Button type="submit" variant="outline" size="sm"
                            >Decline</Button
                        >
                    </Form>
                </div>
            </div>
        </div>
    </div>
</template>
