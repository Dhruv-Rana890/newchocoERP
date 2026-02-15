<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subcategory\StoreSubcategoryRequest;
use App\Http\Requests\Subcategory\UpdateSubcategoryRequest;
use App\Models\Category;
use App\Models\Subcategory;
use App\Traits\FileHandleTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class SubcategoryController extends Controller
{
    use FileHandleTrait;

    /**
     * Get only product categories (for dropdown and relation).
     */
    protected function getProductCategories()
    {
        return Category::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('type')->orWhere('type', 'product');
            })
            ->orderBy('name')
            ->get();
    }

    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if (!$role->hasPermissionTo('category')) {
            return redirect()->back()->with('not_permitted', __('db.Sorry! You are not allowed to access this module'));
        }
        $categories_list = $this->getProductCategories();
        return view('backend.subcategory.index', compact('categories_list'));
    }

    public function subcategoryData(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'category_id',
            2 => 'name_english',
            3 => 'name_arabic',
            4 => 'slug',
            5 => 'sort_order',
        ];

        $totalData = Subcategory::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length') != -1 ? $request->input('length') : $totalData;
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')] ?? 'id';
        $dir = $request->input('order.0.dir') ?? 'asc';

        if (empty($request->input('search.value'))) {
            $subcategories = Subcategory::with('category')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $subcategories = Subcategory::with('category')
                ->where(function ($q) use ($search) {
                    $q->where('name_english', 'LIKE', "%{$search}%")
                        ->orWhere('name_arabic', 'LIKE', "%{$search}%")
                        ->orWhere('slug', 'LIKE', "%{$search}%")
                        ->orWhereHas('category', function ($cq) use ($search) {
                            $cq->where('name', 'LIKE', "%{$search}%");
                        });
                })
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Subcategory::where(function ($q) use ($search) {
                $q->where('name_english', 'LIKE', "%{$search}%")
                    ->orWhere('name_arabic', 'LIKE', "%{$search}%")
                    ->orWhere('slug', 'LIKE', "%{$search}%")
                    ->orWhereHas('category', function ($cq) use ($search) {
                        $cq->where('name', 'LIKE', "%{$search}%");
                    });
            })->count();
        }

        $data = [];
        foreach ($subcategories as $key => $sub) {
            $img = $sub->image ? url('images/subcategory', $sub->image) : url('images/zummXD2dvAtI.png');
            $nestedData['id'] = $sub->id;
            $nestedData['key'] = $key;
            $nestedData['name_english'] = '<img src="' . $img . '" height="40" width="40" class="rounded"> ' . e($sub->name_english);
            $nestedData['name_arabic'] = e($sub->name_arabic ?? '—');
            $nestedData['category_name'] = $sub->category ? e($sub->category->name) : '—';
            $nestedData['slug'] = e($sub->slug ?? '—');
            $nestedData['sort_order'] = (int) $sub->sort_order;
            $destroyUrl = route('subcategory.destroy', $sub->id);
            $nestedData['options'] = '<div class="btn-group">
                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' . __("db.action") . '
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                    <li>
                        <button type="button" data-id="' . $sub->id . '" class="open-EditSubcategoryDialog btn btn-link" data-toggle="modal" data-target="#editSubcategoryModal"><i class="dripicons-document-edit"></i> ' . __("db.edit") . '</button>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <form method="POST" action="' . e($destroyUrl) . '" class="d-inline" onsubmit="return confirm(\'Delete this subcategory?\');">
                            <input type="hidden" name="_token" value="' . csrf_token() . '">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-link"><i class="dripicons-trash"></i> ' . __("db.delete") . '</button>
                        </form>
                    </li>
                </ul>
            </div>';
            $data[] = $nestedData;
        }

        $json_data = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
        ];
        return response()->json($json_data);
    }

    public function store(StoreSubcategoryRequest $request)
    {
        if (!env('USER_VERIFIED')) {
            return response()->json(['message' => __('db.This feature is disable for demo!')], 403);
        }

        $data = $request->validated();
        $data['sort_order'] = (int) ($request->sort_order ?? 0);
        $data['slug'] = $request->slug ? Str::slug($request->slug, '-') : Str::slug($request->name_english, '-');

        foreach (['subcate_banner_img', 'image'] as $field) {
            $file = $request->file($field);
            if ($file) {
                $dir = $field === 'subcate_banner_img' ? 'images/subcategory/banner' : 'images/subcategory';
                if (!file_exists(public_path($dir))) {
                    mkdir(public_path($dir), 0755, true);
                }
                $ext = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                $name = date('Ymdhis') . '.' . $ext;
                $file->move(public_path($dir), $name);
                $data[$field] = $name;
            }
        }

        Subcategory::create($data);
        return response()->json(['message' => __('db.Subcategory inserted successfully')]);
    }

    public function edit($id)
    {
        $subcategory = Subcategory::with('category')->find($id);
        if (!$subcategory) {
            return response()->json(['error' => 'Subcategory not found'], 404);
        }
        return response()->json($subcategory);
    }

    public function update(UpdateSubcategoryRequest $request, $id)
    {
        if (!env('USER_VERIFIED')) {
            return redirect()->back()->with('not_permitted', __('db.This feature is disable for demo!'));
        }

        $subcategory = Subcategory::find($id);
        if (!$subcategory) {
            return redirect()->back()->with('not_permitted', __('Subcategory not found'));
        }

        $data = $request->except('_token', '_method', 'subcate_banner_img', 'image');
        $data['sort_order'] = (int) ($request->sort_order ?? 0);
        $data['slug'] = $request->slug ? Str::slug($request->slug, '-') : Str::slug($request->name_english, '-');

        if ($request->hasFile('subcate_banner_img')) {
            $dir = public_path('images/subcategory/banner');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $this->fileDelete($dir . '/', $subcategory->subcate_banner_img);
            $file = $request->file('subcate_banner_img');
            $ext = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $name = date('Ymdhis') . '.' . $ext;
            $file->move($dir, $name);
            $data['subcate_banner_img'] = $name;
        }

        if ($request->hasFile('image')) {
            $dir = public_path('images/subcategory');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $this->fileDelete($dir . '/', $subcategory->image);
            $file = $request->file('image');
            $ext = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $name = date('Ymdhis') . '.' . $ext;
            $file->move($dir, $name);
            $data['image'] = $name;
        }

        $subcategory->update($data);
        return redirect()->route('subcategory.index')->with('message', __('db.Subcategory updated successfully'));
    }

    public function destroy($id)
    {
        $subcategory = Subcategory::find($id);
        if (!$subcategory) {
            return redirect()->back()->with('not_permitted', __('Subcategory not found'));
        }
        $dirBanner = public_path('images/subcategory/banner');
        $dirImg = public_path('images/subcategory');
        $this->fileDelete($dirBanner . '/', $subcategory->subcate_banner_img);
        $this->fileDelete($dirImg . '/', $subcategory->image);
        $subcategory->delete();
        return redirect()->route('subcategory.index')->with('message', __('Subcategory deleted successfully'));
    }
}
