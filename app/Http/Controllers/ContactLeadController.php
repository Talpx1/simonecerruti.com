<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ContactLead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactLeadController extends Controller {
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                Rule::email()
                    ->rfcCompliant(strict: false)
                    ->validateMxRecord()
                    ->preventSpoofing(),
                'max:255',
            ],
            'phone' => ['nullable', 'numeric'],
            'message' => ['required', 'string'],
        ]);

        ContactLead::create($validated);

        return back();
    }
}
