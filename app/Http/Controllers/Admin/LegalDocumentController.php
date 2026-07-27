<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\LegalDocument;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LegalDocumentController extends Controller
{
    use ScopesToCurrentUser;

    public function index()
    {
        $legalDocuments = $this->owned(LegalDocument::query())->latest()->get();

        return view('admin.legal-documents.index', compact('legalDocuments'));
    }

    public function create()
    {
        return view('admin.legal-documents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'document_type' => ['required', Rule::in(['privacy_policy', 'terms_of_service', 'refund_policy', 'user_agreement', 'other'])],
            'content' => ['nullable', 'string'],
            'version' => ['nullable', 'string', 'max:20'],
            'effective_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['draft', 'published'])],
        ]);

        $validated['created_by'] = Auth::id();
        $validated['version'] = $validated['version'] ?? '1.0';

        $document = LegalDocument::create($validated);

        ActivityLogger::log('legal_document_created', "Legal document {$document->title} created", $document);

        return redirect()
            ->route('admin.legal-documents.index')
            ->with('success', 'Legal document created successfully.');
    }

    public function edit(LegalDocument $legalDocument)
    {
        $this->authorizeOwner($legalDocument);

        return view('admin.legal-documents.edit', compact('legalDocument'));
    }

    public function update(Request $request, LegalDocument $legalDocument)
    {
        $this->authorizeOwner($legalDocument);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'document_type' => ['required', Rule::in(['privacy_policy', 'terms_of_service', 'refund_policy', 'user_agreement', 'other'])],
            'content' => ['nullable', 'string'],
            'version' => ['nullable', 'string', 'max:20'],
            'effective_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['draft', 'published'])],
        ]);

        $validated['version'] = $validated['version'] ?? '1.0';

        $legalDocument->update($validated);

        ActivityLogger::log('legal_document_updated', "Legal document {$legalDocument->title} updated", $legalDocument);

        return redirect()
            ->route('admin.legal-documents.index')
            ->with('success', 'Legal document updated successfully.');
    }

    public function destroy(LegalDocument $legalDocument)
    {
        $this->authorizeOwner($legalDocument);

        $title = $legalDocument->title;
        $legalDocument->delete();

        ActivityLogger::log('legal_document_deleted', "Legal document {$title} deleted");

        return redirect()
            ->route('admin.legal-documents.index')
            ->with('success', 'Legal document deleted.');
    }
}
