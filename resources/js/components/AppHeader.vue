<!-- eslint-disable import/order -->
<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { BookOpen, Folder, LayoutGrid, Menu, Search, CheckCheck, Handshake, GitCommitVertical, FilePlusCorner } from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
//import AppLogoIcon from '@/components/AppLogoIcon.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    NavigationMenu,
    NavigationMenuItem,
    NavigationMenuList,
    navigationMenuTriggerStyle,
} from '@/components/ui/navigation-menu';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { getInitials } from '@/composables/useInitials';
import { useModuleMemory } from '@/composables/useModuleMemory';
import { toUrl } from '@/lib/utils';
import { dashboard } from '@/routes';
import  nuevoTramite  from '@/routes/nuevo-tramite';
import estadoTramite from '@/routes/estado-tramite';
import autorizar from '@/routes/autorizar';
import type { BreadcrumbItem, NavItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const auth = computed(() => page.props.auth);
// eslint-disable-next-line @typescript-eslint/no-unused-vars
const { isCurrentOrParentUrl ,isCurrentUrl, whenCurrentUrl } = useCurrentUrl();
const { getModuleUrl } = useModuleMemory();

const activeItemStyles =
    ' bg-tfja-blue-dark text-white font-semibold';

// const mainNavItems: NavItem[] = [
const mainNavItems = computed<NavItem[]>(() => [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Nuevo trámite',
        href: getModuleUrl('nuevo-tramite', toUrl(nuevoTramite.index())),
        icon: FilePlusCorner,
    },
    {
        title: 'Estado de trámite',
        href: getModuleUrl('estado-tramite', toUrl(estadoTramite.index())),
        icon: GitCommitVertical,
    },
    {
        title: 'Autorizar',
        href: getModuleUrl('autorizar', toUrl(autorizar.index())),
        icon: Handshake,
    },
]);

const rightNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Search,
    },
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentación',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <div class="w-full bg-tfja-blue-nav text-white shadow-xs">
        <div class="border-b border-tfja-blue-dark">
            <div class="relative mx-auto flex h-16 items-center justify-between px-4 md:max-w-7xl">
                <!-- Mobile Menu -->
                <div class="lg:hidden">
                    <Sheet>
                        <SheetTrigger :as-child="true">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="mr-2 h-9 w-9 text-white hover:bg-tfja-blue-hover hover:text-white"
                            >
                                <Menu class="h-5 w-5" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="left" class="w-75 p-6">
                            <SheetTitle class="sr-only"
                                >Navigation menu</SheetTitle
                            >
                            <SheetHeader class="flex justify-start text-left">
                                <!-- <AppLogoIcon
                                    class="size-6 fill-current text-black dark:text-white"
                                /> -->
                            </SheetHeader>
                            <div
                                class="flex h-full flex-1 flex-col justify-between space-y-4 py-6"
                            >
                                <nav class="-mx-3 space-y-1">
                                    <Link
                                        v-for="item in mainNavItems"
                                        :key="item.title"
                                        :href="item.href"
                                        class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent"
                                        :class="
                                            whenCurrentUrl(
                                                item.href,
                                                activeItemStyles,
                                            )
                                        "
                                    >
                                        <component
                                            v-if="item.icon"
                                            :is="item.icon"
                                            class="h-5 w-5"
                                        />
                                        {{ item.title }}
                                    </Link>
                                </nav>
                                <div class="flex flex-col space-y-4">
                                    <a
                                        v-for="item in rightNavItems"
                                        :key="item.title"
                                        :href="toUrl(item.href)"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="flex items-center space-x-2 text-sm font-medium"
                                    >
                                        <component
                                            v-if="item.icon"
                                            :is="item.icon"
                                            class="h-5 w-5"
                                        />
                                        <span>{{ item.title }}</span>
                                    </a>
                                </div>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>

                
<TooltipProvider :delay-duration="200">
    <Tooltip>
        <TooltipTrigger :as-child="true">
            <Link
                :href="dashboard()"
                class="group flex items-center cursor-pointer select-none focus:outline-none"
            >
                <AppLogo />
            </Link>
        </TooltipTrigger>
        <TooltipContent side="bottom">
            <p>Menú principal</p>
        </TooltipContent>
    </Tooltip>
</TooltipProvider>

                <!-- Desktop Menu -->
                <div class="hidden h-full lg:flex lg:absolute lg:left-1/2 lg:top-0 lg:-translate-x-1/2">
                    <NavigationMenu class="flex h-full items-stretch">
                        <NavigationMenuList
                            class="flex h-full items-stretch space-x-2"
                        >
                            <NavigationMenuItem
                                v-for="(item, index) in mainNavItems"
                                :key="index"
                                class="relative flex h-full items-center"
                            >
                                <Link
                                    :class="[
                                        navigationMenuTriggerStyle(),
                                        whenCurrentUrl(
                                            item.href,
                                            activeItemStyles,
                                        ),
                                        'h-9 cursor-pointer px-3 text-xs uppercase tracking-wide text-slate-100 bg-transparent hover:bg-tfja-bronce-hover hover:text-white focus:bg-transparent focus:text-white focus-visible:outline-none focus-visible:ring-0 focus-visible:ring-offset-0',
                                    ]"
                                    :href="item.href"
                                >
                                    <component
                                        v-if="item.icon"
                                        :is="item.icon"
                                        class="mr-2 h-4 w-4 shrink-0 text-slate-100"
                                    />
                                    {{ item.title }}
                                </Link>
                                <div
                                    v-if="isCurrentOrParentUrl(item.href)"
                                    class="absolute bottom-0 left-0 h-0.5 w-full bg-tfja-bronze"
                                ></div>
                            </NavigationMenuItem>
                        </NavigationMenuList>
                    </NavigationMenu>
                </div>

                <div class="ml-auto flex items-center space-x-2">
                    <div class="relative flex items-center space-x-1">
                        <!-- <Button
                            variant="ghost"
                            size="icon"
                            class="group h-9 w-9 cursor-pointer"
                        >
                            <Search
                                class="size-5 opacity-80 group-hover:opacity-100"
                            />
                        </Button> -->

                        <div class="hidden space-x-1 lg:flex">
                            <template
                                v-for="item in rightNavItems"
                                :key="item.title"
                            >
                                <TooltipProvider :delay-duration="0">
                                    <Tooltip>
                                        <TooltipTrigger>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                as-child
                                                class="group h-9 w-9 text-slate-200 hover:bg-tfja-blue-hover hover:text-white cursor-pointer"
                                            >
                                                <a
                                                    :href="toUrl(item.href)"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                >
                                                    <span class="sr-only">{{
                                                        item.title
                                                    }}</span>
                                                    <component
                                                        :is="item.icon"
                                                        class="size-5 opacity-80 group-hover:opacity-100"
                                                    />
                                                </a>
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{{ item.title }}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                            </template>
                        </div>
                    </div>

                    <DropdownMenu>
    <DropdownMenuTrigger :as-child="true">
        <Button
            variant="ghost"
            size="icon"
            class="group relative size-8 rounded-full p-0 hover:bg-transparent focus-visible:ring-2 focus-visible:ring-tfja-bronze cursor-pointer"
        >
            <Avatar class="size-8 overflow-hidden rounded-full">
                <AvatarImage
                    v-if="auth.user?.avatar"
                    :src="auth.user.avatar"
                    :alt="auth.user?.name"
                />
                <AvatarFallback
                    class="rounded-full bg-tfja-bronze font-bold text-xs text-white transition-colors duration-200 group-hover:bg-tfja-bg group-hover:text-tfja-blue"
                >
                    {{ getInitials(auth.user?.name) }}
                </AvatarFallback>
            </Avatar>
        </Button>
    </DropdownMenuTrigger>

    <DropdownMenuContent align="end" class="w-56">
        <UserMenuContent :user="auth.user" />
    </DropdownMenuContent>
</DropdownMenu>
                </div>
            </div>
        </div>

        <div
            v-if="props.breadcrumbs.length > 1"
            class="flex w-full border-b border-sidebar-border/70"
        >
            <div
                class="mx-auto flex h-12 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl"
            >
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>
    </div>
</template>
