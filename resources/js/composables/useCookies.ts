export default function useCookies() {
    function set(name: string, value: string, days: number = 365): void {
        if (typeof document === 'undefined') {
            return;
        }

        const maxAge = days * 24 * 60 * 60;

        document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
    }

    function get(name: string): any {
        if (typeof document === 'undefined') {
            return;
        }

        const cookie = document.cookie
            .split(';')
            .map((cookie) => cookie.trim())
            .find((cookie) => cookie.split('=')[0] === name);

        if (!cookie) {
            return null;
        }

        const value = cookie.split('=')[1];

        try {
            return JSON.parse(value);
        } catch {
            return value;
        }
    }

    function invalidate(name: string): void {
        const cookie = get(name);
        if (!cookie) {
            return;
        }

        document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:01 GMT`;
    }

    return { set, get, invalidate };
}
