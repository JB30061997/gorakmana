<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { FileCheck2, FileText, PackageCheck, Users, Wrench } from '@lucide/vue';
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
import { clients, services } from '@/routes/catalog';
import { invoices, orders, quotes } from '@/routes/commercial';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Devis',
        href: quotes(),
        icon: FileText,
    },
    {
        title: 'Commandes',
        href: orders(),
        icon: PackageCheck,
    },
    {
        title: 'Factures',
        href: invoices(),
        icon: FileCheck2,
    },
    {
        title: 'Clients',
        href: clients(),
        icon: Users,
    },
    {
        title: 'Services',
        href: services(),
        icon: Wrench,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        size="lg"
                        as-child
                        class="h-24 w-full p-0 hover:bg-transparent"
                    >
                        <Link :href="quotes()">
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
