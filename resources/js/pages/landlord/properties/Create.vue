<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

const textareaClass =
    'flex min-h-20 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50';
</script>

<template>
    <Head title="Add Property" />

    <div class="mx-auto max-w-2xl p-6">
        <h1 class="mb-6 text-2xl font-semibold">Add Property</h1>

        <Form
            action="/landlord/properties"
            method="post"
            v-slot="{ errors, processing }"
            class="grid gap-5"
        >
            <div class="grid gap-2">
                <Label for="name">Property name</Label>
                <Input
                    id="name"
                    name="name"
                    required
                    placeholder="e.g. Sampaguita Boarding House"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="address">Address</Label>
                <Input
                    id="address"
                    name="address"
                    required
                    placeholder="Street, Barangay, Kabacan"
                />
                <InputError :message="errors.address" />
            </div>

            <div class="grid gap-2">
                <Label for="description">Description</Label>
                <textarea
                    id="description"
                    name="description"
                    :class="textareaClass"
                ></textarea>
                <InputError :message="errors.description" />
            </div>

            <div class="grid gap-2">
                <Label for="rules">House rules</Label>
                <textarea
                    id="rules"
                    name="rules"
                    :class="textareaClass"
                    placeholder="Curfew, visitors, etc."
                ></textarea>
                <InputError :message="errors.rules" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="latitude">Latitude (optional)</Label>
                    <Input
                        id="latitude"
                        name="latitude"
                        type="number"
                        step="any"
                        placeholder="7.1"
                    />
                    <InputError :message="errors.latitude" />
                </div>
                <div class="grid gap-2">
                    <Label for="longitude">Longitude (optional)</Label>
                    <Input
                        id="longitude"
                        name="longitude"
                        type="number"
                        step="any"
                        placeholder="124.8"
                    />
                    <InputError :message="errors.longitude" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="images">Photos (up to 6, max 4 MB each)</Label>
                <Input
                    id="images"
                    name="images[]"
                    type="file"
                    accept="image/*"
                    multiple
                    class="cursor-pointer file:mr-3 file:rounded-md file:border file:bg-muted file:px-3 file:py-1 file:text-sm file:font-medium"
                />
                <InputError :message="errors.images" />
                <InputError :message="errors['images.0']" />
            </div>

            <div class="flex gap-3">
                <Button type="submit" :disabled="processing">
                    <Spinner v-if="processing" />
                    Save property
                </Button>
                <Button variant="outline" as-child>
                    <Link href="/landlord/properties">Cancel</Link>
                </Button>
            </div>
        </Form>
    </div>
</template>
