<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import t from '@/composables/useTranslation';
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

const open = defineModel<boolean>({ required: true });

const form = useForm({
    name: undefined,
    email: undefined,
    phone: undefined,
    message: undefined,
    source: 'landing-dialog-contact-form',
});

function onSubmit() {
    form.clearErrors();

    form.post(route('contact-lead.store'), {
        preserveScroll: true,
        onError: () =>
            toast.warning(t("Oh oh! Let's take another look"), {
                description: t('It seems like there is something wrong with the data you entered... it always happens'),
            }),
        onSuccess: () => {
            open.value = false;
            toast.success(t('One step closer to the realization of your project'), {
                description: t(
                    'Stamp placed and letter sent! I will read your proposal and you will hear from me in a flash. Thanks for contacting me!',
                ),
            });
            form.reset();
        },
    });
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ $t("Let's get in touch") }}</DialogTitle>
                <DialogDescription> {{ $t("Let's talk about how to make your idea a reality.") }} </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="onSubmit" class="space-y-4" id="dialogContactForm">
                <div class="space-y-1">
                    <Label for="contactFormNameField">{{ $t('Name') }} *</Label>
                    <Input
                        type="text"
                        id="contactFormNameField"
                        name="name"
                        autocomplete="given-name"
                        v-model="form.name"
                        :placeholder="$t('Your name. Let\'s start with the simple things')"
                        required
                    />
                    <div v-if="form.errors.name">{{ form.errors.name }}</div>
                </div>

                <div class="space-y-1">
                    <Label for="contactFormEmailField">{{ $t('Email') }} *</Label>
                    <Input
                        type="email"
                        id="contactFormEmailField"
                        name="email"
                        autocomplete="email"
                        v-model="form.email"
                        :placeholder="$t('Your email, but not the one you use for newsletters')"
                        required
                    />
                    <div v-if="form.errors.email">{{ form.errors.email }}</div>
                </div>

                <div class="space-y-1">
                    <Label for="contactFormPhoneField">{{ $t('Phone') }}</Label>
                    <Input
                        type="tel"
                        inputmode="tel"
                        id="contactFormPhoneField"
                        name="phone"
                        autocomplete="tel"
                        :placeholder="$t('I\'d be flattered if you\'d give me your number... but only if you want')"
                        v-model="form.phone"
                    />
                    <div v-if="form.errors.phone">{{ form.errors.phone }}</div>
                </div>

                <div class="space-y-1">
                    <Label for="contactFormMessageField">{{ $t('Message') }} *</Label>
                    <Textarea
                        id="contactFormMessageField"
                        name="message"
                        v-model="form.message"
                        :placeholder="$t('And now for the important things, tell me your vision, your need, your idea. Don\'t spare any detail!')"
                        required
                    />
                </div>
                <div v-if="form.errors.message">{{ form.errors.message }}</div>
            </form>

            <DialogFooter>
                <Button :disabled="form.processing" type="submit" form="dialogContactForm">{{ $t("Let's call this a beginning") }}</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
