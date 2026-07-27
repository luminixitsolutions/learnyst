<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    use ScopesToCurrentUser;

    public function index()
    {
        $contacts = $this->owned(Contact::query())->latest()->get();

        return view('admin.contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('admin.contacts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'organization' => ['nullable', 'string', 'max:255'],
            'contact_type' => ['required', Rule::in(['lead', 'customer', 'partner', 'vendor'])],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->boolean('is_active', true);

        $contact = Contact::create($validated);

        ActivityLogger::log('contact_created', "Contact {$contact->name} created", $contact);

        return redirect()
            ->route('admin.contacts.index')
            ->with('success', 'Contact created successfully.');
    }

    public function edit(Contact $contact)
    {
        $this->authorizeOwner($contact);

        return view('admin.contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $this->authorizeOwner($contact);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'organization' => ['nullable', 'string', 'max:255'],
            'contact_type' => ['required', Rule::in(['lead', 'customer', 'partner', 'vendor'])],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $contact->update($validated);

        ActivityLogger::log('contact_updated', "Contact {$contact->name} updated", $contact);

        return redirect()
            ->route('admin.contacts.index')
            ->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        $this->authorizeOwner($contact);

        $name = $contact->name;
        $contact->delete();

        ActivityLogger::log('contact_deleted', "Contact {$name} deleted");

        return redirect()
            ->route('admin.contacts.index')
            ->with('success', 'Contact deleted.');
    }
}
