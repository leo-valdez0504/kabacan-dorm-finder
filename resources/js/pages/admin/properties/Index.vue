<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

type Item = {
    id: number;
    name: string;
    address: string;
    landlord: string;
    landlord_email: string;
    rooms_count: number;
    is_verified: boolean;
};

defineProps<{ properties: Item[] }>();
</script>

<template>
    <Head title="Verify Properties" />

    <div class="mx-auto max-w-5xl p-6">
        <h1 class="mb-6 text-2xl font-semibold">Verify Properties</h1>

        <p v-if="properties.length === 0" class="text-muted-foreground">
            No properties yet.
        </p>

        <div class="grid gap-3">
            <div
                v-for="p in properties"
                :key="p.id"
                class="flex items-center justify-between rounded-lg border p-4"
            >
                <div>
                    <div class="font-medium">{{ p.name }}</div>
                    <div class="text-sm text-muted-foreground">
                        {{ p.address }}
                    </div>
                    <div class="text-sm">
                        Landlord: {{ p.landlord }}
                        <span class="text-muted-foreground"
                            >· {{ p.landlord_email }}</span
                        >
                    </div>
                    <div class="mt-1 text-xs">
                        {{ p.rooms_count }} room(s) ·
                        <span
                            :class="
                                p.is_verified
                                    ? 'text-green-700'
                                    : 'text-yellow-700'
                            "
                        >
                            {{
                                p.is_verified
                                    ? 'Verified'
                                    : 'Pending verification'
                            }}
                        </span>
                    </div>
                </div>
                <Form
                    :action="`/admin/properties/${p.id}/verify`"
                    method="post"
                >
                    <Button
                        type="submit"
                        size="sm"
                        :variant="p.is_verified ? 'outline' : 'default'"
                    >
                        {{ p.is_verified ? 'Remove verification' : 'Verify' }}
                    </Button>
                </Form>
            </div>
        </div>
    </div>
</template>
