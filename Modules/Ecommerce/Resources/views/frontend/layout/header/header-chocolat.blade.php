{{-- Hotel Chocolat style: top bar, logo prominent center, search, category nav from admin --}}
@php
    $allCats = $categories_list ?? collect();
    $parentCats = $allCats->whereNull('parent_id')->where('is_active', 1);
    if ($parentCats->isNotEmpty() && isset($parentCats->first()->show_in_menu)) {
        $parentCats = $parentCats->filter(function($c) { return !empty($c->show_in_menu); })->values();
    }
    if ($parentCats->isNotEmpty() && isset($parentCats->first()->menu_sort_order)) {
        $parentCats = $parentCats->sortBy(function($c) { return $c->menu_sort_order !== null ? (int)$c->menu_sort_order : 999999; })->values();
    }
    $parentCats = $parentCats->take(12);
    $defaultImg = asset('frontend/images/default-product.svg');
@endphp

{{-- Web: non-sticky | Mobile: sticky --}}
<header class="chocolat-header sticky lg:static top-0 z-50 text-white shadow-lg" style="background: linear-gradient(180deg, {{ $ecommerce_setting->header_bg_color ?? '#0d0d0d' }} 0%, #0a0a0a 100%) !important;">
    {{-- Top utility bar (Hotel Chocolat style) --}}
    <div class="hidden lg:block border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-1.5 flex justify-between items-center text-[11px] uppercase tracking-widest text-amber-100/80">
            <div class="flex items-center gap-5">
                <a href="{{ url('/') }}" class="hover:text-amber-200 transition">{{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Home' }}</a>
                <a href="{{ url('shop') }}" class="hover:text-amber-200 transition">{{ app()->getLocale() == 'ar' ? 'المتجر' : 'Shop' }}</a>
                <a href="{{ url('contact') }}" class="hover:text-amber-200 transition">{{ app()->getLocale() == 'ar' ? 'تواصل' : 'Contact' }}</a>
            </div>
            <div class="flex items-center gap-4">
                @if(Route::has('set.locale'))
                <a href="{{ route('set.locale', 'en') }}" class="hover:text-amber-200 {{ app()->getLocale() == 'en' ? 'font-semibold text-amber-200' : '' }}">EN</a>
                <a href="{{ route('set.locale', 'ar') }}" class="hover:text-amber-200 {{ app()->getLocale() == 'ar' ? 'font-semibold text-amber-200' : '' }}">AR</a>
                @endif
                @guest
                <a href="{{ url('customer/login') }}" class="hover:text-amber-200" title="Account"><i class="material-symbols-outlined text-base align-middle">person</i></a>
                @endguest
                @if(auth()->check() && auth()->user()->role_id == 5)
                <a href="{{ url('customer/account-details') }}" class="hover:text-amber-200" title="Account"><i class="material-symbols-outlined text-base align-middle">person</i></a>
                @endif
                @if(isset($ecommerce_setting->online_order) && $ecommerce_setting->online_order == 1)
                <a href="{{ url('customer/wishlist') }}" class="hover:text-amber-200 relative" title="Wishlist"><i class="material-symbols-outlined text-base align-middle">favorite</i>@if(($wishlist_count ?? 0) > 0)<span class="absolute -top-1 -right-1 bg-amber-500 text-black text-[9px] font-bold rounded-full min-w-[12px] h-[12px] flex items-center justify-center">{{ $wishlist_count }}</span>@endif</a>
                <a href="#" class="cart__menu hover:text-amber-200 flex items-center gap-1" title="Cart"><i class="material-symbols-outlined text-base align-middle">shopping_bag</i> <span class="cart_qty text-amber-100">{{ session()->get('total_qty', 0) }}</span></a>
                @endif
            </div>
        </div>
    </div>

    {{-- Main: logo center (prominent) + search below --}}
    <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 py-3 md:py-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            {{-- Logo – prominent, center on desktop --}}
            <div class="flex items-center justify-between lg:justify-center lg:flex-1 order-1">
            <a href="{{ url('/') }}" class="group mx-auto lg:mx-0">
                @if(isset($ecommerce_setting->logo) && $ecommerce_setting->logo)
                <img src="{{ url('frontend/images') }}/{{ $ecommerce_setting->logo }}" alt="{{ $ecommerce_setting->site_title ?? 'Store' }}" class="h-10 sm:h-12 md:h-14 object-contain transition-transform duration-200 group-hover:scale-105" onerror="this.style.display='none'; var s=this.nextElementSibling; if(s) s.classList.remove('hidden');">
                <span class="hidden text-xl md:text-2xl font-semibold tracking-[0.2em] text-amber-50 uppercase">{{ $ecommerce_setting->site_title ?? 'Chocolat' }}</span>
                @elseif(isset($general_setting->site_logo) && $general_setting->site_logo)
                <img src="{{ url('logo', $general_setting->site_logo) }}" alt="{{ $ecommerce_setting->site_title ?? 'Store' }}" class="h-10 sm:h-12 md:h-14 object-contain brightness-0 invert" onerror="this.style.display='none'; var s=this.nextElementSibling; if(s) s.classList.remove('hidden');">
                <span class="hidden text-xl md:text-2xl font-semibold tracking-[0.2em] text-amber-50 uppercase">{{ $ecommerce_setting->site_title ?? 'Chocolat' }}</span>
                @else
                <span class="text-xl md:text-2xl font-semibold tracking-[0.2em] text-amber-50 uppercase">{{ $ecommerce_setting->site_title ?? 'Chocolat' }}</span>
                @endif
            </a>
            </div>
            {{-- Search bar (below logo, full width on desktop) --}}
            <form action="{{ route('products.search') }}" method="POST" class="w-full lg:max-w-xl lg:flex-1 order-2">
                @csrf
                <div class="relative">
                    <input type="text" name="search" placeholder="{{ app()->getLocale() == 'ar' ? 'ابحث...' : 'Search for a product' }}" class="w-full bg-white/10 border border-amber-500/20 rounded-full pl-5 pr-12 py-2.5 text-white text-sm placeholder-amber-200/50 focus:outline-none focus:border-amber-400/40">
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-amber-200/70 hover:text-amber-200">
                        <i class="material-symbols-outlined text-xl">search</i>
                    </button>
                </div>
            </form>
            {{-- Mobile: actions + menu trigger --}}
            <div class="flex items-center gap-2 lg:hidden order-3">
                @if(isset($ecommerce_setting->online_order) && $ecommerce_setting->online_order == 1)
                <a href="{{ url('customer/wishlist') }}" class="p-2 relative"><i class="material-symbols-outlined text-xl">favorite</i>@if(($wishlist_count ?? 0) > 0)<span class="absolute top-0 right-0 bg-amber-500 text-black text-[10px] font-bold rounded-full min-w-[14px] h-[14px] flex items-center justify-center">{{ $wishlist_count }}</span>@endif</a>
                <a href="#" class="cart__menu flex items-center gap-1 px-2 py-1.5 rounded-full bg-white/10"><i class="material-symbols-outlined text-xl">shopping_bag</i><span class="cart_qty font-semibold">{{ session()->get('total_qty', 0) }}</span></a>
                @endif
                <button type="button" id="chocolat-nav-toggle" class="p-2 rounded-lg hover:bg-white/10 text-amber-100" aria-label="Menu"><i class="material-symbols-outlined text-2xl">menu</i></button>
            </div>
        </div>
        {{-- Mobile search (below logo when no desktop) --}}
        <form action="{{ route('products.search') }}" method="POST" class="lg:hidden mt-2 pb-1">
            @csrf
            <input type="text" name="search" placeholder="{{ app()->getLocale() == 'ar' ? 'ابحث' : 'Search' }}" class="w-full bg-white/5 border border-amber-500/20 rounded-full px-4 py-2 text-sm text-white placeholder-amber-200/40">
        </form>
    </div>

    {{-- Category nav: admin “Show in navbar” categories as direct links (Hotel Chocolat style) --}}
    <nav class="chocolat-nav border-t border-amber-500/15 bg-black/20" aria-label="Main">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6">
            <ul class="hidden lg:flex flex-wrap justify-center gap-1 xl:gap-4 py-2.5 text-xs font-medium uppercase tracking-widest">
                <!-- <li><a href="{{ url('/') }}" class="block py-1.5 px-2 text-amber-100/90 hover:text-amber-200 transition">{{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Home' }}</a></li>
                <li><a href="{{ url('shop') }}" class="block py-1.5 px-2 text-amber-100/90 hover:text-amber-200 transition">{{ app()->getLocale() == 'ar' ? 'المتجر' : 'Shop' }}</a></li> -->
                @foreach($parentCats as $cat)
                <li><a href="{{ url('shop/' . ($cat->slug ?? '')) }}" class="block py-1.5 px-2 text-amber-100/90 hover:text-amber-200 transition">{{ $cat->name ?? '' }}</a></li>
                @endforeach
                <!-- <li><a href="{{ url('collections/all') }}" class="block py-1.5 px-2 text-amber-100/90 hover:text-amber-200 transition">Collections</a></li>
                <li><a href="{{ url('contact') }}" class="block py-1.5 px-2 text-amber-100/90 hover:text-amber-200 transition">{{ app()->getLocale() == 'ar' ? 'تواصل' : 'Contact' }}</a></li> -->
            </ul>
        </div>
    </nav>

    {{-- Mobile nav drawer --}}
    <div id="chocolat-mobile-nav" class="lg:hidden fixed inset-0 z-40 opacity-0 invisible transition-all duration-300" aria-hidden="true">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" id="chocolat-nav-overlay"></div>
        <div class="absolute top-0 right-0 w-72 max-w-[85vw] h-full bg-[#0d0d0d] border-l border-amber-500/20 shadow-2xl overflow-y-auto" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
            <div class="p-4 flex justify-between items-center border-b border-amber-500/20">
                <span class="text-amber-100 font-semibold uppercase tracking-wider">Menu</span>
                <button type="button" id="chocolat-nav-close" class="p-2 text-amber-100 hover:bg-white/10 rounded-lg"><i class="material-symbols-outlined">close</i></button>
            </div>
            <div class="py-4">
                <a href="{{ url('/') }}" class="block py-3 px-4 text-amber-100/90 hover:bg-white/5 border-b border-white/5">{{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Home' }}</a>
                <a href="{{ url('shop') }}" class="block py-3 px-4 text-amber-100/90 hover:bg-white/5 border-b border-white/5">{{ app()->getLocale() == 'ar' ? 'المتجر' : 'Shop' }}</a>
                <a href="{{ url('collections/all') }}" class="block py-3 px-4 text-amber-100/90 hover:bg-white/5 border-b border-white/5">Collections</a>
                <a href="{{ url('contact') }}" class="block py-3 px-4 text-amber-100/90 hover:bg-white/5 border-b border-white/5">{{ app()->getLocale() == 'ar' ? 'تواصل' : 'Contact' }}</a>
                @if($parentCats->isNotEmpty())
                <div class="px-4 py-2 text-amber-200/70 text-xs uppercase tracking-wider mt-2">{{ app()->getLocale() == 'ar' ? 'التصنيفات' : 'Categories' }}</div>
                @foreach($parentCats as $cat)
                <a href="{{ url('shop/' . ($cat->slug ?? '')) }}" class="flex items-center gap-3 py-2.5 px-4 hover:bg-white/5">
                    @if(!empty($cat->icon))
                    <img src="{{ url('images/category/icons/' . $cat->icon) }}" alt="" class="w-8 h-8 rounded-full object-contain" onerror="this.src='{{ $defaultImg }}'">
                    @else
                    <img src="{{ $defaultImg }}" alt="" class="w-8 h-8 rounded-full object-cover">
                    @endif
                    <span class="text-amber-100/90">{{ $cat->name ?? '' }}</span>
                </a>
                @endforeach
                @endif
                @if(Route::has('set.locale'))
                <div class="flex gap-4 px-4 pt-4 mt-4 border-t border-amber-500/20">
                    <a href="{{ route('set.locale', 'en') }}" class="text-sm {{ app()->getLocale() == 'en' ? 'font-semibold text-amber-200' : 'text-amber-100/70' }}">EN</a>
                    <a href="{{ route('set.locale', 'ar') }}" class="text-sm {{ app()->getLocale() == 'ar' ? 'font-semibold text-amber-200' : 'text-amber-100/70' }}">AR</a>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if(!empty($ecommerce_setting->header_announcement) || !empty($ecommerce_setting->header_announcement_ar))
    <div id="header-announcement" class="border-t border-amber-500/15 bg-amber-950/80 text-amber-50 px-3 py-2">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-2">
            <p class="text-center flex-1 text-xs tracking-wide">
                {{ app()->getLocale() == 'ar' && !empty($ecommerce_setting->header_announcement_ar) ? $ecommerce_setting->header_announcement_ar : ($ecommerce_setting->header_announcement ?? '') }}
            </p>
            <button type="button" id="close-announcement" class="p-1 hover:bg-amber-500/20 rounded" aria-label="Close"><i class="material-symbols-outlined text-base">close</i></button>
        </div>
    </div>
    @endif
</header>
<script>
(function(){
    var t = document.getElementById('chocolat-nav-toggle');
    var c = document.getElementById('chocolat-nav-close');
    var n = document.getElementById('chocolat-mobile-nav');
    var o = document.getElementById('chocolat-nav-overlay');
    function openNav(){ if(n){ n.classList.remove('opacity-0','invisible'); n.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden'; }}
    function closeNav(){ if(n){ n.classList.add('opacity-0','invisible'); n.setAttribute('aria-hidden','true'); document.body.style.overflow=''; }}
    if(t) t.addEventListener('click', openNav);
    if(c) c.addEventListener('click', closeNav);
    if(o) o.addEventListener('click', closeNav);
    var bar = document.getElementById('header-announcement');
    var btn = document.getElementById('close-announcement');
    if (bar && btn) {
        if (localStorage.getItem('chocolat_announcement_closed') === '1') bar.style.display = 'none';
        btn.addEventListener('click', function(){ bar.style.display = 'none'; localStorage.setItem('chocolat_announcement_closed', '1'); });
    }
})();
</script>
