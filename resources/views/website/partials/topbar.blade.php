@php
    $cta = config('website.cta');
    $authUser = auth()->user();
@endphp
<div class="kingster-top-bar learnyst-top-bar">
    <div class="kingster-top-bar-background"></div>
    <div class="kingster-top-bar-container kingster-container">
        <div class="kingster-top-bar-container-inner clearfix learnyst-topbar-inner">
            <div class="kingster-top-bar-left kingster-item-pdlr learnyst-topbar-left">
                <a class="learnyst-topbar-contact" href="mailto:{{ config('website.email') }}">
                    <i class="fa fa-envelope-open-o"></i>
                    <span>{{ config('website.email') }}</span>
                </a>
                <span class="learnyst-topbar-dot" aria-hidden="true"></span>
                <a class="learnyst-topbar-contact" href="tel:{{ preg_replace('/\s+/', '', config('website.phone')) }}">
                    <i class="fa fa-phone"></i>
                    <span>{{ config('website.phone') }}</span>
                </a>
            </div>
            <div class="kingster-top-bar-right kingster-item-pdlr learnyst-topbar-right">
                <nav class="learnyst-topbar-nav" aria-label="Quick links">
                    <a href="{{ route('website.page', 'corporate-lms') }}">For Corporate</a>
                    <a href="{{ route('website.page', 'ai') }}">AI Cofounder</a>
                </nav>

                @if($authUser)
                    @php
                        $roleSlug = $authUser->role?->slug;
                        $avatarUrl = $authUser->avatar
                            ? (str_starts_with($authUser->avatar, 'http') ? $authUser->avatar : asset('storage/'.$authUser->avatar))
                            : '';
                        $roleLabel = match ($roleSlug) {
                            'learner' => 'Student',
                            'admin', 'sub-admin' => 'Institute',
                            'super-admin' => 'Platform',
                            'instructor' => 'Instructor',
                            default => 'Account',
                        };
                    @endphp
                    <div class="ly-user-menu" data-ly-user-menu>
                        <button type="button" class="ly-user-menu-toggle" data-ly-user-toggle aria-expanded="false" aria-haspopup="true">
                            <span class="ly-user-avatar">
                                @if($avatarUrl)
                                    <img src="{{ $avatarUrl }}" alt="{{ $authUser->name }}">
                                @else
                                    <i class="fa fa-user"></i>
                                @endif
                            </span>
                            <span class="ly-user-meta">
                                <strong>{{ Str::limit($authUser->name, 22) }}</strong>
                                <em>{{ $roleLabel }}</em>
                            </span>
                            <i class="fa fa-angle-down ly-user-caret"></i>
                        </button>
                        <div class="ly-user-dropdown" data-ly-user-dropdown hidden>
                            @if($roleSlug === 'learner')
                                <a href="{{ route('learner.dashboard') }}"><i class="fa fa-th-large"></i> Student Dashboard</a>
                                <a href="{{ route('learner.courses.index') }}"><i class="fa fa-book"></i> My Courses</a>
                                <a href="{{ route('learner.certificates') }}"><i class="fa fa-certificate"></i> Certificates</a>
                                <a href="{{ route('profile.edit') }}"><i class="fa fa-user"></i> My Profile</a>
                            @elseif(in_array($roleSlug, ['admin', 'sub-admin'], true))
                                <a href="{{ route('admin.dashboard') }}"><i class="fa fa-th-large"></i> Institute Dashboard</a>
                                <a href="{{ route('admin.company-profile.edit') }}"><i class="fa fa-building"></i> Institute Profile</a>
                                <a href="{{ route('admin.courses.index') }}"><i class="fa fa-book"></i> Courses</a>
                                <a href="{{ route('profile.edit') }}"><i class="fa fa-user"></i> My Profile</a>
                            @elseif($roleSlug === 'super-admin')
                                <a href="{{ route('platform.dashboard') }}"><i class="fa fa-th-large"></i> Platform Dashboard</a>
                                <a href="{{ route('profile.edit') }}"><i class="fa fa-user"></i> My Profile</a>
                            @elseif($roleSlug === 'instructor')
                                <a href="{{ route('instructor.dashboard') }}"><i class="fa fa-th-large"></i> Instructor Dashboard</a>
                                <a href="{{ route('profile.edit') }}"><i class="fa fa-user"></i> My Profile</a>
                            @else
                                <a href="{{ route('profile.edit') }}"><i class="fa fa-user"></i> My Profile</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="ly-user-logout">
                                @csrf
                                <button type="submit"><i class="fa fa-sign-out"></i> Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="learnyst-topbar-auth">
                        <div class="ly-login-menu" data-ly-user-menu>
                            <button type="button" class="learnyst-topbar-link ly-login-toggle" data-ly-user-toggle aria-expanded="false" aria-haspopup="true">
                                Login <i class="fa fa-angle-down"></i>
                            </button>
                            <div class="ly-user-dropdown ly-login-dropdown" data-ly-user-dropdown hidden>
                                <a href="{{ route('student.login') }}"><i class="fa fa-graduation-cap"></i> Student Login</a>
                                <a href="{{ route('login') }}"><i class="fa fa-building"></i> Institute Login</a>
                            </div>
                        </div>
                        <div class="ly-login-menu" data-ly-user-menu>
                            <button type="button" class="kingster-top-bar-right-button learnyst-topbar-cta ly-register-toggle" data-ly-user-toggle aria-expanded="false" aria-haspopup="true">
                                Register <i class="fa fa-angle-down"></i>
                            </button>
                            <div class="ly-user-dropdown ly-login-dropdown" data-ly-user-dropdown hidden>
                                <a href="{{ route('student.register') }}"><i class="fa fa-graduation-cap"></i> Student Register</a>
                                <a href="{{ route('signup.show') }}"><i class="fa fa-university"></i> Institute Register</a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
