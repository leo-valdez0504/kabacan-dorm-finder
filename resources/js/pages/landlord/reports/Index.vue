<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Chart, registerables } from 'chart.js';
import { onMounted, ref } from 'vue';
import { Button } from '@/components/ui/button';

Chart.register(...registerables);

const props = defineProps<{
    statusLabels: string[];
    statusCounts: number[];
    roomLabels: string[];
    roomCounts: number[];
    totals: { reservations: number; rooms: number; open_slots: number };
}>();

const statusCanvas = ref<HTMLCanvasElement | null>(null);
const roomCanvas = ref<HTMLCanvasElement | null>(null);

onMounted(() => {
    if (statusCanvas.value) {
        new Chart(statusCanvas.value, {
            type: 'doughnut',
            data: {
                labels: props.statusLabels,
                datasets: [
                    {
                        data: props.statusCounts,
                        backgroundColor: ['#facc15', '#22c55e', '#ef4444', '#9ca3af'],
                    },
                ],
            },
            options: { plugins: { legend: { position: 'bottom' } } },
        });
    }

    if (roomCanvas.value) {
        new Chart(roomCanvas.value, {
            type: 'bar',
            data: {
                labels: props.roomLabels,
                datasets: [{ label: 'Rooms', data: props.roomCounts, backgroundColor: '#3b82f6' }],
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });
    }
});
</script>

<template>
    <Head title="Reports" />

    <div class="mx-auto max-w-5xl p-6">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-semibold">Reports</h1>
            <div class="flex gap-2">
                <Button as-child variant="outline">
                    <a href="/landlord/reports/pdf">Download PDF</a>
                </Button>
                <Button as-child>
                    <a href="/landlord/reports/excel">Download Excel</a>
                </Button>
            </div>
        </div>

        <div class="mb-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-lg border p-4">
                <div class="text-sm text-muted-foreground">Reservations</div>
                <div class="text-2xl font-semibold">{{ totals.reservations }}</div>
            </div>
            <div class="rounded-lg border p-4">
                <div class="text-sm text-muted-foreground">Rooms</div>
                <div class="text-2xl font-semibold">{{ totals.rooms }}</div>
            </div>
            <div class="rounded-lg border p-4">
                <div class="text-sm text-muted-foreground">Open slots</div>
                <div class="text-2xl font-semibold">{{ totals.open_slots }}</div>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="rounded-lg border p-4">
                <h2 class="mb-3 font-medium">Reservations by status</h2>
                <canvas ref="statusCanvas"></canvas>
            </div>
            <div class="rounded-lg border p-4">
                <h2 class="mb-3 font-medium">Rooms by type</h2>
                <canvas ref="roomCanvas"></canvas>
            </div>
        </div>
    </div>
</template>