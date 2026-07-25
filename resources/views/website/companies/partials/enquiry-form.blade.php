<form method="POST" action="{{ route('website.companies.enquiries.store', $company->slug) }}" class="ly-cp-form" id="company-enquiry-form">
    @csrf
    <input type="hidden" name="_form" value="enquiry">

    @if(session('success'))
        <div class="ly-flash-success" style="position:static;top:auto;border:1px solid #a7f3d0;border-radius:12px;margin:0 0 4px;text-align:left;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any() && (old('_form') === 'enquiry' || $errors->hasAny(['name','email','phone','subject','message'])))
        <div class="ly-auth-error" style="margin:0;">{{ $errors->first() }}</div>
    @endif

    <label>
        <span>Name</span>
        <input type="text" name="name" value="{{ old('name', $authUser?->name) }}" required autocomplete="name" placeholder="Your full name">
    </label>
    <label>
        <span>Email</span>
        <input type="email" name="email" value="{{ old('email', $authUser?->email) }}" required autocomplete="email" placeholder="you@example.com">
    </label>
    <label>
        <span>Phone</span>
        <input type="text" name="phone" value="{{ old('phone', $authUser?->phone) }}" autocomplete="tel" placeholder="Optional">
    </label>
    <label>
        <span>Subject</span>
        <input type="text" name="subject" value="{{ old('subject', 'Course enquiry') }}" placeholder="Course enquiry">
    </label>
    <label>
        <span>Message</span>
        <textarea name="message" rows="4" required placeholder="Tell us what you need help with">{{ old('message') }}</textarea>
    </label>
    <button type="submit" class="ly-btn ly-btn-green ly-cp-full-btn">Send enquiry</button>
</form>
