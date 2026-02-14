<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class HomepageHeroBannerController extends Controller
{
    public function index()
    {
        $banners = DB::table('homepage_hero_banners')->orderBy('order')->get();
        return view('ecommerce::backend.homepage-banners.index', compact('banners'));
    }

    public function create()
    {
        return view('ecommerce::backend.homepage-banners.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateAndPrepare($request);
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'));
        }
        $data['created_at'] = now();
        DB::table('homepage_hero_banners')->insert($data);
        return redirect()->route('homepage-banners.index')->with('message', __('db.Saved successfully'));
    }

    public function edit($id)
    {
        $banner = DB::table('homepage_hero_banners')->where('id', $id)->first();
        if (!$banner) abort(404);
        return view('ecommerce::backend.homepage-banners.edit', compact('banner'));
    }

    public function update(Request $request, $id)
    {
        $data = $this->validateAndPrepare($request);
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'));
        }
        DB::table('homepage_hero_banners')->where('id', $id)->update($data);
        return redirect()->route('homepage-banners.index')->with('message', __('db.Updated successfully'));
    }

    public function destroy($id)
    {
        DB::table('homepage_hero_banners')->where('id', $id)->delete();
        return redirect()->route('homepage-banners.index')->with('message', __('db.Deleted successfully'));
    }

    private function validateAndPrepare(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'subtitle_ar' => 'nullable|string',
            'cta_text' => 'nullable|string|max:100',
            'cta_text_ar' => 'nullable|string|max:100',
            'cta_link' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'slider_type' => 'nullable|in:image,video',
            'video_url' => 'nullable|string|max:500',
            'bg_color' => 'nullable|string|max:20',
            'text_color' => 'nullable|string|max:20',
            'order' => 'nullable|integer',
            'status' => 'nullable|in:0,1',
        ]);

        $data = [
            'title' => $request->title,
            'title_ar' => $request->title_ar,
            'subtitle' => $request->subtitle,
            'subtitle_ar' => $request->subtitle_ar,
            'cta_text' => $request->cta_text ?? 'SHOP NOW',
            'cta_text_ar' => $request->cta_text_ar ?? 'تسوق الآن',
            'cta_link' => $request->cta_link,
            'slider_type' => $request->slider_type ?? 'image',
            'video_url' => $request->video_url,
            'bg_color' => $request->bg_color ?? '#8B1538',
            'text_color' => $request->text_color ?? '#FFFFFF',
            'order' => (int) ($request->order ?? 0),
            'status' => (int) ($request->status ?? 1),
            'updated_at' => now(),
        ];

        if (!empty($request->images_json)) {
            $decoded = json_decode($request->images_json, true);
            $data['images'] = is_array($decoded) ? json_encode($decoded) : null;
        }

        return $data;
    }

    private function uploadImage($file)
    {
        $ext = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        $imageName = 'hero-' . date('YmdHis') . '.' . $ext;
        $file->move(public_path('frontend/images/hero/'), $imageName);
        return $imageName;
    }
}
