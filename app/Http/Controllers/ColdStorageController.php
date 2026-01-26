<?php

namespace App\Http\Controllers;

use File;
use Exception;
use Keygen\Keygen;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\Brand;
use App\Models\ColdStorage;
use App\Models\Category;
use App\Traits\TenantInfo;
use App\Traits\CacheForget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Illuminate\Validation\Rule;

class ColdStorageController extends Controller
{
    use CacheForget;
    use TenantInfo;

    public function index(Request $request)
    {
        $role = Role::find(Auth::user()->role_id);
        if ($role->hasPermissionTo('coldstorages-index')) {
            $lims_brand_list = Brand::where('is_active', true)->get();
            $lims_category_list = Category::where('is_active', true)->get();
            $lims_unit_list = Unit::where('is_active', true)->get();
            $lims_tax_list = Tax::where('is_active', true)->get();

            $brand_id = 0;
            $category_id = 0;
            $unit_id = 0;
            $tax_id = 0;

            if ($request->input('brand_id')) $brand_id = $request->input('brand_id');
            if ($request->input('category_id')) $category_id = $request->input('category_id');
            if ($request->input('unit_id')) $unit_id = $request->input('unit_id');
            if ($request->input('tax_id')) $tax_id = $request->input('tax_id');

            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission)
                $all_permission[] = $permission->name;
            if (empty($all_permission))
                $all_permission[] = 'dummy text';
            $role_id = $role->id;
            $numberOfColdStorage = DB::table('cold_storages')->where('is_active', true)->count();

            return view('backend.coldstorage.index', compact('brand_id', 'category_id', 'unit_id', 'tax_id', 'all_permission', 'role_id', 'numberOfColdStorage', 'lims_brand_list', 'lims_category_list', 'lims_unit_list', 'lims_tax_list'));
        } else
            return redirect()->back()->with('not_permitted', __('db.Sorry! You are not allowed to access this module'));
    }

    public function coldStorageData(Request $request)
    {
        $columns = [
            1 => 'name',
            2 => 'code',
            3 => 'brand_id',
            4 => 'category_id',
            5 => 'qty',
            6 => 'unit_id',
            7 => 'price',
            8 => 'cost',
        ];

        $filtered_data = [
            'brand_id'     => $request->input('brand_id'),
            'category_id'  => $request->input('category_id'),
            'unit_id'      => $request->input('unit_id'),
            'tax_id'       => $request->input('tax_id'),
        ];

        $limit = ($request->input('length') != -1) ? $request->input('length') : null;
        $start = $request->input('start');
        $order = 'cold_storages.' . $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        $baseQuery = ColdStorage::with('category', 'brand', 'unit')
            ->where('cold_storages.is_active', true);

        if ($filtered_data['brand_id'] != '0') {
            $baseQuery->where('brand_id', $filtered_data['brand_id']);
        }
        if ($filtered_data['category_id'] != '0') {
            $baseQuery->where('category_id', $filtered_data['category_id']);
        }
        if ($filtered_data['unit_id'] != '0') {
            $baseQuery->where('unit_id', $filtered_data['unit_id']);
        }
        if ($filtered_data['tax_id'] != '0') {
            $baseQuery->where('tax_id', $filtered_data['tax_id']);
        }

        $totalData = $baseQuery->count();
        $totalFiltered = $totalData;

        if ($request->input('search.value')) {
            $search = $request->input('search.value');
            $baseQuery->where(function ($query) use ($search) {
                $query->where('cold_storages.name', 'LIKE', "%{$search}%")
                    ->orWhere('cold_storages.code', 'LIKE', "%{$search}%");
            });
            $totalFiltered = $baseQuery->count();
        }

        $cold_storages = $baseQuery->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = [];
        foreach ($cold_storages as $cold_storage) {
            $nestedData['id'] = $cold_storage->id;
            $nestedData['name'] = $cold_storage->name;
            $nestedData['code'] = $cold_storage->code;
            $nestedData['brand'] = $cold_storage->brand ? $cold_storage->brand->title : 'N/A';
            $nestedData['category'] = $cold_storage->category ? $cold_storage->category->name : 'N/A';
            $nestedData['qty'] = $cold_storage->qty ?? 0;
            $nestedData['unit'] = $cold_storage->unit ? $cold_storage->unit->unit_name : 'N/A';
            $nestedData['price'] = $cold_storage->price;
            $nestedData['cost'] = $cold_storage->cost;

            $nestedData['options'] = '<div class="btn-group">
                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' . __("db.action") . '
                  <span class="caret"></span>
                  <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">';

            if (in_array("coldstorages-edit", $request['all_permission']))
                $nestedData['options'] .= \Form::open(["route" => ["coldstorages.edit", $cold_storage->id], "method" => "GET"]) . '
                    <li>
                        <button type="submit" class="btn btn-link"><i class="dripicons-document-edit"></i> ' . __("db.edit") . '</button>
                    </li>' . \Form::close();

            if (in_array("coldstorages-delete", $request['all_permission']))
                $nestedData['options'] .= \Form::open(["route" => ["coldstorages.destroy", $cold_storage->id], "method" => "DELETE"]) . '
                    <li>
                    <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="fa fa-trash"></i> ' . __("db.delete") . '</button>
                    </li>' . \Form::close() . '
                </ul>
            </div>';

            $data[] = $nestedData;
        }

        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        ]);
    }

    public function create()
    {
        $role = Role::firstOrCreate(['id' => Auth::user()->role_id]);
        if ($role->hasPermissionTo('coldstorages-add')) {
            $lims_brand_list = Brand::where('is_active', true)->get();
            $lims_category_list = Category::where('is_active', true)->get();
            $lims_unit_list = Unit::where('is_active', true)->get();
            $lims_tax_list = Tax::where('is_active', true)->get();
            $numberOfColdStorage = ColdStorage::where('is_active', true)->count();

            return view('backend.coldstorage.create', compact('lims_brand_list', 'lims_category_list', 'lims_unit_list', 'lims_tax_list', 'numberOfColdStorage'));
        } else
            return redirect()->back()->with('not_permitted', __('db.Sorry! You are not allowed to access this module'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'code' => [
                'max:255',
                Rule::unique('cold_storages')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ]
        ]);

        $data = $request->except('image', 'file');
        $data['name'] = preg_replace('/[\n\r]/', "<br>", htmlspecialchars(trim($data['name']), ENT_QUOTES));
        if (isset($data['name_arabic'])) {
            $data['name_arabic'] = preg_replace('/[\n\r]/', "<br>", htmlspecialchars(trim($data['name_arabic']), ENT_QUOTES));
        }

        $data['product_details'] = str_replace('"', '@', $data['product_details'] ?? '');
        $data['is_active'] = true;
        $data['type'] = $data['type'] ?? 'standard';
        $data['barcode_symbology'] = $data['barcode_symbology'] ?? 'C128';

        $images = $request->file('image');
        $image_names = [];
        if ($images && is_array($images)) {
            if (!file_exists(public_path("images/coldstorage"))) {
                mkdir(public_path("images/coldstorage"), 0755, true);
            }

            foreach ($images as $key => $image) {
                if ($image && $image->isValid()) {
                    $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
                    $imageName = date("Ymdhis") . ($key + 1);

                    if (!config('database.connections.saleprosaas_landlord')) {
                        $imageName = $imageName . '.' . $ext;
                    } else {
                        $imageName = $this->getTenantId() . '_' . $imageName . '.' . $ext;
                    }

                    $image->move(public_path('images/coldstorage'), $imageName);
                    $image_names[] = $imageName;
                }
            }
            if (count($image_names) > 0) {
                $data['image'] = implode(",", $image_names);
            } else {
                $data['image'] = 'zummXD2dvAtI.png';
            }
        } else {
            $data['image'] = 'zummXD2dvAtI.png';
        }

        $file = $request->file;
        if ($file) {
            if (!file_exists(public_path("coldstorage/files"))) {
                mkdir(public_path("coldstorage/files"), 0755, true);
            }
            $ext = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $fileName = strtotime(date('Y-m-d H:i:s'));
            $fileName = $fileName . '.' . $ext;
            $file->move(public_path('coldstorage/files'), $fileName);
            $data['file'] = $fileName;
        }

        ColdStorage::create($data);
        \Session::flash('create_message', 'Cold Storage created successfully');
    }

    public function edit($id)
    {
        $role = Role::find(Auth::user()->role_id);
        if ($role->hasPermissionTo('coldstorages-edit')) {
            $lims_brand_list = Brand::where('is_active', true)->get();
            $lims_category_list = Category::where('is_active', true)->get();
            $lims_unit_list = Unit::where('is_active', true)->get();
            $lims_tax_list = Tax::where('is_active', true)->get();
            $lims_coldstorage_data = ColdStorage::where('id', $id)->first();

            return view('backend.coldstorage.edit', compact('lims_brand_list', 'lims_category_list', 'lims_unit_list', 'lims_tax_list', 'lims_coldstorage_data'));
        } else
            return redirect()->back()->with('not_permitted', __('db.Sorry! You are not allowed to access this module'));
    }

    public function update(Request $request)
    {
        if (!env('USER_VERIFIED')) {
            return redirect()->back()->with('not_permitted', __('db.This feature is disable for demo!'));
        }

        DB::beginTransaction();
        try {
            $this->validate($request, [
                'code' => [
                    'max:255',
                    Rule::unique('cold_storages')->ignore($request->input('id'))->where(function ($query) {
                        return $query->where('is_active', 1);
                    }),
                ]
            ]);

            $lims_coldstorage_data = ColdStorage::findOrFail($request->input('id'));
            $data = $request->except('image', 'file', 'prev_img');
            $data['name'] = htmlspecialchars(trim($data['name']), ENT_QUOTES);
            if (isset($data['name_arabic'])) {
                $data['name_arabic'] = htmlspecialchars(trim($data['name_arabic']), ENT_QUOTES);
            }

            $data['product_details'] = str_replace('"', '@', $data['product_details'] ?? '');

            $images = $request->file('image');
            if ($images && is_array($images) && count($images) > 0) {
                if (!file_exists(public_path("images/coldstorage"))) {
                    mkdir(public_path("images/coldstorage"), 0755, true);
                }

                if ($lims_coldstorage_data->image && $lims_coldstorage_data->image != 'zummXD2dvAtI.png') {
                    $old_images = explode(",", $lims_coldstorage_data->image);
                    foreach ($old_images as $old_image) {
                        if (file_exists(public_path('images/coldstorage/' . $old_image))) {
                            unlink(public_path('images/coldstorage/' . $old_image));
                        }
                    }
                }

                $image_names = [];
                foreach ($images as $key => $image) {
                    if ($image && $image->isValid()) {
                        $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
                        $imageName = date("Ymdhis") . ($key + 1);

                        if (!config('database.connections.saleprosaas_landlord')) {
                            $imageName = $imageName . '.' . $ext;
                        } else {
                            $imageName = $this->getTenantId() . '_' . $imageName . '.' . $ext;
                        }

                        $image->move(public_path('images/coldstorage'), $imageName);
                        $image_names[] = $imageName;
                    }
                }
                if (count($image_names) > 0) {
                    $data['image'] = implode(",", $image_names);
                } else {
                    $data['image'] = $lims_coldstorage_data->image;
                }
            } else {
                $data['image'] = $lims_coldstorage_data->image;
            }

            $file = $request->file;
            if ($file) {
                if (!file_exists(public_path("coldstorage/files"))) {
                    mkdir(public_path("coldstorage/files"), 0755, true);
                }
                if ($lims_coldstorage_data->file && file_exists(public_path('coldstorage/files/' . $lims_coldstorage_data->file))) {
                    unlink(public_path('coldstorage/files/' . $lims_coldstorage_data->file));
                }
                $ext = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                $fileName = strtotime(date('Y-m-d H:i:s'));
                $fileName = $fileName . '.' . $ext;
                $file->move(public_path('coldstorage/files'), $fileName);
                $data['file'] = $fileName;
            }

            $lims_coldstorage_data->update($data);
            DB::commit();
            \Session::flash('edit_message', 'Cold Storage updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('not_permitted', __('db.Failed to update cold storage. Please try again'));
        }
    }

    public function deleteBySelection(Request $request)
    {
        $coldstorage_id = $request['coldstorageIdArray'];
        foreach ($coldstorage_id as $id) {
            $lims_coldstorage_data = ColdStorage::findOrFail($id);
            $lims_coldstorage_data->is_active = false;
            $lims_coldstorage_data->save();
        }
        return 'Cold Storage deleted successfully!';
    }

    public function destroy($id)
    {
        if (!env('USER_VERIFIED')) {
            return redirect()->back()->with('not_permitted', __('db.This feature is disable for demo!'));
        } else {
            $lims_coldstorage_data = ColdStorage::findOrFail($id);
            $lims_coldstorage_data->is_active = false;
            $lims_coldstorage_data->save();
            return redirect()->back()->with('message', __('db.Cold Storage deleted successfully'));
        }
    }

    public function generateCode()
    {
        $id = Keygen::numeric(8)->generate();
        return $id;
    }
}
