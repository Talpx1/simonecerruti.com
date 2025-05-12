<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Ref, ref } from 'vue';
import HomeHeader from './components/HomeHeader.vue';

const shouldShowEmail = ref(false);
const shouldShowLinkedin = ref(false);
const shouldShowGithub = ref(false);

type Contact = 'email' | 'linkedin' | 'github';
const contactMap: Record<Contact, Ref<boolean>> = {
    email: shouldShowEmail,
    linkedin: shouldShowLinkedin,
    github: shouldShowGithub,
};

const timeouts: Record<Contact, number[]> = {
    email: [],
    linkedin: [],
    github: [],
};

const lastContact: Ref<Contact> = ref('email');

function cleatTimeoutsForContact(contact: Contact) {
    timeouts[contact].forEach((id) => clearTimeout(id));
}

function showContact(contact: Contact) {
    if (contact !== lastContact.value) {
        cleatTimeoutsForContact(lastContact.value);
        contactMap[lastContact.value].value = false;
    }

    cleatTimeoutsForContact(contact);

    const selectedContact = contactMap[contact];

    selectedContact.value = true;

    lastContact.value = contact;
}

function hideContactAfterDelay(contact: Contact, delayInSeconds: number = 1) {
    const selectedContact = contactMap[contact];

    const timeoutId = setTimeout(() => (selectedContact.value = false), delayInSeconds * 1000);

    timeouts[contact].push(timeoutId);
}
</script>

<template>
    <Head title="Welcome" />
    <div class="bg-light dark:bg-dark text-dark dark:text-light flex min-h-screen flex-col justify-between">
        <HomeHeader />

        <main>
            <h1 class="px-5 text-6xl font-semibold tracking-tighter">
                CREO IL <span class="bg-dark dark:bg-light text-light dark:text-dark">SOFTWARE</span> CHE STAI CERCANDO...<br />
                E CIÒ CHE NEMMENO SAPEVI DI <span class="underline decoration-5 underline-offset-5">DESIDERARE</span>.
            </h1>
            <div class="to-dark dark:to-light mt-1 flex w-full justify-end bg-linear-to-r from-transparent to-60% py-4">
                <h2 class="text-light dark:text-dark px-4 text-5xl font-black"><a href="#">NON APSETTARE, PARLIAMO 🡒</a></h2>
            </div>
        </main>

        <footer class="flex items-end justify-between px-5 py-3">
            <p class="font-thin">© 2024-{{ new Date().getFullYear() }} Simone Cerruti</p>
            <div>
                <div
                    class="relative flex w-full justify-center text-6xl font-black whitespace-nowrap uppercase *:absolute *:mx-auto *:-translate-y-full *:opacity-50 *:transition-opacity *:hover:opacity-100"
                >
                    <Transition>
                        <div v-show="shouldShowEmail" @mouseover="showContact('email')" @mouseleave="hideContactAfterDelay('email')">
                            <a href="mailto:hello@simonecerruti.com">hello@simonecerruti.com</a>
                        </div>
                    </Transition>

                    <Transition>
                        <div v-show="shouldShowLinkedin" @mouseover="showContact('linkedin')" @mouseleave="hideContactAfterDelay('linkedin')">
                            <a href="https://www.linkedin.com/in/simone-cerruti/">Simone Cerruti</a>
                        </div>
                    </Transition>

                    <Transition>
                        <div v-show="shouldShowGithub" @mouseover="showContact('github')" @mouseleave="hideContactAfterDelay('github')">
                            <a href="https://github.com/Talpx1/">Talpx1</a>
                        </div>
                    </Transition>
                </div>

                <div class="text-2xl font-bold">
                    <span @mouseover="showContact('email')" @mouseleave="hideContactAfterDelay('email')">
                        <a href="mailto:hello@simonecerruti.com">EMAIL</a>
                    </span>
                    /
                    <span @mouseover="showContact('linkedin')" @mouseleave="hideContactAfterDelay('linkedin')">
                        <a href="https://www.linkedin.com/in/simone-cerruti/">LINKEDIN</a>
                    </span>
                    /
                    <span @mouseover="showContact('github')" @mouseleave="hideContactAfterDelay('github')">
                        <a href="https://github.com/Talpx1/">GITHUB</a>
                    </span>
                </div>
            </div>
            <p class="font-thin">P.IVA 02790210021</p>
        </footer>
    </div>
</template>

<style lang="css" scoped>
.v-enter-active,
.v-leave-active {
    transition: opacity 0.3s ease-in-out;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}
</style>
