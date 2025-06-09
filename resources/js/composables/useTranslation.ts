import { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';

export default function t(key: string, tags: string[] = []) {
    const translations = usePage<SharedData>().props.translations;

    const keyTranslationExists = Object.hasOwn(translations, key);

    return injectTags(keyTranslationExists ? translations[key] : key, tags);
}

function injectTags(string: string, tags: string[]) {
    tags.forEach((tag, index) => {
        const regexp = new RegExp(`<${index}>(.*?)<\\/${index}>`);

        const content = string.match(regexp);
        if (!content) {
            return;
        }

        const replacement = new DOMParser().parseFromString(`${tag}${content[1].toString()}`, 'text/html').body.innerHTML;

        string = string.replace(regexp, replacement);
    });

    return string;
}
