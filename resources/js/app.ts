/* eslint-disable import/order */
import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import MagistradosLayout from '@/layouts/nuevo-tramite/Layout.vue';
import nuevoTramite from '@/layouts/nuevo-tramite/Layout.vue';
import estadoTramite from '@/layouts/estado-tramite/Layout.vue';
import autorizar from '@/layouts/autorizar/Layout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
                return null;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];
            case name.startsWith('Dev/'):
                return [AppLayout, MagistradosLayout];
            case name.startsWith('nuevo-tramite/'):
                return [AppLayout, nuevoTramite];
            case name.startsWith('estado-tramite/'):
                return [AppLayout, estadoTramite];
            case name.startsWith('autorizar/'):
                return [AppLayout, autorizar];
            default:
                return AppLayout;
        }
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();
