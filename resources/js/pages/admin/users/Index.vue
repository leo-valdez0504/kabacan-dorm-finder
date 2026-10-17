<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

type Item = {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    role: string;
    verified: boolean;
};

defineProps<{ users: Item[] }>();
</script>

<template>
    <Head title="Manage Users" />

    <div class="mx-auto max-w-5xl p-6">
        <h1 class="mb-6 text-2xl font-semibold">Manage Users</h1>

        <div class="grid gap-3">
            <div
                v-for="u in users"
                :key="u.id"
                class="flex items-center justify-between rounded-lg border p-4"
            >
                <div>
                    <div class="font-medium">{{ u.name }}</div>
                    <div class="text-sm text-muted-foreground">
                        {{ u.email
                        }}<span v-if="u.phone"> · {{ u.phone }}</span>
                    </div>
                    <div class="mt-1 text-xs capitalize">
                        {{ u.role }} ·
                        {{
                            u.verified ? 'Email verified' : 'Email not verified'
                        }}
                    </div>
                </div>
                <Form
                    v-if="u.role !== 'admin'"
                    :action="`/admin/users/${u.id}`"
                    method="delete"
                    @submit="
                        (e: Event) => {
                            if (
                                !confirm('Delete this user and all their data?')
                            )
                                e.preventDefault();
                        }
                    "
                >
                    <Button type="submit" variant="destructive" size="sm"
                        >Delete</Button
                    >
                </Form>
            </div>
        </div>
    </div>
</template>
