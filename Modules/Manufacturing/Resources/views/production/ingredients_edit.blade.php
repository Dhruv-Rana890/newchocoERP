<?php
$product_list = array_filter(array_map('trim', explode(',', $production->product_list ?? '')));
$wastage_percent = explode(',', $production->wastage_percent ?? '');
$qty_list = explode(',', $production->qty_list ?? '');
$price_list = explode(',', $production->price_list ?? '');
$production_units_ids = explode(',', $production->production_units_ids ?? '');
$variant_list = $production->variant_list ? explode(',', $production->variant_list) : [];
$is_raw_material_list = explode(',', $production->is_raw_material_list ?? '');
?>
@foreach ($product_list as $key => $id)
    @php
        $id = trim($id);
        if ($id === '') continue;
        $is_raw = isset($is_raw_material_list[$key]) && trim($is_raw_material_list[$key]) == '1';
        $name = null;
        $code = '';
        $unit = null;
        $combo_unit = collect();
        $selected_unit_id = null;
        $variant_list_key = isset($variant_list[$key]) ? $variant_list[$key] : '';
        $qty_val = isset($qty_list[$key]) ? $qty_list[$key] : 1;
        $wastage_val = isset($wastage_percent[$key]) ? $wastage_percent[$key] : 0;
        $price_val = isset($price_list[$key]) ? $price_list[$key] : 0;
        $stock = 0;
        if ($is_raw) {
            $rawMaterial = \App\Models\RawMaterial::find($id);
            if (!$rawMaterial) continue;
            $name = $rawMaterial->name;
            $code = $rawMaterial->code;
            $stock = $rawMaterial->qty ?? 0;
            $unit = \App\Models\Unit::where('id', $rawMaterial->unit_id)->first();
            $combo_unit = \App\Models\Unit::query()->where('id', $rawMaterial->unit_id)->orWhere('base_unit', $rawMaterial->unit_id)->get()->unique('id');
            $selected_unit_id = isset($production_units_ids[$key]) ? trim($production_units_ids[$key]) : $rawMaterial->unit_id;
        } else {
            $product = \App\Models\Product::find($id);
            if (!$product) continue;
            $pw = \App\Models\Product_Warehouse::where([['product_id', $product->id], ['warehouse_id', $warehouse_id]])->latest()->first();
            $stock = $pw->qty ?? 0;
            $combo_unit = \App\Models\Unit::query()->where('id', $product->unit_id)->orWhere('base_unit', $product->unit_id)->get()->unique('id');
            $unit = \App\Models\Unit::query()->where('id', $product->unit_id)->first();
            if ($variant_list_key && $product->variant_list) {
                $pv = \App\Models\ProductVariant::select('item_code')->FindExactProduct($id, $variant_list_key)->first();
                if ($pv) $product->code = $pv->item_code;
            }
            $name = $product->name;
            $code = $product->code ?? '';
            $selected_unit_id = isset($production_units_ids[$key]) ? trim($production_units_ids[$key]) : $product->unit_id;
        }
    @endphp
    @if ($name)
    <tr>
        <td>{{ $name }} [{{ $code }}]</td>
        <td>
            <div class="input-group">
                <input type="number" class="form-control wastage_percent" name="wastage_percent[]" value="{{ $wastage_val }}" min="0" step="any" readonly />
                <div class="input-group-append">
                    <span class="input-group-text">%</span>
                </div>
            </div>
        </td>
        <td>
            <div class="input-group" style="max-width: unset">
                <input type="number" class="form-control qty" name="product_qty[]" data-qty="{{ $qty_val }}" value="{{ $qty_val }}" step="any" placeholder="Qty" data-stock="{{ $stock }}" readonly>
                <div class="input-group-append">
                    <select name="production_unit_ids[]" style="width: 112px;" class="btn btn-outline-secondary form-control production_unit_ids" readonly disabled>
                        @foreach ($combo_unit as $row)
                            <option value="{{ $row->id }}" data-operation_value="{{ $row->operation_value }}" data-unit_name="{{ $row->unit_name }}" data-operator="{{ $row->operator }}" @if ($selected_unit_id == $row->id) selected @endif>{{ $row->unit_name }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="stock_list[]" value="{{ $stock }}">
                <span class="text-danger qty-error"></span>
            </div>
        </td>
        <td><input type="text" class="form-control unit_name" disabled value="{{ $qty_val }} ({{ $unit ? $unit->unit_name : '' }})" /></td>
        <td><input type="number" class="form-control unit_price" name="unit_price[]" value="{{ $price_val }}" step="any" readonly /></td>
        <td><input type="number" class="form-control subtotal" name="subtotal[]" value="{{ number_format((float)($qty_val ?? 0) * (float)($price_val ?? 0), 2, '.', '') }}" step="any" readonly /></td>
        <td><span class="text-muted">—</span></td>
        <input type="hidden" class="product-id" name="product_list[]" value="{{ $id }}" />
        <input type="hidden" name="is_raw_material[]" value="{{ $is_raw ? '1' : '0' }}" />
        <input type="hidden" class="variant-id" name="variant_id[]" value="{{ $variant_list_key }}" />
    </tr>
    @endif
@endforeach
