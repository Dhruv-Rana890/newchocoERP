@extends('backend.layout.main')
@section('content')

<style type="text/css">
    .btn-icon i {
        margin-right: 5px
    }

    .top-fields {
        margin-top: 10px;
        position: relative;
    }

    .top-fields label {
        background: #FFF;
        font-size: 11px;
        font-weight: 600;
        margin-left: 10px;
        padding: 0 3px;
        position: absolute;
        top: -8px;
        z-index: 9;
    }

    .top-fields input {
        font-size: 13px;
        height: 45px
    }
</style>

    <x-success-message key="create_message" />
    <x-error-message key="not_permitted" />
    <x-error-message key="message" />

    <section>
        <div class="container-fluid">

            @can('rawmaterials-add')
                <a href="{{ route('rawmaterials.create') }}" class="btn btn-info add-rawmaterial-btn btn-icon"><i
                        class="dripicons-plus"></i> {{ __('db.add_raw_material') }}</a>
            @endcan

            <button type="button" class="btn btn-warning btn-icon" id="toggle-filter">
                <i class="dripicons-experiment"></i> {{ __('db.Filter Raw Materials') }}
            </button>

            <div class="card mt-3 mb-2">
                <div class="card-body" id="filter-card" style="display: none;">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group top-fields">
                                <label>{{ __('db.Brand') }}</label>
                                <select name="brand_id" required class="form-control selectpicker" id="brand_id"
                                    data-live-search="true" data-live-search-style="begins">
                                    <option value="0" selected>All Brands</option>
                                    @foreach ($lims_brand_list as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group top-fields">
                                <label>{{ __('db.category') }}</label>
                                <select name="category_id" required class="form-control selectpicker" id="category_id"
                                    data-live-search="true" data-live-search-style="begins">
                                    <option value="0" selected>All Categories</option>
                                    @foreach ($lims_category_list as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group top-fields">
                                <label>{{ __('db.Unit') }}</label>
                                <select name="unit_id" required class="form-control selectpicker" id="unit_id"
                                    data-live-search="true" data-live-search-style="begins">
                                    <option value="0" selected>All Unit</option>
                                    @foreach ($lims_unit_list as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group top-fields">
                                <label>{{ __('db.Tax') }}</label>
                                <select name="tax_id" required class="form-control selectpicker" id="tax_id"
                                    data-live-search="true" data-live-search-style="begins">
                                    <option value="0" selected>All Tax</option>
                                    @foreach ($lims_tax_list as $tax)
                                        <option value="{{ $tax->id }}">{{ $tax->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table id="rawmaterial-data-table" class="table pt-0" style="width: 100%">
                <thead>
                    <tr>
                        <th class="not-exported"></th>
                        <th>{{ __('db.name') }}</th>
                        <th>{{ __('db.Code') }}</th>
                        <th>{{ __('db.Brand') }}</th>
                        <th>{{ __('db.category') }}</th>
                        <th>{{ __('db.Quantity') }}</th>
                        <th>{{ __('db.Unit') }}</th>
                        <th>{{ __('db.Price') }}</th>
                        @if ($role_id <= 2)
                            <th>{{ __('db.Cost') }}</th>
                        @endif
                        <th class="not-exported">{{ __('db.action') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>

@endsection
@push('scripts')
    <script>
        $("ul#rawmaterial").siblings('a').attr('aria-expanded', 'true');
        $("ul#rawmaterial").addClass("show");
        $("ul#rawmaterial #rawmaterial-list-menu").addClass("active");

        function confirmDelete() {
            if (confirm("Are you sure want to delete?")) {
                return true;
            }
            return false;
        }

        var role_id = <?php echo json_encode($role_id); ?>;
        var columns = [{
            "data": "key"
        }, {
            "data": "name"
        }, {
            "data": "code"
        }, {
            "data": "brand"
        }, {
            "data": "category"
        }, {
            "data": "qty"
        }, {
            "data": "unit"
        }, {
            "data": "price"
        }];
        if (role_id <= 2) {
            columns.push({
                "data": "cost"
            });
        }
        columns.push({
            "data": "options"
        });

        var all_permission = <?php echo json_encode($all_permission); ?>;
        var user_verified = <?php echo json_encode(env('USER_VERIFIED')); ?>;
        var brand_id = <?php echo json_encode($brand_id); ?>;
        var category_id = <?php echo json_encode($category_id); ?>;
        var unit_id = <?php echo json_encode($unit_id); ?>;
        var tax_id = <?php echo json_encode($tax_id); ?>;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#brand_id").val(brand_id);
        $("#category_id").val(category_id);
        $("#unit_id").val(unit_id);
        $("#tax_id").val(tax_id);

        $('#toggle-filter').on('click', function() {
            $('#filter-card').slideToggle('slow');
        });

        var rawmaterial_id = [];
        let buttons = [];

        buttons.push([{
            extend: 'colvis',
            text: '<i title="column visibility" class="fa fa-eye"></i>',
            columns: ':gt(0)'
        }, ]);

        $(document).ready(function() {
            var table = $('#rawmaterial-data-table').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('rawmaterials.rawmaterial-data') }}",
                    data: function(d) {
                        d.all_permission = all_permission;
                        d.brand_id = $('#brand_id').val();
                        d.category_id = $('#category_id').val();
                        d.unit_id = $('#unit_id').val();
                        d.tax_id = $('#tax_id').val();
                    },
                    type: "post"
                },
                columns: columns,
                columnDefs: [{
                    orderable: false,
                    targets: [0, columns.length - 1]
                }, {
                    render: function(data, type, row, meta) {
                        if (type === 'display') {
                            data =
                                '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                        }
                        return data;
                    },
                    checkboxes: {
                        selectRow: true,
                        selectAllRender:
                            '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                    },
                    targets: [0]
                }],
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
                order: [['1', 'asc']],
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                dom: '<"row"lfB>rtip',
                buttons: buttons,
            });

            $('#brand_id, #category_id, #unit_id, #tax_id').on('change', function() {
                table.ajax.reload();
            });
        });
    </script>
@endpush
