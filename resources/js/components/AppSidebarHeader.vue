<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { LogOut } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { logout } from '@/routes';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const handleLogout = () => {
    router.flushAll();
};
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex min-w-0 items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <div class="hidden h-11 w-52 items-center sm:flex">
                <img
                    src="/images/gorakmana/logo-dark.png"
                    alt="GoRakmana Digital Solutions"
                    class="h-full w-full object-contain"
                />
            </div>
        </div>
        <Button variant="ghost" size="sm" class="ml-auto gap-2" as-child>
            <Link
                :href="logout()"
                as="button"
                data-test="header-logout-button"
                @click="handleLogout"
            >
                <LogOut class="size-4" />
                <span class="hidden sm:inline">Déconnexion</span>
            </Link>
        </Button>
    </header>
</template>
