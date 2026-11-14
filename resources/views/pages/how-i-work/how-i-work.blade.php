@php
    $rules = [
        [
            'k' => 'Orders after 5 pm',
            'rule' => 'If an order arrives after 5 pm and the customer already has 3 open orders, it goes to warehouse B. Except on Fridays.',
            't' => [
                ['x', 'Arrival times do not change the warehouse'],
                ['x', 'No exception by day of the week'],
                ['w', 'Workaround: someone checks the orders by hand every evening'],
                ['w', 'Workaround: an Excel sheet with the customers to keep an eye on'],
            ],
            'c' => [
                ['v', 'Time and open orders read in real time'],
                ['v', 'Automatic routing to warehouse B'],
                ['v', 'On Fridays the rule switches itself off'],
                ['v', 'Zero manual checks'],
            ],
        ],
        [
            'k' => 'Loyalty-only discounts',
            'rule' => 'The discount applies only to customers with us for more than 5 years, and only on the lines chosen by the local sales rep.',
            't' => [
                ['x', 'The discount is the same for everyone or per category'],
                ['x', 'No link between sales rep and product lines'],
                ['w', 'Workaround: the discount is applied by hand on the invoice'],
                ['w', 'Workaround: a list of long-time customers on a shared sheet'],
            ],
            'c' => [
                ['v', 'Customer seniority calculated automatically'],
                ['v', 'Each sales rep manages their own lines'],
                ['v', 'The right discount already shows up in the quote'],
                ['v', 'No mistakes on the invoice'],
            ],
        ],
        [
            'k' => 'Invoice to the accountant',
            'rule' => 'Some customers want the invoice sent to their accountant, not to them. But the payment reminder goes to the customer.',
            't' => [
                ['x', 'A customer has only one sending address'],
                ['x', 'Invoice and reminder always go to the same contact'],
                ['w', 'Workaround: every invoice is forwarded by hand'],
                ['w', 'Workaround: a red note on the customer record, hoping someone reads it'],
            ],
            'c' => [
                ['v', 'Different recipients for invoice and reminder'],
                ['v', 'Accountant linked to the customer record'],
                ['v', 'Right recipients, every time, automatically'],
                ['v', 'No note to remember'],
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

    $fx_config = [
        'locale' => str_replace('_', '-', app()->getLocale()),
        'rules' => array_map(fn (array $rule): array => [
            'k' => __($rule['k']),
            'rule' => __($rule['rule']),
            't' => array_map(fn (array $item): array => [$item[0], __($item[1])], $rule['t']),
            'c' => array_map(fn (array $item): array => [$item[0], __($item[1])], $rule['c']),
        ], $rules),
        'modules' => array_map(fn (array $module): array => [
            'name' => __($module[0]),
            'value' => __($module[1]),
            'label' => __($module[2]),
            'viz' => $module[3],
        ], $modules),
        'automations' => array_map(fn (array $automation): array => [
            'name' => __($automation[0]),
            'pain' => __($automation[1]),
            'ev' => array_map(fn (string $event): string => __($event), $automation[2]),
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
    <x-page-hero fx="mesh" :label="__('How I work: software that does not break.')"
        :rows="[__('Software that'), __('does not break.')]">
        <p>{!! __('My method, in short: interfaces that do not frustrate and results that <em>last over time.</em> Try to break it.') !!}</p>

        <x-slot:actions>
            <a class="cta xl mag hero-cta" href="{{ route('contacts') }}" wire:navigate data-cursor="{{ __('Write to me') }}"
                data-pan="cta-method-hero"><span>{{ __('Let\'s talk') }}</span><span class="ar">→</span></a>
        </x-slot:actions>
    </x-page-hero>

    <section class="sec tight principles" data-fx="how-i-work">
        <script type="application/json" class="fx-config">{!! json_encode($fx_config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) !!}</script>

        <div class="pr">
            <div>
                <h3 class="rv">{!! __('Built<br>around you<span class="acc">.</span>') !!}</h3>
                <p class="rv">{{ __('Every company has its strange rules. The ones only you have, that no off-the-shelf management system foresaw. A template forces you into workarounds: separate sheets, reminders, manual steps. Custom software writes them into the code, and from then on applies them by itself.') }}</p>
                <p class="rv">{{ __('Every project starts from a blank page and a conversation. Your processes drive the software, never the other way around.') }}</p>
            </div>
            <div class="demo" id="fit">
                <span class="label">{{ __('Pick a rule only your company has') }}</span>
                <div class="rules" role="group" aria-label="{{ __('Example rules') }}"></div>
                <div class="fit-head"><span class="label" id="fit-title">{{ __('How it handles it') }}</span>
                    <div class="switch"><button type="button" class="on" data-m="t">{{ __('Template') }}</button><button
                            type="button" data-m="c">{{ __('Custom') }}</button></div>
                </div>
                <ol class="flow" id="flow" aria-live="polite"></ol>
                <p class="fit-score" id="fit-score"></p>
            </div>
        </div>

        <div class="pr">
            <div>
                <h3 class="rv">{!! __('It works.<br><em style="font-style: italic; font-weight: 400; font-stretch: 82%">It keeps working. Safely.</em>') !!}</h3>
                <p class="rv">{{ __('Automated tests run on every change and flag problems before they reach you. I do not hope it works: I verify it.') }}</p>
                <p class="rv">{!! __('<b style="color: var(--ink)">And it has to be secure.</b> In the AI era, generated code and external dependencies widen the attack surface. That is why I run in-depth security audits: an SBOM for every project, dependency audits straight from the package managers, no known vulnerability let through to production.') !!}</p>
            </div>
            <div class="demo" id="tests">
                <span class="label">{{ __('Before every release · Quality') }}</span>
                @foreach (['Unit tests', 'End-to-end tests', 'Static analysis', 'Code coverage and mutation testing'] as $check)
                    <div class="run"><i></i><span>{{ __($check) }}</span><b>{{ __('pending') }}</b></div>
                @endforeach
                <span class="label" style="margin-top: 10px; color: var(--acc)">{{ __('Security') }}</span>
                @foreach (['Dependency audit · Composer, npm', 'SBOM generated and verified', 'Known vulnerabilities'] as $check)
                    <div class="run sec"><i></i><span>{{ __($check) }}</span><b>{{ __('pending') }}</b></div>
                @endforeach
                <div class="meter"><i></i></div>
                <button type="button" class="more" id="rerun" style="align-self: flex-start">{{ __('Run the tests again') }}
                    <span>↻</span></button>
            </div>
        </div>

        <div class="pr">
            <div>
                <h3 class="rv">{!! __('Grows<br>with you<span class="acc">.</span>') !!}</h3>
                <p class="rv">{{ __('Flexible, extensible data structures: the software expands as your business grows, without rewrites. And I stay by your side as a technical partner.') }}</p>
            </div>
            <div class="demo">
                <span class="label">{{ __('Your company grows. So does the software.') }}</span>
                <div class="mini-app" aria-live="polite">
                    <div class="ma-top"><i></i><i></i><i></i><span>{{ __('Your management system') }}</span></div>
                    <div class="ma-body">
                        <ul class="ma-nav" id="ma-nav"></ul>
                        <div class="ma-grid" id="ma-grid"></div>
                    </div>
                </div>
                <div class="ma-foot"><button type="button" class="cta mag" id="addmod"><span>{{ __('Add a module') }}</span><span
                            class="ar">+</span></button><span class="label" id="modcount"></span></div>
            </div>
        </div>

        <div class="pr">
            <div>
                <h3 class="rv">{!! __('I automate<br>repetition<span class="acc">.</span>') !!}</h3>
                <p class="rv">{{ __('Reports, syncs, documents, notifications: everything that repeats I spot and take off your hands.') }}</p>
            </div>
            <div class="demo">
                <span class="label">{{ __('Today\'s problem. Turn it on, and it disappears.') }}</span>
                <div class="autos" id="autos"></div>
                <div class="alog">
                    <div class="alog-h"><span class="label">{{ __('Today\'s log') }}</span><span class="label" id="manual"></span></div>
                    <ul id="alog"></ul>
                </div>
            </div>
        </div>

        <div class="pr">
            <div>
                <h3 class="rv">{!! __('Data<br>always intact<span class="acc">.</span>') !!}</h3>
                <p class="rv">{{ __('Every piece of data is validated, cleaned and verified before it gets in. That way the database, the reports and your decisions stay reliable.') }}</p>
            </div>
            <div class="demo">
                <div class="field" id="f-mail"><label for="v-mail">{{ __('Try getting an email wrong') }}</label><input
                        id="v-mail" type="text" placeholder="{{ __('name@company') }}" autocomplete="off"><span
                        class="msg">{{ __('Write something.') }}</span></div>
                <div class="field" id="f-iva"><label for="v-iva">{{ __('And a VAT number') }}</label><input id="v-iva"
                        type="text" inputmode="numeric" placeholder="{{ __('11 digits') }}" autocomplete="off"><span
                        class="msg">{{ __('Write something.') }}</span></div>
            </div>
        </div>

        <div class="pr">
            <div>
                <h3 class="rv">{!! __('Interfaces<br>people like<span class="acc">.</span>') !!}</h3>
                <p class="rv">{{ __('Clean, modern, intuitive. Your team learns them in minutes, not weeks. And likes using them.') }}</p>
                <p class="rv">{{ __('With custom software the interface is not a table with fields: it is designed around the gesture you make every day. Dragging a job, signing on the phone, configuring a product. Try them here.') }}</p>
            </div>
            <div class="demo uix">
                <div class="uix-tabs" role="tablist" aria-label="{{ __('Interface examples') }}">
                    <button type="button" role="tab" class="on" data-t="0">{{ __('Planning') }}</button>
                    <button type="button" role="tab" data-t="1">{{ __('Field app') }}</button>
                    <button type="button" role="tab" data-t="2">{{ __('Configurator') }}</button>
                </div>
                <div class="uix-stage">
                    <div class="uix-pane on" data-p="0">
                        <p class="uix-hint">{{ __('Drag a job into the right column. Or tap it to move it forward.') }}</p>
                        <div class="kb">
                            <div class="kb-col" data-c="0"><span class="kb-h">{{ __('To do') }}</span>
                                <div class="kb-card" draggable="true"><b>#1043 Studio Riva</b><small>{{ __('Shelving · 3 days') }}</small></div>
                                <div class="kb-card" draggable="true"><b>#1044 Hotel Lago</b><small>{{ __('Lobby furnishing · 6 days') }}</small></div>
                            </div>
                            <div class="kb-col" data-c="1"><span class="kb-h">{{ __('In progress') }}</span>
                                <div class="kb-card" draggable="true"><b>#1042 Bottega Sardi</b><small>{{ __('Counter · today') }}</small></div>
                            </div>
                            <div class="kb-col" data-c="2"><span class="kb-h">{{ __('Ready') }}</span>
                                <div class="kb-card done" draggable="true"><b>#1041 Caffè Centrale</b><small>{{ __('Delivered') }}</small></div>
                            </div>
                        </div>
                    </div>
                    <div class="uix-pane" data-p="1">
                        <div class="phone">
                            <div class="ph-top"><b>{{ __('Job #318') }}</b><small>{{ __('Boiler · Rossi, 12 Garibaldi St') }}</small></div>
                            <div class="ph-list">
                                @foreach (['Pressure check', 'Burner cleaning', 'Flue gas test'] as $step)
                                    <button type="button" class="chk"><i></i>{{ __($step) }}</button>
                                @endforeach
                            </div>
                            <span class="ph-l">{{ __('Customer signature') }}</span>
                            <canvas class="ph-sign" width="460" height="120" aria-label="{{ __('Signature area') }}"></canvas>
                            <button type="button" class="ph-send" disabled>{{ __('Close job') }}</button>
                        </div>
                        <p class="uix-hint">{{ __('Tick the checks and sign with your finger or mouse. The report goes to the office by itself.') }}</p>
                    </div>
                    <div class="uix-pane" data-p="2">
                        <div class="cfg">
                            <div class="cfg-prev">
                                <div class="cfg-obj" id="cfg-obj"></div><span class="cfg-dim" id="cfg-dim">160 cm</span>
                            </div>
                            <div class="cfg-opts">
                                <div><span class="label">{{ __('Material') }}</span>
                                    <div class="cfg-row" data-k="mat"><button type="button" class="on" data-v="rovere">{{ __('Oak') }}</button><button type="button" data-v="noce">{{ __('Walnut') }}</button><button type="button" data-v="nero">{{ __('Matte black') }}</button></div>
                                </div>
                                <div><span class="label">{{ __('Length') }}</span>
                                    <div class="cfg-row" data-k="len"><button type="button" data-v="120">120</button><button type="button" class="on" data-v="160">160</button><button type="button" data-v="200">200</button></div>
                                </div>
                                <div><span class="label">{{ __('Delivery') }}</span>
                                    <div class="cfg-row" data-k="del"><button type="button" class="on" data-v="std">{{ __('Standard') }}</button><button type="button" data-v="exp">{{ __('Express') }}</button></div>
                                </div>
                                <p class="cfg-price">{{ __('Price') }} <b id="cfg-price">€ 1,240</b><small>{{ __('Example prices') }}</small></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pr">
            <div>
                <h3 class="rv">{!! __('AI-assisted.<br><em style="font-style: italic; font-weight: 400; font-stretch: 82%">Never AI-dependent.</em>') !!}</h3>
                <p class="rv">{{ __('I use specialized AI tools to write better code. But the judgment stays human: every line is read, understood and verified before it gets to you.') }}</p>
            </div>
            <div class="demo"><span class="label">{{ __('Mature, documented technologies with active communities') }}</span>
                <div class="stack-cloud" id="stack" data-fx="stack">
                    @foreach (['Laravel', 'Livewire', 'Filament', 'Tailwind', 'Vue', 'Docker', 'TypeScript', 'PHP', 'Sentry'] as $tech)
                        <span>{{ $tech }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="sec scrub">
        <p data-acc="{{ __('really\\.') }}">{{ __('You do not need faster software. You need software that really works.') }}</p>
        <div class="actions" style="justify-content: flex-start; margin-top: 40px">
            <a class="cta xl mag" href="{{ route('contacts') }}" wire:navigate
                data-pan="cta-method-contacts"><span>{{ __('Let us talk') }}</span><span class="ar">→</span></a>
            <a class="more" href="{{ route('services') }}" wire:navigate data-pan="cta-method-services">{{ __('Explore services') }}
                <span>→</span></a>
            <a class="more" href="{{ route('projects') }}" wire:navigate data-pan="cta-method-projects">{{ __('See works') }}
                <span>→</span></a>
        </div>
    </section>

    <x-contact-band />
</div>
