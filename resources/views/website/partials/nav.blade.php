@php
    $products = config('website.products');
    $solutions = config('website.solutions');
    $customers = config('website.customers');
    $resourcesMenu = config('website.resources_menu');
    $comparisons = config('website.comparisons');
@endphp

<header class="kingster-header-wrap kingster-header-style-plain kingster-style-menu-right kingster-sticky-navigation kingster-style-fixed" data-navigation-offset="75px">
    <div class="kingster-header-background"></div>
    <div class="kingster-header-container kingster-container">
        <div class="kingster-header-container-inner clearfix">
            <div class="kingster-logo kingster-item-pdlr">
                <div class="kingster-logo-inner">
                    <a href="{{ route('home') }}"><span class="learnyst-brand-text">{{ config('website.brand') }}</span></a>
                </div>
            </div>
            <div class="kingster-navigation kingster-item-pdlr clearfix">
                <div class="kingster-main-menu" id="kingster-main-menu">
                    <ul id="menu-main-navigation-1" class="sf-menu">
                        <li class="menu-item menu-item-home {{ request()->routeIs('website.home') ? 'current-menu-item' : '' }} kingster-normal-menu">
                            <a href="{{ route('home') }}">Home</a>
                        </li>

                        <li class="menu-item menu-item-has-children {{ request()->routeIs('website.product') ? 'current-menu-item' : '' }} kingster-mega-menu learnyst-products-menu">
                            <a href="#" class="sf-with-ul-pre">Products</a>
                            <div class="sf-mega sf-mega-full learnyst-solutions-mega">
                                <div class="learnyst-mega-panel">
                                    <div class="learnyst-mega-section">
                                        <h4 class="learnyst-mega-heading">What can you sell?</h4>
                                        <div class="learnyst-mega-grid">
                                            @foreach(collect($products)->where('group', 'sell') as $slug => $item)
                                                <a class="learnyst-mega-item" href="{{ route('website.product', $slug) }}">
                                                    <span class="learnyst-mega-icon"><i class="fa {{ $item['icon'] }}"></i></span>
                                                    <span class="learnyst-mega-copy">
                                                        <strong>{{ $item['menu'] }}</strong>
                                                        <em>{{ $item['menu_desc'] }}</em>
                                                    </span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="learnyst-mega-section">
                                        <h4 class="learnyst-mega-heading">How can you enhance your online presence</h4>
                                        <div class="learnyst-mega-grid">
                                            @foreach(collect($products)->where('group', 'presence') as $slug => $item)
                                                <a class="learnyst-mega-item" href="{{ route('website.product', $slug) }}">
                                                    <span class="learnyst-mega-icon"><i class="fa {{ $item['icon'] }}"></i></span>
                                                    <span class="learnyst-mega-copy">
                                                        <strong>{{ $item['menu'] }}</strong>
                                                        <em>{{ $item['menu_desc'] }}</em>
                                                    </span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="learnyst-mega-section">
                                        <h4 class="learnyst-mega-heading">How can you market effectively?</h4>
                                        <div class="learnyst-mega-grid">
                                            @foreach(collect($products)->where('group', 'market') as $slug => $item)
                                                <a class="learnyst-mega-item" href="{{ route('website.product', $slug) }}">
                                                    <span class="learnyst-mega-icon"><i class="fa {{ $item['icon'] }}"></i></span>
                                                    <span class="learnyst-mega-copy">
                                                        <strong>{{ $item['menu'] }}</strong>
                                                        <em>{{ $item['menu_desc'] }}</em>
                                                    </span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="menu-item menu-item-has-children {{ request()->routeIs('website.solution') ? 'current-menu-item' : '' }} kingster-mega-menu learnyst-solutions-menu">
                            <a href="#" class="sf-with-ul-pre">Solutions</a>
                            <div class="sf-mega sf-mega-full learnyst-solutions-mega">
                                <div class="learnyst-mega-panel">
                                    <div class="learnyst-mega-section">
                                        <h4 class="learnyst-mega-heading">For Test Prep</h4>
                                        <div class="learnyst-mega-grid">
                                            @foreach(collect($solutions)->where('group', 'test_prep') as $slug => $item)
                                                <a class="learnyst-mega-item" href="{{ route('website.solution', $slug) }}">
                                                    <span class="learnyst-mega-icon"><i class="fa {{ $item['icon'] }}"></i></span>
                                                    <span class="learnyst-mega-copy">
                                                        <strong>{{ $item['menu'] }}</strong>
                                                        <em>{{ $item['menu_desc'] }}</em>
                                                    </span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="learnyst-mega-section">
                                        <h4 class="learnyst-mega-heading">For Creators</h4>
                                        <div class="learnyst-mega-grid">
                                            @foreach(collect($solutions)->where('group', 'creators') as $slug => $item)
                                                <a class="learnyst-mega-item" href="{{ route('website.solution', $slug) }}">
                                                    <span class="learnyst-mega-icon"><i class="fa {{ $item['icon'] }}"></i></span>
                                                    <span class="learnyst-mega-copy">
                                                        <strong>{{ $item['menu'] }}</strong>
                                                        <em>{{ $item['menu_desc'] }}</em>
                                                    </span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="menu-item menu-item-has-children {{ request()->routeIs('website.customer') ? 'current-menu-item' : '' }} kingster-mega-menu learnyst-customers-menu">
                            <a href="#" class="sf-with-ul-pre">Customers</a>
                            <div class="sf-mega sf-mega-full learnyst-solutions-mega">
                                <div class="learnyst-mega-panel">
                                    <div class="learnyst-mega-section">
                                        <h4 class="learnyst-mega-heading">Customers</h4>
                                        <div class="learnyst-mega-grid learnyst-mega-grid-3">
                                            @foreach($customers as $slug => $item)
                                                <a class="learnyst-mega-item" href="{{ route('website.customer', $slug) }}">
                                                    <span class="learnyst-mega-icon"><i class="fa {{ $item['icon'] }}"></i></span>
                                                    <span class="learnyst-mega-copy">
                                                        <strong>{{ $item['menu'] }}</strong>
                                                        <em>{{ $item['menu_desc'] }}</em>
                                                    </span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="menu-item {{ request()->routeIs('website.companies.*') ? 'current-menu-item' : '' }} kingster-normal-menu">
                            <a href="{{ route('website.companies.index') }}">Institutes</a>
                        </li>

                        <li class="menu-item {{ request()->routeIs('public.courses') || request()->routeIs('public.course') ? 'current-menu-item' : '' }} kingster-normal-menu">
                            <a href="{{ route('public.courses') }}">Courses</a>
                        </li>

                        <li class="menu-item menu-item-has-children {{ request()->routeIs('website.page') || request()->routeIs('website.comparison') ? 'current-menu-item' : '' }} kingster-mega-menu learnyst-resources-menu">
                            <a href="#" class="sf-with-ul-pre">Resources</a>
                            <div class="sf-mega sf-mega-full learnyst-solutions-mega">
                                <div class="learnyst-mega-panel">
                                    <div class="learnyst-mega-section">
                                        <h4 class="learnyst-mega-heading">Support</h4>
                                        <div class="learnyst-mega-grid learnyst-mega-grid-3">
                                            @foreach(collect($resourcesMenu)->where('group', 'support') as $slug => $item)
                                                @php
                                                    $href = !empty($item['external']) ? $item['url'] : route($item['route'], $item['param'] ?? []);
                                                    $target = !empty($item['external']) ? ' target="_blank" rel="noopener"' : '';
                                                @endphp
                                                <a class="learnyst-mega-item" href="{{ $href }}"{!! $target !!}>
                                                    <span class="learnyst-mega-icon"><i class="fa {{ $item['icon'] }}"></i></span>
                                                    <span class="learnyst-mega-copy">
                                                        <strong>{{ $item['menu'] }}</strong>
                                                        <em>{{ $item['menu_desc'] }}</em>
                                                    </span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="learnyst-mega-section">
                                        <h4 class="learnyst-mega-heading">Resources</h4>
                                        <div class="learnyst-mega-grid learnyst-mega-grid-3">
                                            @foreach(collect($resourcesMenu)->where('group', 'resources') as $slug => $item)
                                                @php
                                                    $href = !empty($item['external']) ? $item['url'] : route($item['route'], $item['param'] ?? []);
                                                    $target = !empty($item['external']) ? ' target="_blank" rel="noopener"' : '';
                                                @endphp
                                                <a class="learnyst-mega-item" href="{{ $href }}"{!! $target !!}>
                                                    <span class="learnyst-mega-icon"><i class="fa {{ $item['icon'] }}"></i></span>
                                                    <span class="learnyst-mega-copy">
                                                        <strong>{{ $item['menu'] }}</strong>
                                                        <em>{{ $item['menu_desc'] }}</em>
                                                    </span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="menu-item {{ request()->is('pricing') ? 'current-menu-item' : '' }} kingster-normal-menu">
                            <a href="{{ route('website.pricing') }}">Pricing</a>
                        </li>
                    </ul>
                    <div class="kingster-navigation-slide-bar" id="kingster-navigation-slide-bar"></div>
                </div>
            </div>
        </div>
    </div>
</header>
