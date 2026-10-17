<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Room = {
    id: number;
    room_type: string;
    capacity: number;
    available_slots: number;
    price: string;
    status: string;
};

type PropertyData = {
    id: number;
    name: string;
    address: string;
    description: string | null;
    rules: string | null;
    is_verified: boolean;
    latitude: string | null;
    longitude: string | null;
    landlord: { name: string; phone: string | null };
    images: { id: number; url: string }[];
    rooms: Room[];
};

const props = defineProps<{ property: PropertyData }>();

const mapQuery = computed(() => {
    const p = props.property;
    if (p.latitude && p.longitude) {
        return `${p.latitude},${p.longitude}`;
    }
    return encodeURIComponent(
        `${p.name}, ${p.address}, Kabacan, Cotabato, Philippines`,
    );
});

const mapEmbed = computed(
    () => `https://maps.google.com/maps?q=${mapQuery.value}&z=16&output=embed`,
);
const mapLink = computed(
    () => `https://www.google.com/maps?q=${mapQuery.value}`,
);
</script>

<template>
    <Head :title="property.name" />

    <div class="mx-auto max-w-4xl p-6">
        <Link href="/student/dorms" class="text-sm underline"
            >← Back to search</Link
        >

        <h1 class="mt-4 text-2xl font-semibold">{{ property.name }}</h1>
        <p class="text-muted-foreground">{{ property.address }}</p>
        <p v-if="property.is_verified" class="mt-1 text-xs">Verified listing</p>

        <div
            v-if="property.images.length"
            class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3"
        >
            <img
                v-for="img in property.images"
                :key="img.id"
                :src="img.url"
                alt=""
                class="h-40 w-full rounded-lg object-cover"
            />
        </div>

        <div v-if="property.description" class="mt-6">
            <h2 class="font-medium">About</h2>
            <p class="text-sm whitespace-pre-line text-muted-foreground">
                {{ property.description }}
            </p>
        </div>

        <div v-if="property.rules" class="mt-4">
            <h2 class="font-medium">House rules</h2>
            <p class="text-sm whitespace-pre-line text-muted-foreground">
                {{ property.rules }}
            </p>
        </div>

        <div class="mt-4">
            <h2 class="font-medium">Landlord</h2>
            <p class="text-sm text-muted-foreground">
                {{ property.landlord.name }}
                <span v-if="property.landlord.phone">
                    · {{ property.landlord.phone }}</span
                >
            </p>
        </div>

        <div class="mt-6">
            <div class="flex items-center justify-between">
                <h2 class="font-medium">Location</h2>
                <a
                    :href="mapLink"
                    target="_blank"
                    rel="noopener"
                    class="text-sm underline"
                    >Open in Google Maps</a
                >
            </div>
            <iframe
                :src="mapEmbed"
                class="mt-2 h-72 w-full rounded-lg border"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                title="Property location"
            ></iframe>
        </div>

        <h2 class="mt-8 text-lg font-semibold">Rooms</h2>
        <div class="mt-3 grid gap-3">
            <div
                v-for="room in property.rooms"
                :key="room.id"
                class="rounded-lg border p-4"
            >
                <div class="font-medium capitalize">
                    {{ room.room_type }} · ₱{{ room.price }}/month
                </div>
                <div class="text-sm text-muted-foreground">
                    {{ room.available_slots }} of {{ room.capacity }} slot(s)
                    available · {{ room.status }}
                </div>

                <Form
                    v-if="
                        room.status === 'available' && room.available_slots > 0
                    "
                    :action="`/student/rooms/${room.id}/reserve`"
                    method="post"
                    v-slot="{ errors, processing }"
                    class="mt-3 grid gap-3 sm:grid-cols-3"
                >
                    <div class="grid gap-1">
                        <Label :for="`date-${room.id}`">Move-in date</Label>
                        <Input
                            :id="`date-${room.id}`"
                            name="move_in_date"
                            type="date"
                            required
                        />
                        <InputError :message="errors.move_in_date" />
                    </div>
                    <div class="grid gap-1">
                        <Label :for="`msg-${room.id}`"
                            >Message (optional)</Label
                        >
                        <Input
                            :id="`msg-${room.id}`"
                            name="message"
                            placeholder="Hi, I'm interested"
                        />
                    </div>
                    <div class="flex items-end">
                        <Button type="submit" :disabled="processing"
                            >Request reservation</Button
                        >
                    </div>
                </Form>
                <p v-else class="mt-2 text-sm text-muted-foreground">
                    This room is full.
                </p>
            </div>
        </div>
    </div>
</template>
