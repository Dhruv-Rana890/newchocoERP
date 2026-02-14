{{-- Chocolat Theme Header - Professional Hotel Chocolat Style --}}
<header class="chocolat-header sticky top-0 z-50 bg-black text-white shadow-lg" style="background-color: {{ $ecommerce_setting->header_bg_color ?? '#000000' }} !important;">
    {{-- Top Bar --}}
    <div class="border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-2 flex justify-between items-center text-xs sm:text-sm">
            <div class="flex items-center gap-4 sm:gap-6">
                <a href="{{ url('/') }}" class="hover:text-amber-200 transition">{{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Home' }}</a>
                <a href="{{ url('shop') }}" class="hover:text-amber-200 transition">{{ app()->getLocale() == 'ar' ? 'المتجر' : 'Shop' }}</a>
                <a href="{{ url('contact') }}" class="hover:text-amber-200 transition">{{ app()->getLocale() == 'ar' ? 'تواصل' : 'Contact' }}</a>
            </div>
            @if(Route::has('set.locale'))
            <div class="flex gap-2">
                <a href="{{ route('set.locale', 'en') }}" class="hover:underline {{ app()->getLocale() == 'en' ? 'font-bold' : 'opacity-70' }}">EN</a>
                <span class="opacity-50">|</span>
                <a href="{{ route('set.locale', 'ar') }}" class="hover:underline {{ app()->getLocale() == 'ar' ? 'font-bold' : 'opacity-70' }}">AR</a>
            </div>
            @endif
        </div>
    </div>

    {{-- Main Header --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4">
        <div class="flex items-center justify-between gap-4">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex-shrink-0">
                @if(isset($ecommerce_setting->logo) && $ecommerce_setting->logo)
                <img src="{{ url('frontend/images') }}/{{ $ecommerce_setting->logo }}" alt="{{ $ecommerce_setting->site_title ?? 'Store' }}" class="h-10 sm:h-12 object-contain">
                @elseif(isset($general_setting->site_logo) && $general_setting->site_logo)
                <img src="{{ url('logo', $general_setting->site_logo) }}" alt="{{ $ecommerce_setting->site_title ?? 'Store' }}" class="h-10 sm:h-12 object-contain brightness-0 invert">
                @else
                <span class="text-xl font-bold tracking-wider">{{ $ecommerce_setting->site_title ?? 'Store' }}</span>
                @endif
            </a>

            {{-- Search - Desktop --}}
            <form action="{{ route('products.search') }}" method="POST" class="hidden md:block flex-1 max-w-md mx-6">
                @csrf
                <div class="relative">
                    <input type="text" name="search" placeholder="{{ app()->getLocale() == 'ar' ? 'ابحث...' : 'Search chocolates...' }}" class="w-full bg-white/10 border border-white/20 rounded-full pl-5 pr-12 py-2.5 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-amber-400/50 transition">
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-white/70 hover:text-white">
                        <i class="material-symbols-outlined text-xl">search</i>
                    </button>
                </div>
            </form>

            {{-- Icons --}}
            <div class="flex items-center gap-3 sm:gap-5">
                @guest
                <a href="{{ url('customer/login') }}" class="p-2 hover:bg-white/10 rounded-full transition" title="Sign In"><i class="material-symbols-outlined text-2xl">person</i></a>
                @endguest
                @if(auth()->check() && auth()->user()->role_id == 5)
                <a href="{{ url('customer/account-details') }}" class="p-2 hover:bg-white/10 rounded-full transition" title="Account"><i class="material-symbols-outlined text-2xl">person</i></a>
                @endif
                @if(isset($ecommerce_setting->online_order) && $ecommerce_setting->online_order == 1)
                <a href="{{ url('customer/wishlist') }}" class="p-2 hover:bg-white/10 rounded-full transition relative" title="Wishlist">
                    <i class="material-symbols-outlined text-2xl">favorite</i>
                    @if(($wishlist_count ?? 0) > 0)
                    <span class="absolute top-0 right-0 bg-amber-500 text-black text-xs font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center leading-none">{{ $wishlist_count }}</span>
                    @endif
                </a>
                <a href="{{ url('cart') }}" class="cart__menu flex items-center gap-2 px-3 py-2 bg-white/10 hover:bg-white/20 rounded-full transition" title="Cart">
                    <i class="material-symbols-outlined text-2xl">shopping_bag</i>
                    <span class="cart_qty font-semibold">{{ session()->get('total_qty', 0) }}</span>
                    <span class="total text-sm hidden sm:inline">
                        @php $subTotal = session()->get('subTotal', 0); $curr = session()->get('currency_code') ? \App\Models\Currency::where('code', session()->get('currency_code'))->first() : (\App\Models\Currency::where('is_active', 1)->first() ?? null); @endphp
                        @if($curr && !empty($general_setting))
                        @if(($general_setting->currency_position ?? 'prefix') == 'prefix')
                        {{ $curr->symbol ?? $curr->code }} {{ number_format($subTotal * ($curr->exchange_rate ?? 1), 2) }}
                        @else
                        {{ number_format($subTotal * ($curr->exchange_rate ?? 1), 2) }} {{ $curr->symbol ?? $curr->code }}
                        @endif
                        @endif
                    </span>
                </a>
                @endif
            </div>
        </div>
        {{-- Mobile Search --}}
        <form action="{{ route('products.search') }}" method="POST" class="md:hidden mt-3">
            @csrf
            <input type="text" name="search" placeholder="{{ app()->getLocale() == 'ar' ? 'ابحث' : 'Search' }}" class="w-full bg-white/10 border border-white/20 rounded-full px-4 py-2 text-white placeholder-white/50">
        </form>
    </div>

    {{-- Navigation --}}
    <nav class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <ul class="flex flex-wrap justify-center gap-4 sm:gap-8 py-3 text-sm font-semibold uppercase tracking-wide">
                @if(!empty($topNavItems) && is_array($topNavItems))
                    @foreach($topNavItems as $nav)
                        @if(empty($nav->children[0]))
                            @if(isset($nav->type) && $nav->type == 'custom')
                            <li><a href="{{ $nav->slug ?? '#' }}" target="_blank" class="hover:text-amber-200 transition">{{ $nav->name ?? $nav->title ?? '' }}</a></li>
                            @elseif(isset($nav->type) && $nav->type == 'category')
                            <li><a href="{{ url('shop/' . ($nav->slug ?? '')) }}" class="hover:text-amber-200 transition">{{ $nav->name ?? $nav->title ?? '' }}</a></li>
                            @elseif(isset($nav->type) && $nav->type == 'collection')
                            <li><a href="{{ url('collections/' . ($nav->slug ?? '')) }}" class="hover:text-amber-200 transition">{{ $nav->name ?? $nav->title ?? '' }}</a></li>
                            @elseif(isset($nav->type) && $nav->type == 'brand')
                            <li><a href="{{ url('brands/' . ($nav->slug ?? '')) }}" class="hover:text-amber-200 transition">{{ $nav->name ?? $nav->title ?? '' }}</a></li>
                            @else
                            <li><a href="{{ url($nav->slug ?? '/') }}" class="hover:text-amber-200 transition">{{ $nav->name ?? $nav->title ?? '' }}</a></li>
                            @endif
                        @else
                        <li class="relative group">
                            <span class="cursor-pointer hover:text-amber-200 transition">{{ $nav->name ?? $nav->title ?? '' }} <i class="material-symbols-outlined text-sm align-middle">expand_more</i></span>
                            <ul class="absolute left-1/2 -translate-x-1/2 top-full pt-2 hidden group-hover:block bg-black/95 border border-white/20 rounded-lg min-w-[180px] z-50">
                                @foreach($nav->children[0] ?? [] as $child)
                                <li><a href="{{ (isset($child->type) && $child->type == 'custom') ? ($child->slug ?? '#') : url($child->slug ?? '/') }}" class="block px-4 py-2.5 hover:bg-white/10 rounded" {{ (isset($child->type) && $child->type == 'custom') ? 'target="_blank"' : '' }}>{{ $child->name ?? $child->title ?? '' }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                        @endif
                    @endforeach
                @else
                <li><a href="{{ url('/') }}" class="hover:text-amber-200 transition">Home</a></li>
                <li><a href="{{ url('shop') }}" class="hover:text-amber-200 transition">Shop</a></li>
                <li><a href="{{ url('collections/all') }}" class="hover:text-amber-200 transition">Collections</a></li>
                <li><a href="{{ url('contact') }}" class="hover:text-amber-200 transition">Contact</a></li>
                @endif
            </ul>
        </div>
    </nav>
</header>
