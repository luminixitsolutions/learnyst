<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use App\Models\LandingPageEvent;
use App\Models\Lead;
use App\Models\Webinar;
use App\Models\WebinarRegistration;
use App\Services\AutomationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PublicMarketingController extends Controller
{
    public function __construct(protected AutomationService $automations) {}

    public function landingPage(string $slug)
    {
        $page = LandingPage::where('slug', $slug)->where('is_published', true)->firstOrFail();
        $page->increment('views');
        LandingPageEvent::create([
            'landing_page_id' => $page->id,
            'event_type' => 'view',
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
        ]);

        return view('public.landing-page', compact('page'));
    }

    public function landingCta(string $slug)
    {
        $page = LandingPage::where('slug', $slug)->where('is_published', true)->firstOrFail();
        $page->increment('cta_clicks');
        LandingPageEvent::create([
            'landing_page_id' => $page->id,
            'event_type' => 'cta_click',
            'ip_address' => request()->ip(),
        ]);

        return redirect()->away($page->cta_url ?: url('/'));
    }

    public function landingLead(Request $request, string $slug)
    {
        $page = LandingPage::where('slug', $slug)->where('is_published', true)->firstOrFail();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:40'],
        ]);

        $lead = Lead::create([
            'created_by' => $page->created_by,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'source' => 'landing:'.$page->slug,
            'status' => 'new',
            'stage' => 'new',
        ]);

        $page->increment('leads_captured');
        LandingPageEvent::create([
            'landing_page_id' => $page->id,
            'event_type' => 'lead',
            'meta' => ['lead_id' => $lead->id],
        ]);

        $this->automations->dispatch('signup', $page->created_by, $lead, [
            'email' => $lead->email,
            'phone' => $lead->phone,
            'name' => $lead->name,
        ]);

        return back()->with('success', 'Thanks! We will contact you soon.');
    }

    public function webinarRegisterForm(Webinar $webinar)
    {
        abort_unless($webinar->registration_enabled && $webinar->status === 'published', 404);

        return view('public.webinar-register', compact('webinar'));
    }

    public function webinarRegister(Request $request, Webinar $webinar)
    {
        abort_unless($webinar->registration_enabled, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:40'],
        ]);

        $registration = WebinarRegistration::updateOrCreate(
            ['webinar_id' => $webinar->id, 'email' => $validated['email']],
            [
                'created_by' => $webinar->created_by,
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? null,
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]
        );

        $lead = Lead::create([
            'created_by' => $webinar->created_by,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'source' => 'webinar:'.$webinar->slug,
            'status' => 'new',
            'stage' => 'new',
            'notes' => 'Registered for webinar: '.$webinar->title,
        ]);

        $registration->update(['lead_id' => $lead->id]);

        $message = $webinar->confirmation_message
            ?: "You are registered for {$webinar->title}. ".($webinar->meeting_url ?: '');

        try {
            Mail::raw($message, function ($mail) use ($validated, $webinar) {
                $mail->to($validated['email'])->subject('Confirmed: '.$webinar->title);
            });
        } catch (\Throwable) {
        }

        $this->automations->dispatch('webinar_registration', $webinar->created_by, $lead, [
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'name' => $validated['name'],
            'webinar_id' => $webinar->id,
        ]);

        return back()->with('success', 'Registration confirmed! Check your email.');
    }
}
