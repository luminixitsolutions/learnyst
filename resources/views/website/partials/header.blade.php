@php
    $products = config('website.products');
    $solutions = config('website.solutions');
    $customers = config('website.customers');
    $resourcesMenu = config('website.resources_menu');
    $comparisons = config('website.comparisons');
    $cta = config('website.cta');
@endphp

<div class="kingster-mobile-header-wrap">
    <div class="kingster-mobile-header kingster-header-background kingster-style-slide kingster-sticky-mobile-navigation" id="kingster-mobile-header">
        <div class="kingster-mobile-header-container kingster-container clearfix">
            <div class="kingster-logo kingster-item-pdlr">
                <div class="kingster-logo-inner">
                    <a href="{{ route('home') }}"><span class="learnyst-brand-text">{{ config('website.brand') }}</span></a>
                </div>
            </div>
            <div class="kingster-mobile-menu-right">
                <div class="kingster-mobile-menu">
                    <a class="kingster-mm-menu-button kingster-mobile-menu-button kingster-mobile-button-hamburger" href="#kingster-mobile-menu"><span></span></a>
                    <div class="kingster-mm-menu-wrap kingster-navigation-font" id="kingster-mobile-menu" data-slide="right">
                        <ul id="menu-main-navigation" class="m-menu">
                            <li class="menu-item {{ request()->routeIs('website.home') ? 'current-menu-item' : '' }}"><a href="{{ route('home') }}">Home</a></li>
                            <li class="menu-item menu-item-has-children"><a href="#">Products</a>
                                <ul class="sub-menu">
                                    <li class="menu-item menu-item-has-children"><a href="#">What can you sell?</a>
                                        <ul class="sub-menu">
                                            @foreach(collect($products)->where('group', 'sell') as $slug => $item)
                                                <li class="menu-item"><a href="{{ route('website.product', $slug) }}">{{ $item['menu'] }}</a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                    <li class="menu-item menu-item-has-children"><a href="#">Online Presence</a>
                                        <ul class="sub-menu">
                                            @foreach(collect($products)->where('group', 'presence') as $slug => $item)
                                                <li class="menu-item"><a href="{{ route('website.product', $slug) }}">{{ $item['menu'] }}</a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                    <li class="menu-item menu-item-has-children"><a href="#">Market Effectively</a>
                                        <ul class="sub-menu">
                                            @foreach(collect($products)->where('group', 'market') as $slug => $item)
                                                <li class="menu-item"><a href="{{ route('website.product', $slug) }}">{{ $item['menu'] }}</a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="menu-item menu-item-has-children"><a href="#">Solutions</a>
                                <ul class="sub-menu">
                                    <li class="menu-item menu-item-has-children"><a href="#">For Test Prep</a>
                                        <ul class="sub-menu">
                                            @foreach(collect($solutions)->where('group', 'test_prep') as $slug => $item)
                                                <li class="menu-item"><a href="{{ route('website.solution', $slug) }}">{{ $item['menu'] }}</a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                    <li class="menu-item menu-item-has-children"><a href="#">For Creators</a>
                                        <ul class="sub-menu">
                                            @foreach(collect($solutions)->where('group', 'creators') as $slug => $item)
                                                <li class="menu-item"><a href="{{ route('website.solution', $slug) }}">{{ $item['menu'] }}</a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="menu-item menu-item-has-children"><a href="#">Customers</a>
                                <ul class="sub-menu">
                                    @foreach($customers as $slug => $item)
                                        <li class="menu-item"><a href="{{ route('website.customer', $slug) }}">{{ $item['menu'] }}</a></li>
                                    @endforeach
                                </ul>
                            </li>
                            <li class="menu-item {{ request()->routeIs('website.companies.*') ? 'current-menu-item' : '' }}">
                                <a href="{{ route('website.companies.index') }}">Institutes</a>
                            </li>
                            <li class="menu-item {{ request()->routeIs('public.courses') || request()->routeIs('public.course') ? 'current-menu-item' : '' }}">
                                <a href="{{ route('public.courses') }}">Courses</a>
                            </li>
                            <li class="menu-item menu-item-has-children"><a href="#">Resources</a>
                                <ul class="sub-menu">
                                    <li class="menu-item menu-item-has-children"><a href="#">Support</a>
                                        <ul class="sub-menu">
                                            @foreach(collect($resourcesMenu)->where('group', 'support') as $slug => $item)
                                                @php
                                                    $href = !empty($item['external']) ? $item['url'] : route($item['route'], $item['param'] ?? []);
                                                @endphp
                                                <li class="menu-item"><a href="{{ $href }}" @if(!empty($item['external'])) target="_blank" rel="noopener" @endif>{{ $item['menu'] }}</a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                    <li class="menu-item menu-item-has-children"><a href="#">Resources</a>
                                        <ul class="sub-menu">
                                            @foreach(collect($resourcesMenu)->where('group', 'resources') as $slug => $item)
                                                @php
                                                    $href = !empty($item['external']) ? $item['url'] : route($item['route'], $item['param'] ?? []);
                                                @endphp
                                                <li class="menu-item"><a href="{{ $href }}" @if(!empty($item['external'])) target="_blank" rel="noopener" @endif>{{ $item['menu'] }}</a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="menu-item"><a href="{{ route('website.pricing') }}">Pricing</a></li>
                            @auth
                                @php $mobileRole = auth()->user()->role?->slug; @endphp
                                @if($mobileRole === 'learner')
                                    <li class="menu-item"><a href="{{ route('learner.dashboard') }}">Student Dashboard</a></li>
                                    <li class="menu-item"><a href="{{ route('profile.edit') }}">My Profile</a></li>
                                @elseif(in_array($mobileRole, ['admin', 'sub-admin'], true))
                                    <li class="menu-item"><a href="{{ route('admin.dashboard') }}">Institute Dashboard</a></li>
                                    <li class="menu-item"><a href="{{ route('profile.edit') }}">My Profile</a></li>
                                @else
                                    <li class="menu-item"><a href="{{ route('profile.edit') }}">My Profile</a></li>
                                @endif
                            @else
                                <li class="menu-item menu-item-has-children">
                                    <a href="#">Login</a>
                                    <ul class="sub-menu">
                                        <li class="menu-item"><a href="{{ route('student.login') }}">Student Login</a></li>
                                        <li class="menu-item"><a href="{{ route('login') }}">Institute Login</a></li>
                                    </ul>
                                </li>
                                <li class="menu-item menu-item-has-children">
                                    <a href="#">Register</a>
                                    <ul class="sub-menu">
                                        <li class="menu-item"><a href="{{ route('student.register') }}">Student Register</a></li>
                                        <li class="menu-item"><a href="{{ route('signup.show') }}">Institute Register</a></li>
                                    </ul>
                                </li>
                            @endauth
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
