<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    BarChart3,
    Building2,
    ClipboardList,
    LayoutGrid,
    Search,
    ShieldCheck,
    Users,
} from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const page = usePage();

const role = computed(
    () => (page.props.auth as any)?.user?.role as string | undefined,
);

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
    ];

    if (role.value === 'landlord') {
        items.push({
            title: 'My Properties',
            href: '/landlord/properties',
            icon: Building2,
        });
        items.push({
            title: 'Reservations',
            href: '/landlord/reservations',
            icon: ClipboardList,
        });
        items.push({
            title: 'Reports',
            href: '/landlord/reports',
            icon: BarChart3,
        });
    }

    if (role.value === 'student') {
        items.push({
            title: 'Find a Dorm',
            href: '/student/dorms',
            icon: Search,
        });
        items.push({
            title: 'My Reservations',
            href: '/student/reservations',
            icon: ClipboardList,
        });
    }

    if (role.value === 'admin') {
        items.push({
            title: 'Verify Properties',
            href: '/admin/properties',
            icon: ShieldCheck,
        });
        items.push({
            title: 'Manage Users',
            href: '/admin/users',
            icon: Users,
        });
    }

    return items;
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>