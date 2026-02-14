@extends('ecommerce::frontend.layout.main')

@section('title') {{ $ecommerce_setting->site_title ?? '' }} @endsection
@section('description') {{ '' }} @endsection

@section('content')

@php
    $isRtl = !empty($ecommerce_setting->is_rtl);
    $lang = app()->getLocale();
    $gs = $general_setting ?? null;
    $cur = $currency ?? null;
    $curSym = $cur ? ($cur->symbol ?? $cur->code ?? '') : '';
    $defaultImg = asset('frontend/images/default-product.svg');
@endphp

{{-- Hero Slider: Type 1 = Multi-image (auto+arrows) with caption/link, Type 2 = Video with caption/link --}}
@if(isset($hero_banners) && $hero_banners->isNotEmpty())
<section class="relative overflow-hidden chocolat-hero-slider" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <div id="chocolat-hero-carousel" class="relative">
        <div class="overflow-hidden">
            @foreach($hero_banners as $key => $banner)
            @php
                $slideType = $banner->slider_type ?? 'image';
                $imgList = [];
                if ($slideType == 'image') {
                    if (!empty($banner->images)) {
                        $imgList = is_string($banner->images) ? json_decode($banner->images, true) : (array)$banner->images;
                    }
                    if (empty($imgList) && !empty($banner->image)) {
                        $imgList = [$banner->image];
                    }
                }
            @endphp
            <div class="chocolat-hero-slide {{ $key === 0 ? 'active' : '' }}" data-index="{{ $key }}" style="background-color: {{ $banner->bg_color ?? '#8B1538' }};">
                @if($slideType == 'video' && !empty($banner->video_url))
                <div class="relative min-h-[400px] md:min-h-[500px] lg:min-h-[550px] flex items-center">
                    <video class="absolute inset-0 w-full h-full object-cover" autoplay muted loop playsinline>
                        <source src="{{ $banner->video_url }}" type="video/mp4">
                    </video>
                    <div class="relative z-10 w-full container mx-auto px-4 md:px-6 lg:px-8 py-12">
                        <div class="max-w-xl">
                            <h2 class="text-2xl md:text-4xl font-bold uppercase mb-3" style="color: {{ $banner->text_color ?? '#FFFFFF' }};">{{ $lang == 'ar' && !empty($banner->title_ar) ? $banner->title_ar : ($banner->title ?? '') }}</h2>
                            @if(!empty($banner->subtitle) || !empty($banner->subtitle_ar))
                            <p class="text-base md:text-lg mb-4 opacity-95" style="color: {{ $banner->text_color ?? '#FFFFFF' }};">{{ $lang == 'ar' && !empty($banner->subtitle_ar) ? $banner->subtitle_ar : ($banner->subtitle ?? '') }}</p>
                            @endif
                            @if(!empty($banner->cta_link))
                            <a href="{{ url($banner->cta_link) }}" class="inline-block px-6 py-2.5 font-semibold uppercase text-white" style="background-color: {{ $ecommerce_setting->cta_bg_color ?? '#000000' }};">{{ $lang == 'ar' && !empty($banner->cta_text_ar) ? $banner->cta_text_ar : ($banner->cta_text ?? 'SHOP NOW') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
                @elseif(!empty($imgList))
                <div class="relative min-h-[400px] md:min-h-[500px] lg:min-h-[550px] flex items-center">
                    <div class="chocolat-hero-img-carousel w-full" data-images='@json($imgList)' data-interval="4000">
                        @foreach($imgList as $i => $img)
                        <div class="chocolat-hero-img-slide {{ $i === 0 ? 'active' : '' }}">
                            <img src="{{ url('frontend/images/hero/' . $img) }}" alt="" class="w-full h-[400px] md:h-[500px] object-cover" onerror="this.src='{{ $defaultImg }}'">
                        </div>
                        @endforeach
                    </div>
                    <div class="absolute inset-0 flex items-center pointer-events-none">
                        <div class="container mx-auto px-4 md:px-6 lg:px-8">
                            <div class="max-w-xl {{ $isRtl ? 'ml-auto' : '' }}">
                                <h2 class="text-2xl md:text-4xl font-bold uppercase mb-3" style="color: {{ $banner->text_color ?? '#FFFFFF' }};">{{ $lang == 'ar' && !empty($banner->title_ar) ? $banner->title_ar : ($banner->title ?? '') }}</h2>
                                @if(!empty($banner->subtitle) || !empty($banner->subtitle_ar))
                                <p class="text-base md:text-lg mb-4 opacity-95" style="color: {{ $banner->text_color ?? '#FFFFFF' }};">{{ $lang == 'ar' && !empty($banner->subtitle_ar) ? $banner->subtitle_ar : ($banner->subtitle ?? '') }}</p>
                                @endif
                                @if(!empty($banner->cta_link))
                                <a href="{{ url($banner->cta_link) }}" class="inline-block px-8 py-3 font-semibold uppercase tracking-[0.12em] text-white pointer-events-auto transition-all duration-200 hover:opacity-95" style="background: linear-gradient(135deg, {{ $ecommerce_setting->cta_bg_color ?? '#1a1512' }} 0%, #2c1810 100%);">{{ $lang == 'ar' && !empty($banner->cta_text_ar) ? $banner->cta_text_ar : ($banner->cta_text ?? 'SHOP NOW') }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @else
                {{-- No image: show default --}}
                <div class="relative min-h-[400px] md:min-h-[500px] lg:min-h-[550px] flex items-center">
                    <img src="{{ $defaultImg }}" alt="" class="absolute inset-0 w-full h-full object-cover opacity-40">
                    <div class="relative z-10 w-full container mx-auto px-4 md:px-6 lg:px-8">
                        <div class="max-w-xl {{ $isRtl ? 'ml-auto' : '' }}">
                            <h2 class="text-2xl md:text-4xl font-bold uppercase mb-3" style="color: {{ $banner->text_color ?? '#FFFFFF' }};">{{ $lang == 'ar' && !empty($banner->title_ar) ? $banner->title_ar : ($banner->title ?? '') }}</h2>
                            @if(!empty($banner->subtitle) || !empty($banner->subtitle_ar))
                            <p class="text-base md:text-lg mb-4 opacity-95" style="color: {{ $banner->text_color ?? '#FFFFFF' }};">{{ $lang == 'ar' && !empty($banner->subtitle_ar) ? $banner->subtitle_ar : ($banner->subtitle ?? '') }}</p>
                            @endif
                            @if(!empty($banner->cta_link))
                            <a href="{{ url($banner->cta_link) }}" class="inline-block px-8 py-3 font-semibold uppercase tracking-[0.12em] text-white" style="background: linear-gradient(135deg, {{ $ecommerce_setting->cta_bg_color ?? '#1a1512' }} 0%, #2c1810 100%);">{{ $lang == 'ar' && !empty($banner->cta_text_ar) ? $banner->cta_text_ar : ($banner->cta_text ?? 'SHOP NOW') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @if($hero_banners->count() > 1)
        <button type="button" class="chocolat-hero-prev absolute left-2 top-1/2 -translate-y-1/2 z-20 w-12 h-12 flex items-center justify-center bg-black/50 hover:bg-black/70 rounded-full text-white transition"><i class="material-symbols-outlined">chevron_left</i></button>
        <button type="button" class="chocolat-hero-next absolute right-2 top-1/2 -translate-y-1/2 z-20 w-12 h-12 flex items-center justify-center bg-black/50 hover:bg-black/70 rounded-full text-white transition"><i class="material-symbols-outlined">chevron_right</i></button>
        @endif
    </div>
</section>
@else
{{-- Fallback Hero when no banners --}}
<section class="relative overflow-hidden" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <div class="relative min-h-[400px] md:min-h-[500px] flex items-center" style="background-color: {{ $ecommerce_setting->header_bg_color ?? '#8B1538' }};">
        <div class="container mx-auto px-4 md:px-6 lg:px-8 py-12 md:py-16">
            <div class="max-w-2xl">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold uppercase tracking-wide mb-4 text-white">
                    {{ $lang == 'ar' ? 'هدايا الشوكولاتة الفاخرة' : 'Premium Chocolate Gifts' }}
                </h1>
                <p class="text-lg md:text-xl mb-6 text-white opacity-95">
                    {{ $lang == 'ar' ? 'اكتشف تشكيلتنا المميزة من الهدايا اللذيذة' : 'Discover our exquisite collection of delicious gifts' }}
                </p>
                <a href="{{ url('shop') }}" class="inline-block px-10 py-3.5 font-semibold uppercase tracking-[0.12em] text-white transition-all duration-200 hover:opacity-95" style="background: linear-gradient(135deg, {{ $ecommerce_setting->cta_bg_color ?? '#1a1512' }} 0%, #2c1810 100%);">
                    {{ $lang == 'ar' ? 'تسوق الآن' : 'SHOP NOW' }}
                </a>
            </div>
        </div>
    </div>
</section>
@endif

{{-- Round Product Categories – Luxury --}}
@php
    $roundCats = isset($categories_list) ? $categories_list->whereNull('parent_id')->where('is_active', 1)->take(8) : collect();
@endphp
@if($roundCats->isNotEmpty())
<section class="py-14 md:py-20 bg-[#faf8f5] border-y border-amber-900/5" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <div class="container mx-auto px-4 md:px-6 lg:px-8">
        <h2 class="text-2xl md:text-3xl font-semibold text-center mb-10 md:mb-14 text-[#2c1810] tracking-[0.08em] uppercase">
            {{ $lang == 'ar' ? 'تصفح حسب التصنيف' : 'Shop by Category' }}
        </h2>
        <div class="flex flex-wrap justify-center gap-8 md:gap-12">
            @foreach($roundCats as $cat)
            <a href="{{ url('shop/' . ($cat->slug ?? '')) }}" class="group flex flex-col items-center">
                <div class="w-28 h-28 md:w-32 md:h-32 rounded-full overflow-hidden bg-white border-2 border-amber-200/60 group-hover:border-amber-400 group-hover:shadow-xl transition-all duration-300 flex items-center justify-center shadow-lg">
                    @if(!empty($cat->icon))
                    <img src="{{ url('images/category/icons/' . $cat->icon) }}" alt="{{ $cat->name }}" class="w-14 h-14 md:w-16 md:h-16 object-contain group-hover:scale-110 transition-transform duration-300" onerror="this.src='{{ $defaultImg }}'">
                    @elseif(!empty($cat->image))
                    <img src="{{ url('images/category/' . $cat->image) }}" alt="{{ $cat->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" onerror="this.src='{{ $defaultImg }}'">
                    @else
                    <img src="{{ $defaultImg }}" alt="{{ $cat->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                    @endif
                </div>
                <span class="mt-4 text-sm font-medium text-[#5c4a3a] group-hover:text-[#2c1810] transition-colors text-center tracking-wide">{{ $cat->name ?? '' }}</span>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Featured Products Section – Luxury --}}
@if(isset($featured_products) && $featured_products->isNotEmpty())
<section class="py-14 md:py-20 bg-[#f5f0ea]" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <div class="container mx-auto px-4 md:px-6 lg:px-8">
        <h2 class="text-2xl md:text-3xl font-semibold text-center mb-10 md:mb-14 text-[#2c1810] tracking-[0.08em] uppercase">
            {{ $lang == 'ar' ? 'منتجات مميزة' : 'Popular Gifts' }}
        </h2>
        <div class="relative">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-5 md:gap-6 product-grid-chocolat">
                @foreach($featured_products as $product)
                <a href="{{ url('product') }}/{{ $product->slug ?? Str::slug($product->name ?? '') }}/{{ $product->id }}" class="group block bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl border border-amber-900/5 hover:border-amber-300/40 transition-all duration-300">
                    <div class="aspect-square relative bg-gray-100">
                        @if(!empty($product->image))
                        @php $img = is_string($product->image) ? explode(',', $product->image)[0] : $product->image; @endphp
                        <img src="{{ url('images/product/large/' . $img) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.src='{{ $defaultImg }}'">
                        @else
                        <img src="{{ $defaultImg }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @endif
                        @if(($product->promotion ?? 0) == 1 && (empty($product->last_date) || ($product->last_date ?? '') > date('Y-m-d')))
                        <span class="absolute top-2 {{ $isRtl ? 'right' : 'left' }}-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded">
                            -{{ $product->price > 0 ? round(($product->price - ($product->promotion_price ?? $product->price)) / $product->price * 100) : 0 }}%
                        </span>
                        @endif
                    </div>
                    <div class="p-3 md:p-4">
                        <h3 class="text-sm md:text-base font-medium text-gray-800 line-clamp-2 min-h-[2.5em]">
                            {{ $lang == 'ar' && !empty($product->name_ar) ? $product->name_ar : $product->name }}
                        </h3>
                        <div class="mt-2 flex items-center gap-2">
                            @if(($product->promotion ?? 0) == 1 && (empty($product->last_date) || ($product->last_date ?? '') > date('Y-m-d')))
                            <span class="font-bold" style="color: var(--theme-color, #8B1538);">
                                @if($gs && ($gs->currency_position ?? 'prefix') == 'prefix')
                                {{ $curSym }} {{ $product->promotion_price ?? $product->price }}
                                @else
                                {{ $product->promotion_price ?? $product->price }} {{ $curSym }}
                                @endif
                            </span>
                            <span class="text-gray-400 text-sm line-through">
                                @if($gs && ($gs->currency_position ?? 'prefix') == 'prefix')
                                {{ $curSym }} {{ $product->price }}
                                @else
                                {{ $product->price }} {{ $curSym }}
                                @endif
                            </span>
                            @else
                            <span class="font-bold" style="color: var(--theme-color, #8B1538);">
                                @if($gs && ($gs->currency_position ?? 'prefix') == 'prefix')
                                {{ $curSym }} {{ $product->price }}
                                @else
                                {{ $product->price }} {{ $curSym }}
                                @endif
                            </span>
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        <div class="text-center mt-10">
            <a href="{{ url('shop') }}" class="inline-block px-10 py-3.5 font-semibold uppercase tracking-[0.12em] text-white transition-all duration-200 hover:opacity-95 border border-transparent hover:border-amber-400/50" style="background: linear-gradient(135deg, {{ $ecommerce_setting->cta_bg_color ?? '#1a1512' }} 0%, #2c1810 100%);">
                {{ $lang == 'ar' ? 'تسوق الكل' : 'Shop All Gifts' }}
            </a>
        </div>
    </div>
</section>
@endif

{{-- Secondary Banner (Easter-style) - use second hero banner if exists --}}
@if(isset($hero_banners) && $hero_banners->count() >= 2)
@php $easterBanner = $hero_banners[1]; @endphp
<section class="py-14 md:py-24 border-y border-amber-900/10" style="background: linear-gradient(135deg, {{ $easterBanner->bg_color ?? '#EDE6DC' }} 0%, #e8dfd0 100%);" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <div class="container mx-auto px-4 md:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div class="text-left {{ $isRtl ? 'lg:order-2 lg:text-right' : '' }}">
                <h2 class="text-2xl md:text-4xl font-semibold uppercase mb-4 tracking-[0.06em]" style="color: {{ $easterBanner->text_color ?? '#2c1810' }};">
                    {{ $lang == 'ar' && !empty($easterBanner->title_ar) ? $easterBanner->title_ar : ($easterBanner->title ?? '') }}
                </h2>
                <p class="text-lg mb-6 opacity-95" style="color: {{ $easterBanner->text_color ?? '#5c4a3a' }};">
                    {{ $lang == 'ar' && !empty($easterBanner->subtitle_ar) ? $easterBanner->subtitle_ar : ($easterBanner->subtitle ?? '') }}
                </p>
                @if(!empty($easterBanner->cta_link))
                <a href="{{ url($easterBanner->cta_link) }}" class="inline-block px-10 py-3.5 font-semibold uppercase tracking-[0.12em] text-white transition-all duration-200 hover:opacity-95" style="background: linear-gradient(135deg, {{ $ecommerce_setting->cta_bg_color ?? '#1a1512' }} 0%, #2c1810 100%);">
                    {{ $lang == 'ar' && !empty($easterBanner->cta_text_ar) ? $easterBanner->cta_text_ar : ($easterBanner->cta_text ?? 'SHOP NOW') }}
                </a>
                @endif
            </div>
            @if(!empty($easterBanner->image))
            <div class="{{ $isRtl ? 'lg:order-1' : '' }}">
                <img src="{{ url('frontend/images/hero/' . $easterBanner->image) }}" alt="" class="max-h-[350px] mx-auto object-contain" onerror="this.src='{{ $defaultImg }}'">
            </div>
            @else
            <div class="{{ $isRtl ? 'lg:order-1' : '' }}">
                <img src="{{ $defaultImg }}" alt="" class="max-h-[350px] mx-auto object-contain">
            </div>
            @endif
        </div>
    </div>
</section>
@endif

{{-- Widgets (from page builder) - products, categories, sliders --}}
@if(isset($widgets))
@foreach($widgets as $widget)
@if($widget->name == 'category-slider-widget')
@include('ecommerce::frontend.includes.category-slider-widget')
@endif
@if($widget->name == 'brand-slider-widget')
@include('ecommerce::frontend.includes.brand-slider-widget')
@endif
@if($widget->name == 'product-collection-widget')
@include('ecommerce::frontend.includes.product-collection-widget')
@endif
@if($widget->name == 'product-category-widget')
@include('ecommerce::frontend.includes.product-category-widget')
@endif
@if($widget->name == 'image-slider-widget')
@include('ecommerce::frontend.includes.image-slider-widget')
@endif
@if($widget->name == 'tab-product-category-widget')
@include('ecommerce::frontend.includes.tab-product-category-widget')
@endif
@if($widget->name == 'tab-product-collection-widget')
@include('ecommerce::frontend.includes.tab-product-collection-widget')
@endif
@endforeach
@endif

{{-- Recently Viewed --}}
@if(isset($recently_viewed) && count($recently_viewed) > 0)
@include('ecommerce::frontend.includes.recently-viewed-products')
@endif

@endsection

@section('script')
{{-- Global add-to-cart AJAX (no page refresh) --}}
<script>
$(document).on('click', '.add-to-cart', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    var parent = '#add_to_cart_' + id;
    var qty = $(parent + " input[name=qty]").val() || 1;
    var route = "{{ route('addToCart') }}";
    var btn = $(this), btnText = btn.html();
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span>');
    $.ajax({
        url: route,
        type: "POST",
        data: { _token: "{{ csrf_token() }}", product_id: id, qty: qty },
        success: function(response) {
            if (response) {
                $('.alert').addClass('alert-custom show');
                $('.alert-custom .message').html(response.success || 'Added to cart');
                $('.cart__menu .cart_qty').html(response.total_qty);
                var t = response.currency_code + ' ' + (response.subTotal * (response.currency_rate || 1)).toFixed(2);
                $('.cart__menu .total').html(t);
                setTimeout(function(){ $('.alert').removeClass('show'); }, 4000);
            }
            btn.prop('disabled', false).html(btnText);
        },
        error: function(){ btn.prop('disabled', false).html(btnText); }
    });
});
</script>
@if(isset($hero_banners) && $hero_banners->count() > 0)
<script>
(function(){
    var slides = document.querySelectorAll('.chocolat-hero-slide');
    var prev = document.querySelector('.chocolat-hero-prev');
    var next = document.querySelector('.chocolat-hero-next');
    var current = 0;
    var interval = 6000;

    function showSlide(i) {
        if (slides.length === 0) return;
        current = (i + slides.length) % slides.length;
        slides.forEach(function(s, k){ s.classList.toggle('active', k === current); });
    }

    if (prev) prev.onclick = function(){ showSlide(current - 1); resetTimer(); };
    if (next) next.onclick = function(){ showSlide(current + 1); resetTimer(); };

    var t;
    function resetTimer() {
        clearInterval(t);
        t = setInterval(function(){ showSlide(current + 1); }, interval);
    }
    t = setInterval(function(){ showSlide(current + 1); }, interval);

    // Inner image carousel (multi-image slides)
    document.querySelectorAll('.chocolat-hero-img-carousel').forEach(function(carousel) {
        var imgs = carousel.querySelectorAll('.chocolat-hero-img-slide');
        if (imgs.length <= 1) return;
        var idx = 0, intv = parseInt(carousel.dataset.interval) || 4000;
        setInterval(function() {
            imgs[idx].classList.remove('active');
            idx = (idx + 1) % imgs.length;
            imgs[idx].classList.add('active');
        }, intv);
    });
})();
</script>
@endif
@endsection
