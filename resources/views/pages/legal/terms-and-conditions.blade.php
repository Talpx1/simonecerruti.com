<div class="min-h-screen bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

        {{-- Header --}}
        <header class="mb-12 border-b border-gray-200 dark:border-gray-800 pb-8">
            <p class="text-sm font-medium uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-3">
                {{ __('Legal') }}
            </p>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                {{ __('Terms and Conditions') }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Last updated:') }} <time>13/03/2026</time>
            </p>
        </header>

        <div class="space-y-10">

            {{-- Intro --}}
            <section>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ __('These Terms govern the use of this Website and any other related Agreement or legal relationship with the Owner in a legally binding way. Capitalised words are defined in the relevant dedicated section of this document.') }}
                </p>
                <p class="mt-3 text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ __('The User must read this document carefully.') }}
                </p>
            </section>

            {{-- Owner --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-violet-500 rounded-full inline-block"></span>
                    {{ __('Owner') }}
                </h2>
                <div
                    class="bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 space-y-2">
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        <span class="font-medium text-gray-900 dark:text-white">{{ __('Company Name:') }}</span>
                        {{ config('company.name') }}
                    </p>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        <span class="font-medium text-gray-900 dark:text-white">{{ __('VAT Number:') }}</span>
                        {{ config('company.vat') }}
                    </p>
                    {{-- <p class="text-sm text-gray-700 dark:text-gray-300">
                        <span class="font-medium text-gray-900 dark:text-white">{{ __('Address:') }}</span>
                        {{ config('company.address') }}
                    </p> --}}
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        <span class="font-medium text-gray-900 dark:text-white">{{ __('Contact Email:') }}</span>
                        <a href="mailto:{{ config('company.contacts.email') }}"
                            class="text-violet-600 dark:text-violet-400 hover:underline">
                            {{ config('company.contacts.email') }}
                        </a>
                    </p>
                </div>
            </section>

            {{-- What the user should know --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-violet-500 rounded-full inline-block"></span>
                    {{ __('What the User should know at a glance') }}
                </h2>
                <div
                    class="bg-violet-50 dark:bg-violet-950 border border-violet-200 dark:border-violet-800 rounded-xl p-5">
                    <ul class="space-y-2 text-sm text-violet-900 dark:text-violet-100">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-violet-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('This Website is intended for Users who are not consumers, i.e., it is directed to business Users only.') }}
                        </li>
                    </ul>
                </div>
            </section>

            {{-- Terms of Use --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-violet-500 rounded-full inline-block"></span>
                    {{ __('Terms of Use') }}
                </h2>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300 mb-3">
                    {{ __('Unless otherwise specified, the terms of use detailed in this section apply generally when using this Website. Single or additional conditions of use or access may apply in specific scenarios and in such cases are additionally indicated within this document.') }}
                </p>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ __('By using this Website, Users confirm to meet the following requirements: Users must qualify as Business Users.') }}
                </p>
            </section>

            {{-- Account Registration --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-violet-500 rounded-full inline-block"></span>
                    {{ __('Account Registration') }}
                </h2>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ __('To use the Service, Users must register or create a User account, providing all required data or information in a complete and truthful manner. Failure to do so will cause unavailability of the Service. Users are responsible for keeping their login credentials confidential and safe. For this reason, Users are also required to choose passwords that meet the highest standards of strength permitted by this Website.') }}
                </p>
                <p class="mt-3 text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ __('By registering, Users agree to be fully responsible for all activities that occur under their username and password. Users are required to immediately and unambiguously inform the Owner via the contact details indicated in this document, if they think their personal information, including but not limited to User accounts, access credentials or personal data, have been violated, unduly disclosed or stolen.') }}
                </p>
            </section>

            {{-- Content --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-violet-500 rounded-full inline-block"></span>
                    {{ __('Content on this Website') }}
                </h2>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300 mb-3">
                    {{ __('Unless where otherwise specified or clearly recognizable, all content available on this Website is owned or provided by the Owner or its licensors.') }}
                </p>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ __('The Owner undertakes its utmost effort to ensure that the content provided on this Website infringes no applicable legal provisions or third-party rights. However, it may not always be possible to achieve such a result. In such cases, without prejudice to any legal prerogatives of Users to enforce their rights, Users are kindly asked to preferably report related complaints using the contact details provided in this document.') }}
                </p>
            </section>

            {{-- Access to External Resources --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-violet-500 rounded-full inline-block"></span>
                    {{ __('Access to External Resources') }}
                </h2>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ __('Through this Website, Users may have access to external resources provided by third parties. Users acknowledge and accept that the Owner has no control over such resources and is therefore not responsible for their content and availability.') }}
                </p>
                <p class="mt-3 text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ __('Conditions applicable to any resources provided by third parties, including those applicable to any possible grant of rights in content, result from each such third parties\' terms and conditions or, in the absence of those, applicable statutory law.') }}
                </p>
                <p class="mt-3 text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ __('This Website uses the following third-party services: Font Bunny (web fonts), Koalenda (appointment scheduling), Microsoft Clarity (analytics), StatusCake (uptime monitoring), Hetzner (hosting), Google reCAPTCHA (spam protection), Google Drive (backup storage), WhatsApp (chat communication).') }}
                </p>
            </section>

            {{-- Acceptable Use --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-violet-500 rounded-full inline-block"></span>
                    {{ __('Acceptable Use') }}
                </h2>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300 mb-4">
                    {{ __('This Website and the Service may only be used within the scope of what they are provided for, under these Terms and applicable law. Users are solely responsible for making sure that their use of this Website and/or the Service violates no applicable law, regulations or third-party rights.') }}
                </p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                    {{ __('Therefore, the Owner reserves the right to take any appropriate measure to protect its legitimate interests including by:') }}
                </p>
                <ul class="space-y-1 text-sm text-gray-700 dark:text-gray-300">
                    @foreach ([__('Denying Users access to this Website or the Service.'), __('Removing or editing content.'), __('Reporting any misconduct performed through this Website or the Service to the competent authorities.')] as $item)
                        <li class="flex items-start gap-2">
                            <span class="mt-1.5 w-1.5 h-1.5 rounded-full bg-violet-400 flex-shrink-0"></span>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
            </section>

            {{-- Liability --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-violet-500 rounded-full inline-block"></span>
                    {{ __('Limitation of Liability') }}
                </h2>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300 mb-3">
                    {{ __('Unless otherwise explicitly stated and without prejudice to applicable statutory product liability provisions, Users shall have no right to claim damages against the Owner (or any natural or legal person acting on its behalf).') }}
                </p>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ __('This does not apply to damages to life, body, or health, damages resulting from the breach of an essential contractual obligation (i.e. an obligation necessary to achieve the purpose of the contract), and/or damages resulting from intent or gross negligence, as long as this Website has been appropriately and correctly used by the User.') }}
                </p>
            </section>

            {{-- Intellectual Property --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-violet-500 rounded-full inline-block"></span>
                    {{ __('Intellectual Property Rights') }}
                </h2>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ __('Without prejudice to any more specific provision of these Terms, any intellectual property rights, such as copyrights, trademark rights, patent rights and design rights related to this Website are the exclusive property of the Owner or its licensors and are subject to the protection granted by applicable laws or international treaties relating to intellectual property.') }}
                </p>
                <p class="mt-3 text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ __('All trademarks — nominal or figurative — and all other marks, trade names, service marks, word marks, illustrations, images, or logos appearing in connection with this Website are, and remain, the exclusive property of the Owner or its licensors and are subject to the protection granted by applicable laws or international treaties related to intellectual property.') }}
                </p>
            </section>

            {{-- Changes --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-violet-500 rounded-full inline-block"></span>
                    {{ __('Changes to these Terms') }}
                </h2>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ __('The Owner reserves the right to amend or otherwise modify these Terms at any time. In such cases, the Owner will appropriately inform the User of these changes. Such changes will only affect the relationship with the User from the date communicated to Users onwards. The continued use of the Service will signify the User\'s acceptance of the revised Terms.') }}
                </p>
            </section>

            {{-- Governing Law --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-violet-500 rounded-full inline-block"></span>
                    {{ __('Governing Law and Jurisdiction') }}
                </h2>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ __('These Terms are governed by the law of the place where the Owner is based, as disclosed in the relevant section of this document, without regard to conflict of laws principles. The exclusive jurisdiction to rule on any controversy resulting from or connected to these Terms lies with the courts of the place where the Owner is based.') }}
                </p>
            </section>

            {{-- Definitions --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-violet-500 rounded-full inline-block"></span>
                    {{ __('Definitions') }}
                </h2>
                <div class="space-y-3">
                    @foreach ([[__('This Website (or this Application)'), __('The property that enables the provision of the Service.')], [__('Agreement'), __('Any legally binding or contractual relationship between the Owner and the User, governed by these Terms.')], [__('Business User'), __('Any User that does not qualify as a Consumer.')], [__('Owner'), config('company.name') . ' – ' . config('company.vat')], [__('Service'), __('The service provided by this Website as described in these Terms.')], [__('Terms'), __('All provisions applicable to the use of this Website and/or the Service as described in this document, including any other related documents or agreements.')], [__('User'), __('Any natural person who uses this Website.')]] as [$term, $def])
                        <div class="flex gap-4 py-3 border-b border-gray-100 dark:border-gray-800 last:border-0">
                            <dt class="w-48 flex-shrink-0 text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $term }}</dt>
                            <dd class="text-sm text-gray-700 dark:text-gray-300">{{ $def }}</dd>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Privacy Policy link --}}
            <section
                class="bg-violet-50 dark:bg-violet-950 border border-violet-200 dark:border-violet-800 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-violet-900 dark:text-violet-100 mb-2">
                    {{ __('Privacy Policy') }}
                </h2>
                <p class="text-sm text-violet-800 dark:text-violet-200 mb-3">
                    {{ __('To find out about the types of Personal Data collected through this Website, the purposes of the processing, and Users\' rights, please consult the Privacy Policy.') }}
                </p>
                <a href="{{ route('privacy_policy') }}"
                    class="inline-flex items-center gap-1 text-sm font-medium text-violet-700 dark:text-violet-300 hover:underline">
                    {{ __('Read the Privacy Policy') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </section>

        </div>
    </div>
</div>
