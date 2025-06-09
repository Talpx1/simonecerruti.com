import { SharedData } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import useCookies from './useCookies';

export function useLocale() {
    const locale = ref(usePage<SharedData>().props.locale);
    const locales = ref(usePage<SharedData>().props.locales);
    const cookies = useCookies();

    onMounted(() => {
        const savedLocale = cookies.get('locale');

        if (savedLocale) {
            locale.value = savedLocale;
        }
    });

    function updateLocale(newLocale: string) {
        newLocale = newLocale.toLowerCase();

        if (newLocale === locale.value || !locales.value.includes(newLocale)) {
            return;
        }

        cookies.set('locale', newLocale);
        locale.value = newLocale;

        router.reload();
    }

    return {
        currentLocale: locale,
        availableLocales: locales,
        updateLocale,
    };
}
