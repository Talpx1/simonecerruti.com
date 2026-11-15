@php
    $rules = [
        [
            'title' => 'Orders after 5 pm',
            'rule' => 'If an order arrives after 5 pm and the customer already has 3 open orders, it goes to warehouse B. Except on Fridays.',
            'template_steps' => [
                ['blocked', 'Arrival times do not change the warehouse'],
                ['blocked', 'No exception by day of the week'],
                ['workaround', 'Workaround: someone checks the orders by hand every evening'],
                ['workaround', 'Workaround: an Excel sheet with the customers to keep an eye on'],
            ],
            'custom_steps' => [
                ['supported', 'Time and open orders read in real time'],
                ['supported', 'Automatic routing to warehouse B'],
                ['supported', 'On Fridays the rule switches itself off'],
                ['supported', 'Zero manual checks'],
            ],
        ],
        [
            'title' => 'Loyalty-only discounts',
            'rule' => 'The discount applies only to customers with us for more than 5 years, and only on the lines chosen by the local sales rep.',
            'template_steps' => [
                ['blocked', 'The discount is the same for everyone or per category'],
                ['blocked', 'No link between sales rep and product lines'],
                ['workaround', 'Workaround: the discount is applied by hand on the invoice'],
                ['workaround', 'Workaround: a list of long-time customers on a shared sheet'],
            ],
            'custom_steps' => [
                ['supported', 'Customer seniority calculated automatically'],
                ['supported', 'Each sales rep manages their own lines'],
                ['supported', 'The right discount already shows up in the quote'],
                ['supported', 'No mistakes on the invoice'],
            ],
        ],
        [
            'title' => 'Invoice to the accountant',
            'rule' => 'Some customers want the invoice sent to their accountant, not to them. But the payment reminder goes to the customer.',
            'template_steps' => [
                ['blocked', 'A customer has only one sending address'],
                ['blocked', 'Invoice and reminder always go to the same contact'],
                ['workaround', 'Workaround: every invoice is forwarded by hand'],
                ['workaround', 'Workaround: a red note on the customer record, hoping someone reads it'],
            ],
            'custom_steps' => [
                ['supported', 'Different recipients for invoice and reminder'],
                ['supported', 'Accountant linked to the customer record'],
                ['supported', 'Right recipients, every time, automatically'],
                ['supported', 'No note to remember'],
            ],
        ],
    ];

    $modules = [
        ['Orders', '36', 'to ship today', 'rows'],
        ['Warehouse', '4', 'items below stock', 'bar'],
        ['Customers', '412', 'active customers', 'dots'],
        ['Invoices', '12', 'ready to issue', 'rows'],
        ['Reports', '+8%', 'compared to last month', 'chart'],
        ['Mobile app', 'Online', 'synced with the management system', 'phone'],
        ['Customer portal', '28', 'logins today', 'dots'],
        ['AI assistant', '“Reorder 3 items?”', 'today\'s suggestion', 'ai'],
        ['Digital signature', '5', 'contracts signed', 'rows'],
        ['B2B e-commerce', '9', 'online orders today', 'chart'],
        ['WhatsApp', '31', 'notifications sent', 'bar'],
    ];

    $automations = [
        ['Sales report', 'Every Monday, two hours copying numbers into a sheet.', ['Sales report generated and sent to management', 'Weekly report updated with yesterday\'s orders']],
        ['Deadline reminders', 'You call customers one by one to remind them of deadlines.', ['Reminder sent to Rossi Srl on WhatsApp', 'Deadline notice sent to Studio Neri by email']],
        ['E-commerce orders', 'You copy online orders into the management system by hand.', ['Order #2291 imported, warehouse updated', 'Order #2292 imported, customer notified']],
        ['Quotes and delivery notes', 'You prepare every document starting from a Word template.', ['Quote P-0412 generated and ready to sign', 'Delivery note 118 created and attached to the shipment']],
    ];

    $page_effect_config = [
        'locale' => str_replace('_', '-', app()->getLocale()),
        'rules' => array_map(fn (array $rule): array => [
            'title' => __($rule['title']),
            'rule' => __($rule['rule']),
            'template_steps' => array_map(fn (array $item): array => [$item[0], __($item[1])], $rule['template_steps']),
            'custom_steps' => array_map(fn (array $item): array => [$item[0], __($item[1])], $rule['custom_steps']),
        ], $rules),
        'modules' => array_map(fn (array $module): array => [
            'name' => __($module[0]),
            'value' => __($module[1]),
            'label' => __($module[2]),
            'visualization' => $module[3],
        ], $modules),
        'automations' => array_map(fn (array $automation): array => [
            'name' => __($automation[0]),
            'pain' => __($automation[1]),
            'events' => array_map(fn (string $event): string => __($event), $automation[2]),
        ], $automations),
        'text' => [
            'workarounds' => __('Workarounds needed: :count'),
            'rule_stays_outside' => __('and the rule still stays outside the system'),
            'waiting' => __('pending'),
            'running' => __('running'),
            'analysing' => __('analysing'),
            'passed' => __('passed'),
            'clean' => __('clean'),
            'none' => __('none'),
            'new' => __('Brand new'),
            'modules' => __('Modules: :count · Rewrites needed: 0'),
            'restart' => __('Start over'),
            'add_module' => __('Add a module'),
            'all_manual' => __('Everything still done by hand. Turn on an automation.'),
            'done_automatically' => __('Done automatically. You never think about it again.'),
            'by_hand' => __('By hand: :left of :total'),
            'write_something' => __('Write something.'),
            'email_ok' => __('Valid. It goes into the database.'),
            'email_bad' => __('Rejected. It does not go into the database.'),
            'vat_ok' => __('Eleven digits. Accepted.'),
            'vat_bad' => __('It takes 11 digits, no letters. Rejected.'),
            'report_sent' => __('Report sent to the office ✓'),
            'close_job' => __('Close job'),
        ],
    ];
@endphp

<div>
    <x-page-hero effect="method-mesh" :label="__('How I work: software that does not break.')"
        :rows="[__('Software that'), __('does not break.')]">
        <p>{!! __('My method, in short: interfaces that do not frustrate and results that <em>last over time.</em> Try to break it.') !!}</p>

        <x-slot:actions>
            <a class="button-primary button-large magnetic-hover home-hero-cta" href="{{ route('contacts') }}" wire:navigate data-cursor="{{ __('Write to me') }}"
                data-pan="cta-method-hero"><span>{{ __('Let\'s talk') }}</span><span class="button-arrow">→</span></a>
        </x-slot:actions>
    </x-page-hero>

    <section class="section section--tight method-principles" data-effect="how-i-work">
        <script type="application/json" class="page-effect-config">{!! json_encode($page_effect_config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) !!}</script>

        <div class="method-principle">
            <div>
                <h3 class="reveal">{!! __('Built<br>around you<span class="text-accent">.</span>') !!}</h3>
                <p class="reveal">{{ __('Every company has its strange rules. The ones only you have, that no off-the-shelf management system foresaw. A template forces you into workarounds: separate sheets, reminders, manual steps. Custom software writes them into the code, and from then on applies them by itself.') }}</p>
                <p class="reveal">{{ __('Every project starts from a blank page and a conversation. Your processes drive the software, never the other way around.') }}</p>
            </div>
            <div class="method-demo" id="method-fit-section">
                <span class="eyebrow">{{ __('Pick a rule only your company has') }}</span>
                <div class="method-rules" role="group" aria-label="{{ __('Example rules') }}"></div>
                <div class="method-fit-head"><span class="eyebrow" id="method-fit-rule-title">{{ __('How it handles it') }}</span>
                    <div class="segmented-toggle"><button type="button" class="is-active" data-fit-mode="template">{{ __('Template') }}</button><button
                            type="button" data-fit-mode="custom">{{ __('Custom') }}</button></div>
                </div>
                <ol class="method-flow" id="method-flow-list" aria-live="polite"></ol>
                <p class="method-fit-score" id="method-fit-score-line"></p>
            </div>
        </div>

        <div class="method-principle">
            <div>
                <h3 class="reveal">{!! __('It works.<br><em style="font-style: italic; font-weight: 400; font-stretch: 82%">It keeps working. Safely.</em>') !!}</h3>
                <p class="reveal">{{ __('Automated tests run on every change and flag problems before they reach you. I do not hope it works: I verify it.') }}</p>
                <p class="reveal">{!! __('<b style="color: var(--site-color-text)">And it has to be secure.</b> In the AI era, generated code and external dependencies widen the attack surface. That is why I run in-depth security audits: an SBOM for every project, dependency audits straight from the package managers, no known vulnerability let through to production.') !!}</p>
            </div>
            <div class="method-demo" id="method-tests-section">
                <span class="eyebrow">{{ __('Before every release · Quality') }}</span>
                @foreach (['Unit tests', 'End-to-end tests', 'Static analysis', 'Code coverage and mutation testing'] as $check)
                    <div class="method-check"><i></i><span>{{ __($check) }}</span><b>{{ __('pending') }}</b></div>
                @endforeach
                <span class="eyebrow" style="margin-top: 10px; color: var(--site-color-accent)">{{ __('Security') }}</span>
                @foreach (['Dependency audit · Composer, npm', 'SBOM generated and verified', 'Known vulnerabilities'] as $check)
                    <div class="method-check section"><i></i><span>{{ __($check) }}</span><b>{{ __('pending') }}</b></div>
                @endforeach
                <div class="method-meter"><i></i></div>
                <button type="button" class="button-outline-link" id="rerun-tests-button" style="align-self: flex-start">{{ __('Run the tests again') }}
                    <span>↻</span></button>
            </div>
        </div>

        <div class="method-principle">
            <div>
                <h3 class="reveal">{!! __('Grows<br>with you<span class="text-accent">.</span>') !!}</h3>
                <p class="reveal">{{ __('Flexible, extensible data structures: the software expands as your business grows, without rewrites. And I stay by your side as a technical partner.') }}</p>
            </div>
            <div class="method-demo">
                <span class="eyebrow">{{ __('Your company grows. So does the software.') }}</span>
                <div class="method-app" aria-live="polite">
                    <div class="method-app-top"><i></i><i></i><i></i><span>{{ __('Your management system') }}</span></div>
                    <div class="method-app-body">
                        <ul class="method-app-nav" id="method-app-nav"></ul>
                        <div class="method-app-grid" id="method-app-grid"></div>
                    </div>
                </div>
                <div class="method-app-foot"><button type="button" class="button-primary magnetic-hover" id="add-module-button"><span>{{ __('Add a module') }}</span><span
                            class="button-arrow">+</span></button><span class="eyebrow" id="module-count"></span></div>
            </div>
        </div>

        <div class="method-principle">
            <div>
                <h3 class="reveal">{!! __('I automate<br>repetition<span class="text-accent">.</span>') !!}</h3>
                <p class="reveal">{{ __('Reports, syncs, documents, notifications: everything that repeats I spot and take off your hands.') }}</p>
            </div>
            <div class="method-demo">
                <span class="eyebrow">{{ __('Today\'s problem. Turn it on, and it disappears.') }}</span>
                <div class="method-autos" id="automation-list"></div>
                <div class="method-auto-log">
                    <div class="method-auto-log-head"><span class="eyebrow">{{ __('Today\'s log') }}</span><span class="eyebrow" id="manual-counter"></span></div>
                    <ul id="automation-log"></ul>
                </div>
            </div>
        </div>

        <div class="method-principle">
            <div>
                <h3 class="reveal">{!! __('Data<br>always intact<span class="text-accent">.</span>') !!}</h3>
                <p class="reveal">{{ __('Every piece of data is validated, cleaned and verified before it gets in. That way the database, the reports and your decisions stay reliable.') }}</p>
            </div>
            <div class="method-demo">
                <div class="method-field" id="field-email"><label for="validation-email">{{ __('Try getting an email wrong') }}</label><input
                        id="validation-email" type="text" placeholder="{{ __('name@company') }}" autocomplete="off"><span
                        class="method-field-message">{{ __('Write something.') }}</span></div>
                <div class="method-field" id="field-vat-number"><label for="validation-vat-number">{{ __('And a VAT number') }}</label><input id="validation-vat-number"
                        type="text" inputmode="numeric" placeholder="{{ __('11 digits') }}" autocomplete="off"><span
                        class="method-field-message">{{ __('Write something.') }}</span></div>
            </div>
        </div>

        <div class="method-principle">
            <div>
                <h3 class="reveal">{!! __('Interfaces<br>people like<span class="text-accent">.</span>') !!}</h3>
                <p class="reveal">{{ __('Clean, modern, intuitive. Your team learns them in minutes, not weeks. And likes using them.') }}</p>
                <p class="reveal">{{ __('With custom software the interface is not a table with fields: it is designed around the gesture you make every day. Dragging a job, signing on the phone, configuring a product. Try them here.') }}</p>
            </div>
            <div class="method-demo method-ui">
                <div class="method-ui-tabs" role="tablist" aria-label="{{ __('Interface examples') }}">
                    <button type="button" role="tab" class="is-active" data-tab-index="0">{{ __('Planning') }}</button>
                    <button type="button" role="tab" data-tab-index="1">{{ __('Field app') }}</button>
                    <button type="button" role="tab" data-tab-index="2">{{ __('Configurator') }}</button>
                </div>
                <div class="method-ui-stage">
                    <div class="method-ui-pane is-active" data-pane-index="0">
                        <p class="method-ui-hint">{{ __('Drag a job into the right column. Or tap it to move it forward.') }}</p>
                        <div class="method-kanban">
                            <div class="method-kanban-column" data-column-index="0"><span class="method-kanban-column-title">{{ __('To do') }}</span>
                                <div class="method-kanban-card" draggable="true"><b>#1043 Studio Riva</b><small>{{ __('Shelving · 3 days') }}</small></div>
                                <div class="method-kanban-card" draggable="true"><b>#1044 Hotel Lago</b><small>{{ __('Lobby furnishing · 6 days') }}</small></div>
                            </div>
                            <div class="method-kanban-column" data-column-index="1"><span class="method-kanban-column-title">{{ __('In progress') }}</span>
                                <div class="method-kanban-card" draggable="true"><b>#1042 Bottega Sardi</b><small>{{ __('Counter · today') }}</small></div>
                            </div>
                            <div class="method-kanban-column" data-column-index="2"><span class="method-kanban-column-title">{{ __('Ready') }}</span>
                                <div class="method-kanban-card is-done" draggable="true"><b>#1041 Caffè Centrale</b><small>{{ __('Delivered') }}</small></div>
                            </div>
                        </div>
                    </div>
                    <div class="method-ui-pane" data-pane-index="1">
                        <div class="method-phone">
                            <div class="method-phone-top"><b>{{ __('Job #318') }}</b><small>{{ __('Boiler · Rossi, 12 Garibaldi St') }}</small></div>
                            <div class="method-phone-list">
                                @foreach (['Pressure check', 'Burner cleaning', 'Flue gas test'] as $step)
                                    <button type="button" class="method-phone-check"><i></i>{{ __($step) }}</button>
                                @endforeach
                            </div>
                            <span class="method-phone-label">{{ __('Customer signature') }}</span>
                            <canvas class="method-phone-signature" width="460" height="120" aria-label="{{ __('Signature area') }}"></canvas>
                            <button type="button" class="method-phone-send" disabled>{{ __('Close job') }}</button>
                        </div>
                        <p class="method-ui-hint">{{ __('Tick the checks and sign with your finger or mouse. The report goes to the office by itself.') }}</p>
                    </div>
                    <div class="method-ui-pane" data-pane-index="2">
                        <div class="method-config">
                            <div class="method-config-preview">
                                <div class="method-config-object" id="configurator-object"></div><span class="method-config-dimensions" id="configurator-dimensions">160 cm</span>
                            </div>
                            <div class="method-config-options">
                                <div><span class="eyebrow">{{ __('Material') }}</span>
                                    <div class="method-config-row" data-option-group="material"><button type="button" class="is-active" data-option-value="rovere">{{ __('Oak') }}</button><button type="button" data-option-value="noce">{{ __('Walnut') }}</button><button type="button" data-option-value="nero">{{ __('Matte black') }}</button></div>
                                </div>
                                <div><span class="eyebrow">{{ __('Length') }}</span>
                                    <div class="method-config-row" data-option-group="length"><button type="button" data-option-value="120">120</button><button type="button" class="is-active" data-option-value="160">160</button><button type="button" data-option-value="200">200</button></div>
                                </div>
                                <div><span class="eyebrow">{{ __('Delivery') }}</span>
                                    <div class="method-config-row" data-option-group="delivery"><button type="button" class="is-active" data-option-value="std">{{ __('Standard') }}</button><button type="button" data-option-value="exp">{{ __('Express') }}</button></div>
                                </div>
                                <p class="method-config-price">{{ __('Price') }} <b id="configurator-price">€ 1,240</b><small>{{ __('Example prices') }}</small></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="method-principle">
            <div>
                <h3 class="reveal">{!! __('AI-assisted.<br><em style="font-style: italic; font-weight: 400; font-stretch: 82%">Never AI-dependent.</em>') !!}</h3>
                <p class="reveal">{{ __('I use specialized AI tools to write better code. But the judgment stays human: every line is read, understood and verified before it gets to you.') }}</p>
            </div>
            <div class="method-demo"><span class="eyebrow">{{ __('Mature, documented technologies with active communities') }}</span>
                <div class="tech-cloud" id="tech-cloud-canvas" data-effect="tech-cloud">
                    @foreach (['Laravel', 'Livewire', 'Filament', 'Tailwind', 'Vue', 'Docker', 'TypeScript', 'PHP', 'Sentry'] as $tech)
                        <span>{{ $tech }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="section scroll-lit">
        <p data-accent-pattern="{{ __('really\\.') }}">{{ __('You do not need faster software. You need software that really works.') }}</p>
        <div class="button-group" style="justify-content: flex-start; margin-top: 40px">
            <a class="button-primary button-large magnetic-hover" href="{{ route('contacts') }}" wire:navigate
                data-pan="cta-method-contacts"><span>{{ __('Let us talk') }}</span><span class="button-arrow">→</span></a>
            <a class="button-outline-link" href="{{ route('services') }}" wire:navigate data-pan="cta-method-services">{{ __('Explore services') }}
                <span>→</span></a>
            <a class="button-outline-link" href="{{ route('projects') }}" wire:navigate data-pan="cta-method-projects">{{ __('See works') }}
                <span>→</span></a>
        </div>
    </section>

    <x-contact-band />
</div>
