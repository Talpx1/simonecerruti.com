<div>
    <section class="legal-hero">
        <h1 class="legal-h1">{{ __('Terms and Conditions') }}<span class="acc">.</span></h1>

        <div class="legal-meta">
            <p>{{ __('The rules for using this website. Short, clear, no surprises.') }}</p>
            <span>{{ __('Last updated:') }} <time>13/03/2026</time></span>
        </div>

        <x-legal-switcher current="terms" />
    </section>

    <div class="art-grid legal-grid" data-fx="legal">
        <aside class="art-side" aria-label="{{ __('Index') }}">
            <div class="art-prog" aria-hidden="true"><i></i></div>
            <nav class="toc" aria-label="{{ __('Index') }}">
                <a href="#owner">{{ __('Owner') }}</a>
                <a href="#what-the-user-should-know-at-a-glance">{{ __('What the User should know at a glance') }}</a>
                <a href="#terms-of-use">{{ __('Terms of Use') }}</a>
                <a href="#account-registration">{{ __('Account Registration') }}</a>
                <a href="#content-on-this-website">{{ __('Content on this Website') }}</a>
                <a href="#access-to-external-resources">{{ __('Access to External Resources') }}</a>
                <a href="#acceptable-use">{{ __('Acceptable Use') }}</a>
                <a href="#limitation-of-liability">{{ __('Limitation of Liability') }}</a>
                <a href="#intellectual-property-rights">{{ __('Intellectual Property Rights') }}</a>
                <a href="#changes-to-these-terms">{{ __('Changes to these Terms') }}</a>
                <a href="#governing-law-and-jurisdiction">{{ __('Governing Law and Jurisdiction') }}</a>
                <a href="#definitions">{{ __('Definitions') }}</a>
                <a href="#privacy-policy">{{ __('Privacy Policy') }}</a>
            </nav>
        </aside>

        <article class="art-body legal-body">

        <section>
            <p>
                {{ __('These Terms govern the use of this Website and any other related Agreement or legal relationship with the Owner in a legally binding way. Capitalised words are defined in the relevant dedicated section of this document.') }}
            </p>
            <p>
                {{ __('The User must read this document carefully.') }}
            </p>
        </section>

        <section>
            <h2 id="owner">
                {{ __('Owner') }}
            </h2>
            <div>
                <p>
                    <b>{{ __('Company Name:') }}</b>
                    {{ config('company.name') }}
                </p>
                <p>
                    <b>{{ __('VAT Number:') }}</b>
                    {{ config('company.vat') }}
                </p>

                <p>
                    <b>{{ __('Contact Email:') }}</b>
                    <a href="mailto:{{ config('company.contacts.email') }}">
                        {{ config('company.contacts.email') }}
                    </a>
                </p>
            </div>
        </section>

        <section>
            <h2 id="what-the-user-should-know-at-a-glance">
                {{ __('What the User should know at a glance') }}
            </h2>
            <div>
                <ul>
                    <li>

                        {{ __('This Website is intended for Users who are not consumers, i.e., it is directed to business Users only.') }}
                    </li>
                </ul>
            </div>
        </section>

        <section>
            <h2 id="terms-of-use">
                {{ __('Terms of Use') }}
            </h2>
            <p>
                {{ __('Unless otherwise specified, the terms of use detailed in this section apply generally when using this Website. Single or additional conditions of use or access may apply in specific scenarios and in such cases are additionally indicated within this document.') }}
            </p>
            <p>
                {{ __('By using this Website, Users confirm to meet the following requirements: Users must qualify as Business Users.') }}
            </p>
        </section>

        <section>
            <h2 id="account-registration">
                {{ __('Account Registration') }}
            </h2>
            <p>
                {{ __('To use the Service, Users must register or create a User account, providing all required data or information in a complete and truthful manner. Failure to do so will cause unavailability of the Service. Users are responsible for keeping their login credentials confidential and safe. For this reason, Users are also required to choose passwords that meet the highest standards of strength permitted by this Website.') }}
            </p>
            <p>
                {{ __('By registering, Users agree to be fully responsible for all activities that occur under their username and password. Users are required to immediately and unambiguously inform the Owner via the contact details indicated in this document, if they think their personal information, including but not limited to User accounts, access credentials or personal data, have been violated, unduly disclosed or stolen.') }}
            </p>
        </section>

        <section>
            <h2 id="content-on-this-website">
                {{ __('Content on this Website') }}
            </h2>
            <p>
                {{ __('Unless where otherwise specified or clearly recognizable, all content available on this Website is owned or provided by the Owner or its licensors.') }}
            </p>
            <p>
                {{ __('The Owner undertakes its utmost effort to ensure that the content provided on this Website infringes no applicable legal provisions or third-party rights. However, it may not always be possible to achieve such a result. In such cases, without prejudice to any legal prerogatives of Users to enforce their rights, Users are kindly asked to preferably report related complaints using the contact details provided in this document.') }}
            </p>
        </section>

        <section>
            <h2 id="access-to-external-resources">
                {{ __('Access to External Resources') }}
            </h2>
            <p>
                {{ __('Through this Website, Users may have access to external resources provided by third parties. Users acknowledge and accept that the Owner has no control over such resources and is therefore not responsible for their content and availability.') }}
            </p>
            <p>
                {{ __('Conditions applicable to any resources provided by third parties, including those applicable to any possible grant of rights in content, result from each such third parties\' terms and conditions or, in the absence of those, applicable statutory law.') }}
            </p>
            <p>
                {{ __('This Website uses the following third-party services: Font Bunny (web fonts), Koalenda (appointment scheduling), Microsoft Clarity (analytics), StatusCake (uptime monitoring), Hetzner (hosting), Google reCAPTCHA (spam protection), Google Drive (backup storage), WhatsApp (chat communication).') }}
            </p>
        </section>

        <section>
            <h2 id="acceptable-use">
                {{ __('Acceptable Use') }}
            </h2>
            <p>
                {{ __('This Website and the Service may only be used within the scope of what they are provided for, under these Terms and applicable law. Users are solely responsible for making sure that their use of this Website and/or the Service violates no applicable law, regulations or third-party rights.') }}
            </p>
            <p>
                {{ __('Therefore, the Owner reserves the right to take any appropriate measure to protect its legitimate interests including by:') }}
            </p>
            <ul>
                @foreach ([__('Denying Users access to this Website or the Service.'), __('Removing or editing content.'), __('Reporting any misconduct performed through this Website or the Service to the competent authorities.')] as $item)
                    <li>

                        {{ $item }}
                    </li>
                @endforeach
            </ul>
        </section>

        <section>
            <h2 id="limitation-of-liability">
                {{ __('Limitation of Liability') }}
            </h2>
            <p>
                {{ __('Unless otherwise explicitly stated and without prejudice to applicable statutory product liability provisions, Users shall have no right to claim damages against the Owner (or any natural or legal person acting on its behalf).') }}
            </p>
            <p>
                {{ __('This does not apply to damages to life, body, or health, damages resulting from the breach of an essential contractual obligation (i.e. an obligation necessary to achieve the purpose of the contract), and/or damages resulting from intent or gross negligence, as long as this Website has been appropriately and correctly used by the User.') }}
            </p>
        </section>

        <section>
            <h2 id="intellectual-property-rights">
                {{ __('Intellectual Property Rights') }}
            </h2>
            <p>
                {{ __('Without prejudice to any more specific provision of these Terms, any intellectual property rights, such as copyrights, trademark rights, patent rights and design rights related to this Website are the exclusive property of the Owner or its licensors and are subject to the protection granted by applicable laws or international treaties relating to intellectual property.') }}
            </p>
            <p>
                {{ __('All trademarks — nominal or figurative — and all other marks, trade names, service marks, word marks, illustrations, images, or logos appearing in connection with this Website are, and remain, the exclusive property of the Owner or its licensors and are subject to the protection granted by applicable laws or international treaties related to intellectual property.') }}
            </p>
        </section>

        <section>
            <h2 id="changes-to-these-terms">
                {{ __('Changes to these Terms') }}
            </h2>
            <p>
                {{ __('The Owner reserves the right to amend or otherwise modify these Terms at any time. In such cases, the Owner will appropriately inform the User of these changes. Such changes will only affect the relationship with the User from the date communicated to Users onwards. The continued use of the Service will signify the User\'s acceptance of the revised Terms.') }}
            </p>
        </section>

        <section>
            <h2 id="governing-law-and-jurisdiction">
                {{ __('Governing Law and Jurisdiction') }}
            </h2>
            <p>
                {{ __('These Terms are governed by the law of the place where the Owner is based, as disclosed in the relevant section of this document, without regard to conflict of laws principles. The exclusive jurisdiction to rule on any controversy resulting from or connected to these Terms lies with the courts of the place where the Owner is based.') }}
            </p>
        </section>

        <section>
            <h2 id="definitions">
                {{ __('Definitions') }}
            </h2>
            <dl>
                @foreach ([[__('This Website (or this Application)'), __('The property that enables the provision of the Service.')], [__('Agreement'), __('Any legally binding or contractual relationship between the Owner and the User, governed by these Terms.')], [__('Business User'), __('Any User that does not qualify as a Consumer.')], [__('Owner'), config('company.name') . ' – ' . config('company.vat')], [__('Service'), __('The service provided by this Website as described in these Terms.')], [__('Terms'), __('All provisions applicable to the use of this Website and/or the Service as described in this document, including any other related documents or agreements.')], [__('User'), __('Any natural person who uses this Website.')]] as [$term, $def])
                    <div>
                        <dt>
                            {{ $term }}</dt>
                        <dd>{{ $def }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>

        <section>
            <h2 id="privacy-policy">
                {{ __('Privacy Policy') }}
            </h2>
            <p>
                {{ __('To find out about the types of Personal Data collected through this Website, the purposes of the processing, and Users\' rights, please consult the Privacy Policy.') }}
            </p>
            <a href="{{ route('privacy_policy') }}">
                {{ __('Read the Privacy Policy') }}

            </a>
        </section>
        </article>
    </div>
</div>
