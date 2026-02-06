@extends('backend.layout.main')

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{__('db.Edit Production')}}</h4>
                    </div>
                    <div class="card-body">
                        @include('includes.session_message')
                        <form method="post" action="{{ route('productions.update', $lims_production_data->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('db.date')}}</label>
                                        <input type="text" name="created_at" class="form-control date" value="{{ $lims_production_data->created_at ? $lims_production_data->created_at->format('d-m-Y') : '' }}" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('db.Production Warehouse')}}</label>
                                        <select id="warehouse_id" name="warehouse_id" class="selectpicker form-control" data-live-search="true">
                                            @foreach($lims_warehouse_list as $warehouse)
                                            <option value="{{$warehouse->id}}" @if($lims_production_data->warehouse_id == $warehouse->id) selected @endif>{{$warehouse->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Batch/Lot Number</label>
                                        <input type="text" class="form-control" readonly value="{{ $lims_production_data->batch_lot_number ?? '-' }}" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('db.Select Recipe')}}</label>
                                        <select id="selectRecipe" name="product_id" class="selectpicker form-control" data-live-search="true" title="Select Recipe..." disabled>
                                            @foreach($lims_product_list as $product)
                                                <option value="{{$product->id}}" @if($lims_production_data->product_id == $product->id) selected @endif>{{$product->name}}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">({{ __('db.Read only') }})</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('db.Total Qty')}}</label>
                                        <input type="number" class="form-control total_qty" readonly value="{{ $lims_production_data->total_qty }}" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Expiry Date</label>
                                        <input type="text" name="expiry_date" id="expiry_date" class="form-control" value="{{ $lims_production_data->expiry_date ? \Carbon\Carbon::parse($lims_production_data->expiry_date)->format('d-m-Y') : '' }}" placeholder="dd-mm-yyyy" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('db.Attach Document')}}</label>
                                        <input type="file" name="document" class="form-control" />
                                        @if($lims_production_data->document)
                                        <small class="text-muted">Current: <a href="{{ url('documents/production/'.$lims_production_data->document) }}" target="_blank">{{ $lims_production_data->document }}</a></small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-12 mb-1">
                                    <label>{{__('db.Ingredient List')}}</label>
                                    <div class="table-responsive">
                                        <table id="myTable" class="table table-hover order-list">
                                            <thead>
                                                <tr>
                                                    <th>{{__('db.product')}}</th>
                                                    <th>{{__('db.Wastage Percent')}}</th>
                                                    <th>{{__('db.Quantity')}}</th>
                                                    <th>{{__('db.Final Quantity')}}</th>
                                                    <th>{{__('db.Unit Price')}}</th>
                                                    <th>{{__('db.Sub total')}}</th>
                                                    <th><i class="dripicons-trash"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody class="combo_product_list_table" id="ingredients-table">
                                                @include('manufacturing::production.ingredients_edit', ['production' => $lims_production_data, 'warehouse_id' => $lims_production_data->warehouse_id])
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Production Overhead Type</label>
                                        <select name="production_overhead_type" id="production_overhead_type" class="form-control">
                                            <option value="fixed" @if(($lims_production_data->production_overhead_type ?? 'fixed') != 'percent') selected @endif>Fixed Amount</option>
                                            <option value="percent" @if(($lims_production_data->production_overhead_type ?? '') == 'percent') selected @endif>Percentage (%)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('db.Production Cost')}}</label>
                                        <input type="number" name="production_cost" id="production_cost_input" class="form-control production_cost" value="{{ ($lims_production_data->production_overhead_type ?? 'fixed') == 'percent' ? ($lims_production_data->production_overhead_cost ?? 0) : ($lims_production_data->production_cost ?? 0) }}" min="0" step="any" placeholder="Enter amount or %" />
                                        <small class="text-muted production-cost-hint">{{ ($lims_production_data->production_overhead_type ?? 'fixed') == 'percent' ? 'Enter percentage (e.g. 10 for 10%)' : 'Enter fixed amount' }}</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('db.Shipping Cost')}}</label>
                                        <input type="number" name="shipping_cost" class="form-control shipping_cost" value="{{ $lims_production_data->shipping_cost ?? 0 }}" step="any" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{__('db.Note')}}</label>
                                        <textarea rows="4" class="form-control" name="note">{{ $lims_production_data->note }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">{{__('db.update')}}</button>
                                        <a href="{{ route('productions.index') }}" class="btn btn-secondary">{{__('db.Cancel')}}</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <table class="table table-bordered table-condensed totals">
            <td><strong>{{__('db.Production Cost')}}</strong>
                <span class="pull-right" id="production_cost_display">{{ number_format($lims_production_data->production_cost ?? 0, $general_setting->decimal ?? 2, '.', '') }}</span>
            </td>
            <td><strong>{{__('db.Shipping Cost')}}</strong>
                <span class="pull-right" id="shipping_cost_display">{{ number_format($lims_production_data->shipping_cost ?? 0, $general_setting->decimal ?? 2, '.', '') }}</span>
            </td>
            <td><strong>{{__('db.Total')}}</strong>
                <span class="pull-right" id="total_display">{{ number_format($lims_production_data->total_cost ?? 0, $general_setting->decimal ?? 2, '.', '') }}</span>
            </td>
            <td><strong>{{__('db.grand total')}}</strong>
                <span class="pull-right" id="grand_total_display">{{ number_format($lims_production_data->grand_total ?? 0, $general_setting->decimal ?? 2, '.', '') }}</span>
            </td>
        </table>
    </div>
</section>
@endsection

@push('scripts')
<script>
    var editTotalCost = {{ $lims_production_data->total_cost ?? 0 }};

    function calculate_price_edit() {
        var production_cost_input = parseFloat($('.production_cost').val()) || 0;
        var shipping_cost = parseFloat($('.shipping_cost').val()) || 0;
        var overhead_type = $('#production_overhead_type').val();
        var production_cost = 0;
        if (overhead_type === 'fixed') {
            production_cost = production_cost_input;
        } else if (overhead_type === 'percent') {
            production_cost = (editTotalCost * production_cost_input) / 100;
        }
        var grand_total = editTotalCost + production_cost + shipping_cost;

        $('#shipping_cost_display').html(shipping_cost.toFixed(2));
        $('#production_cost_display').html(production_cost.toFixed(2));
        $('#total_display').html(editTotalCost.toFixed(2));
        $('#grand_total_display').html(grand_total.toFixed(2));
    }

    $('.date').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true
    });
    $('#expiry_date').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true,
        startDate: new Date()
    });
    $('#production_overhead_type').on('change', function(){
        var type = $(this).val();
        if (type === 'percent') {
            $('.production-cost-hint').text('Enter percentage (e.g. 10 for 10%)');
            $('#production_cost_input').attr('placeholder', 'Enter %');
        } else {
            $('.production-cost-hint').text('Enter fixed amount');
            $('#production_cost_input').attr('placeholder', 'Enter amount');
        }
        calculate_price_edit();
    });
    $('.production_cost, .shipping_cost').on('input change', function(){
        calculate_price_edit();
    });
    $('.selectpicker').selectpicker('refresh');
    calculate_price_edit();
</script>
@endpush
