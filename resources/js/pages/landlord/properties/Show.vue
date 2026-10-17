<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
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
    images: { id: number; url: string }[];
    rooms: Room[];
};

defineProps<{ property: PropertyData }>();
</script>

<template>
    <Head :title="property.name" />

    <div class="mx-auto max-w-4xl p-6">
        <Link href="/landlord/properties" class="text-sm underline"
            >← Back to my properties</Link
        >

        <div class="mt-4 flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-semibold">{{ property.name }}</h1>
                <p class="text-muted-foreground">{{ property.address }}</p>
                <p class="mt-1 text-xs">
                    {{
                        property.is_verified
                            ? 'Verified'
                            : 'Pending verification'
                    }}
                </p>
            </div>
            <Form
                :action="`/landlord/properties/${property.id}`"
                method="delete"
                @submit="
                    (e: Event) => {
                        if (!confirm('Delete this property?'))
                            e.preventDefault();
                    }
                "
            >
                <Button type="submit" variant="destructive" size="sm"
                    >Delete property</Button
                >
            </Form>
        </div>

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
            <h2 class="font-medium">Description</h2>
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

        <h2 class="mt-8 text-lg font-semibold">Rooms</h2>

        <p
            v-if="property.rooms.length === 0"
            class="mt-2 text-sm text-muted-foreground"
        >
            No rooms yet. Add one below.
        </p>

        <div class="mt-3 grid gap-3">
            <div
                v-for="room in property.rooms"
                :key="room.id"
                class="flex items-center justify-between rounded-lg border p-4"
            >
                <div>
                    <div class="font-medium capitalize">
                        {{ room.room_type }} · ₱{{ room.price }}/month
                    </div>
                    <div class="text-sm text-muted-foreground">
                        {{ room.available_slots }} of
                        {{ room.capacity }} slot(s) available ·
                        {{ room.status }}
                    </div>
                </div>
                <Form :action="`/landlord/rooms/${room.id}`" method="delete">
                    <Button type="submit" variant="outline" size="sm"
                        >Remove</Button
                    >
                </Form>
            </div>
        </div>

        <h3 class="mt-8 font-medium">Add a room</h3>
        <Form
            :action="`/landlord/properties/${property.id}/rooms`"
            method="post"
            v-slot="{ errors, processing }"
            :reset-on-success="['capacity', 'price']"
            class="mt-3 grid gap-4 sm:grid-cols-4"
        >
            <div class="grid gap-2">
                <Label for="room_type">Type</Label>
                <select
                    id="room_type"
                    name="room_type"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm"
                >
                    <option value="single">Single</option>
                    <option value="shared">Shared</option>
                    <option value="bedspace">Bedspace</option>
                </select>
                <InputError :message="errors.room_type" />
            </div>
            <div class="grid gap-2">
                <Label for="capacity">Capacity</Label>
                <Input
                    id="capacity"
                    name="capacity"
                    type="number"
                    min="1"
                    required
                />
                <InputError :message="errors.capacity" />
            </div>
            <div class="grid gap-2">
                <Label for="price">Price (₱/month)</Label>
                <Input
                    id="price"
                    name="price"
                    type="number"
                    min="0"
                    step="0.01"
                    required
                />
                <InputError :message="errors.price" />
            </div>
            <div class="flex items-end">
                <Button type="submit" :disabled="processing">Add room</Button>
            </div>
        </Form>
    </div>
</template>
