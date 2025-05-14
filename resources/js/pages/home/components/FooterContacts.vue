<script setup lang="ts">
import { Ref, ref } from 'vue';

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
