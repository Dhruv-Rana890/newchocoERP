{{-- Luxury Chocolat Header – Compact, Responsive, Premium --}}
@php
    $allCats = $categories_list ?? collect();
    $parentCats = $allCats->whereNull('parent_id')->where('is_active', 1)->take(12);
    $defaultImg = asset('frontend/images/default-product.svg');
@endphp

<header class="chocolat-header sticky top-0 z-50 text-white shadow-lg" style="background: linear-gradient(180deg, {{ $ecommerce_setting->header_bg_color ?? '#0d0d0d' }} 0%, #0a0a0a 100%) !important;">
    {{-- Single row: top links + logo + search + actions (compact) --}}
    <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6">
        <div class="flex items-center justify-between gap-2 py-2 md:py-2.5">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex-shrink-0 order-1 group">
                @if(isset($ecommerce_setting->logo) && $ecommerce_setting->logo)
                <img src="{{ url('frontend/images') }}/{{ $ecommerce_setting->logo }}" alt="{{ $ecommerce_setting->site_title ?? 'Store' }}" class="h-8 sm:h-9 md:h-10 object-contain transition-transform duration-200 group-hover:scale-105" onerror="this.style.display='none'; var s=this.nextElementSibling; if(s) s.classList.remove('hidden');">
                <span class="hidden text-lg md:text-xl font-semibold tracking-widest text-amber-50 uppercase">{{ $ecommerce_setting->site_title ?? 'Chocolat' }}</span>
                @elseif(isset($general_setting->site_logo) && $general_setting->site_logo)
                <img src="{{ url('logo', $general_setting->site_logo) }}" alt="{{ $ecommerce_setting->site_title ?? 'Store' }}" class="h-8 sm:h-9 md:h-10 object-contain brightness-0 invert" onerror="this.style.display='none'; var s=this.nextElementSibling; if(s) s.classList.remove('hidden');">
                <span class="hidden text-lg md:text-xl font-semibold tracking-widest text-amber-50 uppercase">{{ $ecommerce_setting->site_title ?? 'Chocolat' }}</span>
                @else
                <span class="text-lg md:text-xl font-semibold tracking-widest text-amber-50 uppercase">{{ $ecommerce_setting->site_title ?? 'Chocolat' }}</span>
                @endif
            </a>

            {{-- Desktop: Quick links (inline, small) --}}
            <div class="hidden lg:flex items-center gap-4 text-[11px] uppercase tracking-widest text-amber-100/80 flex-1 max-w-xs justify-center">
                <a href="{{ url('/') }}" class="hover:text-amber-200 transition">{{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Home' }}</a>
                <a href="{{ url('shop') }}" class="hover:text-amber-200 transition">{{ app()->getLocale() == 'ar' ? 'المتجر' : 'Shop' }}</a>
                <a href="{{ url('contact') }}" class="hover:text-amber-200 transition">{{ app()->getLocale() == 'ar' ? 'تواصل' : 'Contact' }}</a>
            </div>

            {{-- Search (hidden on small) --}}
            <form action="{{ route('products.search') }}" method="POST" class="hidden sm:block flex-1 max-w-xs lg:max-w-sm mx-2 md:mx-4 order-2">
                @csrf
                <div class="relative">
                    <input type="text" name="search" placeholder="{{ app()->getLocale() == 'ar' ? 'ابحث...' : 'Search' }}" class="w-full bg-white/5 border border-amber-500/20 rounded-full pl-4 pr-10 py-2 text-white text-sm placeholder-amber-200/40 focus:outline-none focus:border-amber-400/40">
                    <button type="submit" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-amber-200/70 hover:text-amber-200">
                        <i class="material-symbols-outlined text-lg">search</i>
                    </button>
                </div>
            </form>

            {{-- Actions + Lang + Mobile menu trigger --}}
            <div class="flex items-center gap-1 sm:gap-2 order-3">
                @if(Route::has('set.locale'))
                <div class="hidden sm:flex gap-2 text-[11px] text-amber-100/70">
                    <a href="{{ route('set.locale', 'en') }}" class="hover:text-amber-200 {{ app()->getLocale() == 'en' ? 'font-semibold text-amber-200' : '' }}">EN</a>
                    <span class="opacity-50">|</span>
                    <a href="{{ route('set.locale', 'ar') }}" class="hover:text-amber-200 {{ app()->getLocale() == 'ar' ? 'font-semibold text-amber-200' : '' }}">AR</a>
                </div>
                @endif
                @guest
                <a href="{{ url('customer/login') }}" class="p-2 rounded-full hover:bg-white/10 transition" title="Sign In"><i class="material-symbols-outlined text-lg md:text-xl">person</i></a>
                @endguest
                @if(auth()->check() && auth()->user()->role_id == 5)
                <a href="{{ url('customer/account-details') }}" class="p-2 rounded-full hover:bg-white/10 transition" title="Account"><i class="material-symbols-outlined text-lg md:text-xl">person</i></a>
                @endif
                @if(isset($ecommerce_setting->online_order) && $ecommerce_setting->online_order == 1)
                <a href="{{ url('customer/wishlist') }}" class="p-2 rounded-full hover:bg-white/10 relative transition" title="Wishlist">
                    <i class="material-symbols-outlined text-lg md:text-xl">favorite</i>
                    @if(($wishlist_count ?? 0) > 0)
                    <span class="absolute top-0 right-0 bg-amber-500 text-black text-[10px] font-bold rounded-full min-w-[14px] h-[14px] flex items-center justify-center leading-none">{{ $wishlist_count }}</span>
                    @endif
                </a>
                <a href="#" class="cart__menu flex items-center gap-1.5 px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-full bg-white/5 hover:bg-white/10 border border-amber-500/20 transition">
                    <i class="material-symbols-outlined text-lg md:text-xl">shopping_bag</i>
                    <span class="cart_qty font-semibold text-sm text-amber-100">{{ session()->get('total_qty', 0) }}</span>
                    <span class="total text-xs hidden lg:inline text-amber-200/80">
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
                {{-- Mobile menu toggle --}}
                <button type="button" id="chocolat-nav-toggle" class="lg:hidden p-2 rounded-lg hover:bg-white/10 transition text-amber-100" aria-label="Menu">
                    <i class="material-symbols-outlined text-2xl">menu</i>
                </button>
            </div>
        </div>
        {{-- Mobile search --}}
        <form action="{{ route('products.search') }}" method="POST" class="sm:hidden pb-2">
            @csrf
            <input type="text" name="search" placeholder="{{ app()->getLocale() == 'ar' ? 'ابحث' : 'Search' }}" class="w-full bg-white/5 border border-amber-500/20 rounded-full px-4 py-2 text-sm text-white placeholder-amber-200/40">
        </form>
    </div>

    {{-- Navigation – desktop: compact bar | mobile: drawer --}}
    <nav class="chocolat-nav border-t border-amber-500/15 bg-black/20" aria-label="Main">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6">
            {{-- Desktop nav --}}
            <ul class="hidden lg:flex flex-wrap justify-center gap-1 xl:gap-4 py-2 text-xs font-medium uppercase tracking-widest">
                @if(!empty($topNavItems) && is_array($topNavItems))
                    @foreach($topNavItems as $nav)
                        @if(!empty($nav->children[0]))
                        <li class="megamenu-item relative group">
                            <span class="cursor-pointer inline-flex items-center gap-0.5 py-1.5 px-2 text-amber-100/90 hover:text-amber-200 transition">
                                {{ $nav->name ?? $nav->title ?? '' }}
                                <i class="material-symbols-outlined text-xs">expand_more</i>
                            </span>
                            <div class="megamenu-panel absolute left-0 right-0 top-full opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 bg-[#0d0d0d] border-t border-amber-500/20 shadow-2xl z-50">
                                <div class="max-w-5xl mx-auto px-6 py-6">
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                        @foreach($nav->children[0] ?? [] as $child)
                                        <a href="{{ (isset($child->type) && $child->type == 'custom') ? ($child->slug ?? '#') : url($child->slug ?? '/') }}" class="block py-1.5 text-amber-100/90 hover:text-amber-200 text-sm" {{ (isset($child->type) && $child->type == 'custom') ? 'target="_blank"' : '' }}>{{ $child->name ?? $child->title ?? '' }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </li>
                        @else
                        <li>
                            @if(isset($nav->type) && $nav->type == 'custom')
                            <a href="{{ $nav->slug ?? '#' }}" target="_blank" class="block py-1.5 px-2 text-amber-100/90 hover:text-amber-200 transition">{{ $nav->name ?? $nav->title ?? '' }}</a>
                            @elseif(isset($nav->type) && $nav->type == 'category')
                            <a href="{{ url('shop/' . ($nav->slug ?? '')) }}" class="block py-1.5 px-2 text-amber-100/90 hover:text-amber-200 transition">{{ $nav->name ?? $nav->title ?? '' }}</a>
                            @elseif(isset($nav->type) && $nav->type == 'collection')
                            <a href="{{ url('collections/' . ($nav->slug ?? '')) }}" class="block py-1.5 px-2 text-amber-100/90 hover:text-amber-200 transition">{{ $nav->name ?? $nav->title ?? '' }}</a>
                            @elseif(isset($nav->type) && $nav->type == 'brand')
                            <a href="{{ url('brands/' . ($nav->slug ?? '')) }}" class="block py-1.5 px-2 text-amber-100/90 hover:text-amber-200 transition">{{ $nav->name ?? $nav->title ?? '' }}</a>
                            @else
                            <a href="{{ url($nav->slug ?? '/') }}" class="block py-1.5 px-2 text-amber-100/90 hover:text-amber-200 transition">{{ $nav->name ?? $nav->title ?? '' }}</a>
                            @endif
                        </li>
                        @endif
                    @endforeach
                    @if($parentCats->isNotEmpty())
                    <li class="megamenu-item relative group">
                        <span class="cursor-pointer inline-flex items-center gap-0.5 py-1.5 px-2 text-amber-100/90 hover:text-amber-200 transition">
                            {{ app()->getLocale() == 'ar' ? 'التصنيفات' : 'Categories' }}
                            <i class="material-symbols-outlined text-xs">expand_more</i>
                        </span>
                        <div class="megamenu-panel absolute left-0 right-0 top-full opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 bg-[#0d0d0d] border-t border-amber-500/20 shadow-2xl z-50">
                            <div class="max-w-5xl mx-auto px-6 py-6">
                                <div class="grid grid-cols-3 md:grid-cols-6 gap-4">
                                    @foreach($parentCats as $cat)
                                    <a href="{{ url('shop/' . ($cat->slug ?? '')) }}" class="flex flex-col items-center gap-2 group/cat">
                                        @if(!empty($cat->icon))
                                        <img src="{{ url('images/category/icons/' . $cat->icon) }}" alt="" class="w-10 h-10 rounded-full object-contain border border-amber-500/30 group-hover/cat:border-amber-400/50" onerror="this.src='{{ $defaultImg }}'">
                                        @elseif(!empty($cat->image))
                                        <img src="{{ url('images/category/' . $cat->image) }}" alt="" class="w-10 h-10 rounded-full object-cover border border-amber-500/30" onerror="this.src='{{ $defaultImg }}'">
                                        @else
                                        <img src="{{ $defaultImg }}" alt="" class="w-10 h-10 rounded-full object-cover border border-amber-500/30">
                                        @endif
                                        <span class="text-[11px] text-amber-100/80 text-center">{{ $cat->name ?? '' }}</span>
                                    </a>
                                    @endforeach
                                </div>
                                <a href="{{ url('shop') }}" class="inline-block mt-4 text-sm font-semibold text-amber-200 hover:underline">{{ app()->getLocale() == 'ar' ? 'جميع التصنيفات' : 'View All' }}</a>
                            </div>
                        </div>
                    </li>
                    @endif
                @else
                <li><a href="{{ url('/') }}" class="block py-1.5 px-2 text-amber-100/90 hover:text-amber-200">Home</a></li>
                <li><a href="{{ url('shop') }}" class="block py-1.5 px-2 text-amber-100/90 hover:text-amber-200">Shop</a></li>
                @if($parentCats->isNotEmpty())
                <li class="megamenu-item relative group">
                    <span class="cursor-pointer inline-flex items-center gap-0.5 py-1.5 px-2 text-amber-100/90 hover:text-amber-200">Categories <i class="material-symbols-outlined text-xs">expand_more</i></span>
                    <div class="megamenu-panel absolute left-0 right-0 top-full opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 bg-[#0d0d0d] border-t border-amber-500/20 shadow-2xl z-50">
                        <div class="max-w-5xl mx-auto px-6 py-6">
                            <div class="grid grid-cols-3 md:grid-cols-6 gap-4">
                                @foreach($parentCats as $cat)
                                <a href="{{ url('shop/' . ($cat->slug ?? '')) }}" class="flex flex-col items-center gap-2">
                                    @if(!empty($cat->icon))
                                    <img src="{{ url('images/category/icons/' . $cat->icon) }}" alt="" class="w-10 h-10 rounded-full object-contain border border-amber-500/30" onerror="this.src='{{ $defaultImg }}'">
                                    @elseif(!empty($cat->image))
                                    <img src="{{ url('images/category/' . $cat->image) }}" alt="" class="w-10 h-10 rounded-full object-cover border border-amber-500/30" onerror="this.src='{{ $defaultImg }}'">
                                    @else
                                    <img src="{{ $defaultImg }}" alt="" class="w-10 h-10 rounded-full object-cover border border-amber-500/30">
                                    @endif
                                    <span class="text-[11px] text-amber-100/80">{{ $cat->name ?? '' }}</span>
                                </a>
                                @endforeach
                            </div>
                            <a href="{{ url('shop') }}" class="inline-block mt-4 text-sm font-semibold text-amber-200 hover:underline">View All</a>
                        </div>
                    </div>
                </li>
                @endif
                <li><a href="{{ url('collections/all') }}" class="block py-1.5 px-2 text-amber-100/90 hover:text-amber-200">Collections</a></li>
                <li><a href="{{ url('contact') }}" class="block py-1.5 px-2 text-amber-100/90 hover:text-amber-200">Contact</a></li>
                @endif
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
