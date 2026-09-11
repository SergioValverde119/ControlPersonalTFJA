import type { ComputedRef, Ref } from 'vue';
import { computed, ref } from 'vue';
import type { Appearance, ResolvedAppearance } from '@/types';

export type { Appearance, ResolvedAppearance };

export type UseAppearanceReturn = {
    appearance: Ref<Appearance>;
    resolvedAppearance: ComputedRef<ResolvedAppearance>;
    updateAppearance: (value: Appearance) => void;
};

export function updateTheme(): void {
    if (typeof document === 'undefined') {
        return;
    }

    document.documentElement.classList.remove('dark');
}

export function initializeTheme(): void {
    updateTheme();
}

const appearance = ref<Appearance>('light');

export function useAppearance(): UseAppearanceReturn {
    const resolvedAppearance = computed<ResolvedAppearance>(() => 'light');

    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    function updateAppearance(_value: Appearance = 'light') {
        appearance.value = 'light';

        if (typeof localStorage !== 'undefined') {
            localStorage.setItem('appearance', 'light');
        }

        if (typeof document !== 'undefined') {
            document.cookie = 'appearance=light;path=/;max-age=31536000;SameSite=Lax';
        }

        updateTheme();
    }

    return {
        appearance,
        resolvedAppearance,
        updateAppearance,
    };
}
