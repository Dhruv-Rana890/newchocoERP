@extends('backend.layout.main') @section('content')

@push('css')
<style>
.switch { position: relative; display: inline-block; width: 36px; height: 20px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .3s; border-radius: 20px; }
.slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,.3); }
input:checked + .slider { background-color: #28a745; }
input:checked + .slider:before { transform: translateX(16px); }
.slider.round { border-radius: 20px; }
.slider.round:before { border-radius: 50%; }
#menu-categories-sortable { list-style: none; padding: 0; margin: 0; }
#menu-categories-sortable .list-group-item { cursor: move; display: flex; align-items: center; padding: 0.65rem 0.85rem; }
#menu-categories-sortable .list-group-item .drag-handle { color: #6c757d; margin-right: 0.5rem; }
#menu-categories-sortable .list-group-item.ui-sortable-helper { box-shadow: 0 4px 12px rgba(0,0,0,.15); }
</style>
@endpush


<x-success-message key="message" />
<x-error-message key="not_permitted" />

<section>
    <div class="container-fluid">
        <!-- Trigger the modal with a button -->
         @can('categories-add')
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#category-modal"><i class="dripicons-plus"></i> {{__("db.Add Category")}}</button>&nbsp;
        @endcan
        @can('categories-import')
            <button class="btn btn-primary" data-toggle="modal" data-target="#importCategory"><i class="dripicons-copy"></i> {{__('db.Import Category')}}</button>
        @endcan
        @if(in_array('ecommerce', explode(',', $general_setting->modules ?? '')) && \Schema::hasColumn('categories', 'show_in_menu'))
            <button type="button" class="btn btn-outline-secondary" id="btn-arrange-navbar-menu" data-toggle="modal" data-target="#arrangeMenuModal"><i class="dripicons-move"></i> {{ __('Arrange navbar menu') }}</button>
        @endif
    </div>
    <div class="table-responsive">
        <table id="category-table" class="table" style="width: 100%">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{__('db.category')}}</th>
                    <th>{{__('db.Parent Category')}}</th>
                    <th class="not-exported">{{ __('Show in navbar') }}</th>
                    <th>{{__('db.Number of Product')}}</th>
                    <th>{{__('db.Stock Quantity')}}</th>
                    <th>{{__('db.Stock Worth') . '(' . __('db.Price') . '/' . __('db.Cost') . ')'}}</th>
                    <th class="not-exported">{{__('db.action')}}</th>
                </tr>
            </thead>
        </table>
    </div>
</section>

<!-- Edit Modal -->
<div id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog">
    <div class="modal-content">
        <!-- {{ Form::open(['route' => ['category.update', 1], 'method' => 'PUT', 'files' => true] ) }} -->
        {{ Form::open(['url' => '', 'method' => 'PUT', 'files' => true, 'id' => 'editCategoryForm']) }}
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title">{{__('db.Update Category')}}</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
      </div>
      <div class="modal-body">
        <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}.</small></p>
        <div class="row">
            <div class="col-md-6 form-group">
                <label>{{__('db.name')}} *</label>
                {{Form::text('name',null, array('required' => 'required', 'class' => 'form-control'))}}
                <x-validation-error fieldName="name" />
            </div>
            <input type="hidden" name="category_id">
            <div class="col-md-6 form-group">
                <label>{{__('db.Image')}}</label>
                <input type="file" name="image" class="form-control">
                <x-validation-error fieldName="image" />
            </div>
            <div class="col-md-6 form-group">
                <label>{{__('db.Parent Category')}}</label>
                <select name="parent_id" class="form-control selectpicker" id="parent">
                    <option value="">No {{__('db.parent')}}</option>
                    @foreach($categories_list as $category)
                    <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                </select>
                <x-validation-error fieldName="parent_id" />
            </div>
            @if (\Schema::hasColumn('categories', 'woocommerce_category_id'))
            <div class="col-md-6 form-group mt-4">
                <h5><input name="is_sync_disable" type="checkbox" id="is_sync_disable" value="1">&nbsp; {{__('db.Disable Woocommerce Sync')}}</h5>
                <x-validation-error fieldName="is_sync_disable" />
            </div>
            @endif
            @if(in_array('restaurant',explode(',',$general_setting->modules)))
            <div class="col-md-12 mt-3">
                <h6><strong>{{ __('For Website') }}</strong></h6>
                <hr>
            </div>
            <div class="col-md-12 form-group">
                <br>
                <input type="checkbox" name="featured" id="featured" value="1"> <label>{{ __('List on website') }}</label>
            </div>
            @endif
            @if(in_array('ecommerce',explode(',',$general_setting->modules)))
            <div class="col-md-12 mt-3">
                <h6><strong>{{ __('For Website') }}</strong></h6>
                <hr>
            </div>

            <div class="col-md-6 form-group">
                <label>{{ __('Icon') }}</label>
                <input type="file" name="icon" class="form-control">
            </div> 
            <div class="col-md-6 form-group">
                <br>
                <input type="checkbox" name="featured" id="featured" value="1"> <label>{{ __('List on category dropdown') }}</label>
            </div>
            <div class="col-md-6 form-group d-flex align-items-center">
                <label class="switch mb-0 mr-2"><input type="checkbox" name="show_in_menu" id="show_in_menu" value="1"><span class="slider round"></span></label>
                <label for="show_in_menu" class="mb-0">{{ __('Show in website navbar') }}</label>
            </div>
            @endif
        </div>
        @if(in_array('ecommerce',explode(',',$general_setting->modules)))
        <div class="row">
            <div class="col-md-12 mt-3">
                <h6><strong>{{ __('For SEO') }}</strong></h6>
                <hr>
            </div>
            <div class="col-md-12 form-group">
                <label>{{ __('Meta Title') }}</label>
                {{Form::text('page_title',null,array('class' => 'form-control', 'placeholder' => __('db.Meta Title')))}}
            </div>
            <div class="col-md-12 form-group">
                <label>{{ __('Meta Description') }}</label>
                {{Form::text('short_description',null,array('class' => 'form-control', 'placeholder' => __('db.Meta Description')))}}
            </div>
        </div>
        @endif

        <div class="form-group">
            <input type="submit" value="{{__('db.submit')}}" class="btn btn-primary">
          </div>
        </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
<!-- Import Modal -->
<div id="importCategory" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        {!! Form::open(['route' => 'category.import', 'method' => 'post', 'files' => true]) !!}
        <div class="modal-header">
          <h5 id="exampleModalLabel" class="modal-title">{{__('db.Import Category')}}</h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
            <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}.</small></p>
           <p>{{__('db.The correct column order is')}} (name*, parent_category) {{__('db.and you must follow this')}}.</p>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{__('db.Upload CSV File')}} *</label>
                        {{Form::file('file', array('class' => 'form-control','required'))}}
                        <x-validation-error fieldName="file" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label> {{__('db.Sample File')}}</label>
                        <a href="sample_file/sample_category.csv" class="btn btn-info btn-block btn-md"><i class="dripicons-download"></i>  {{__('db.Download')}}</a>
                    </div>
                </div>
            </div>
            <input type="submit" value="{{__('db.submit')}}" class="btn btn-primary">
        </div>
        {{ Form::close() }}
      </div>
    </div>
</div>

<!-- Arrange Navbar Menu Modal -->
<div id="arrangeMenuModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="arrangeMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-sm border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="arrangeMenuModalLabel"><i class="dripicons-view-list text-primary mr-2"></i>{{ __('Arrange navbar menu') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body pt-2">
                <p class="text-muted small mb-3">{{ __('Drag items to reorder. This order will appear on the website navbar.') }}</p>
                <div id="menu-categories-loading" class="text-center py-4 text-muted"><i class="dripicons-loading dripicons-spin"></i> {{ __('Loading...') }}</div>
                <ul id="menu-categories-sortable" class="list-group list-group-flush d-none"></ul>
                <div id="menu-categories-empty" class="alert alert-light border text-muted text-center d-none">{{ __('No categories are set to show in navbar. Enable "Show in navbar" for categories first.') }}</div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" id="menu-categories-save"><i class="dripicons-checkmark mr-1"></i>{{ __('Save order') }}</button>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script type="text/javascript">
    $("ul#product").siblings('a').attr('aria-expanded','true');
    $("ul#product").addClass("show");
    $("ul#product #category-menu").addClass("active");

    function confirmDelete() {
      if (confirm("If you delete category all products under this category will also be deleted. Are you sure want to delete?")) {
          return true;
      }
      return false;
    }

    var category_id = [];
    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).on("click", ".open-EditCategoryDialog", function(){
        $("#editModal input[name='is_sync_disable']").prop("checked", false);
        $("#editModal input[name='featured']").prop("checked", false);
        $("#editModal input[name='show_in_menu']").prop("checked", false);
        var url ="category/";
        var id = $(this).data('id').toString();
        url = url.concat(id).concat("/edit");
        $.get(url, function(data){
            $("#editModal input[name='name']").val(data['name']);
            $("#editModal select[name='parent_id']").val(data['parent_id']);
            $("#editModal input[name='category_id']").val(data['id']);

            var updateUrl = "category/" + data['id'];
            $("#editCategoryForm").attr('action', updateUrl);

            if (data['is_sync_disable']) {
                $("#editModal input[name='is_sync_disable']").prop("checked", true);
            }
            if (data['featured']) {
                $("#editModal input[name='featured']").prop("checked", true);
            }
            if (data['show_in_menu']) {
                $("#editModal input[name='show_in_menu']").prop("checked", true);
            }
            $("#editModal input[name='page_title']").val(data['page_title']);
            $("#editModal input[name='short_description']").val(data['short_description']);
            $('.selectpicker').selectpicker('refresh');
        });
    });

    $(document).on('change', '.toggle-show-in-menu', function() {
        var chk = $(this);
        var id = chk.data('id');
        var showInMenu = chk.prop('checked') ? 1 : 0;
        chk.prop('disabled', true);
        $.post('{{ url("category/toggle-show-in-menu") }}', { _token: '{{ csrf_token() }}', category_id: id, show_in_menu: showInMenu })
            .done(function(r) {
                if (r.success && showInMenu && r.over_max) { alert('Max {{ \App\Http\Controllers\CategoryController::MAX_MENU_CATEGORIES }} categories in navbar. Oldest was auto-disabled.'); }
            })
            .fail(function() { chk.prop('checked', !showInMenu); })
            .always(function() { chk.prop('disabled', false); });
    });

    var menuCategoriesSortable = null;
    $('#arrangeMenuModal').on('show.bs.modal', function() {
        $('#menu-categories-loading').removeClass('d-none');
        $('#menu-categories-sortable').addClass('d-none').empty();
        $('#menu-categories-empty').addClass('d-none');
        $.get('{{ url("category/menu-categories") }}')
            .done(function(r) {
                $('#menu-categories-loading').addClass('d-none');
                if (r.categories && r.categories.length) {
                    r.categories.forEach(function(c) {
                        $('#menu-categories-sortable').append(
                            '<li class="list-group-item" data-id="' + c.id + '"><i class="dripicons-move drag-handle"></i><span>' + (c.name || '') + '</span></li>'
                        );
                    });
                    $('#menu-categories-sortable').removeClass('d-none');
                    if (menuCategoriesSortable) { $('#menu-categories-sortable').sortable('destroy'); }
                    $('#menu-categories-sortable').sortable({ handle: '.drag-handle', placeholder: 'list-group-item list-group-item-secondary', forcePlaceholderSize: true });
                } else {
                    $('#menu-categories-empty').removeClass('d-none');
                }
            })
            .fail(function() {
                $('#menu-categories-loading').addClass('d-none');
                $('#menu-categories-empty').removeClass('d-none').text('{{ __("Failed to load categories.") }}');
            });
    });

    $('#menu-categories-save').on('click', function() {
        var ids = [];
        $('#menu-categories-sortable .list-group-item').each(function() { ids.push(parseInt($(this).data('id'), 10)); });
        if (!ids.length) return;
        var btn = $(this).prop('disabled', true);
        $.post('{{ url("category/save-menu-order") }}', { _token: '{{ csrf_token() }}', order: ids })
            .done(function(r) {
                if (r.success) { $('#arrangeMenuModal').modal('hide'); $('#category-table').DataTable().ajax.reload(null, false); }
            })
            .always(function() { btn.prop('disabled', false); });
    });

    $('#category-table').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax":{
            url:"category/category-data",
            dataType: "json",
            type:"post"
        },
        "createdRow": function( row, data, dataIndex ) {
            $(row).attr('data-id', data['id']);
        },
        "columns": [
            {"data": "key"},
            {"data": "name"},
            {"data": "parent_id"},
            {"data": "show_in_menu"},
            {"data": "number_of_product"},
            {"data": "stock_qty"},
            {"data": "stock_worth"},
            {"data": "options"},
        ],
        'language': {
            'lengthMenu': '_MENU_ {{__("db.records per page")}}',
             "info":      '<small>{{__("db.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search":  '{{__("db.Search")}}',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        order:[['2', 'asc']],
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 1, 2, 3, 4, 5, 6]
            },
            {
                'render': function(data, type, row, meta){
                    if(type === 'display'){
                        data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                    }

                   return data;
                },
                'checkboxes': {
                   'selectRow': true,
                   'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                },
                'targets': [0]
            }
        ],
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],

        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                footer:true
            },
            {
                extend: 'excel',
                text: '<i title="export to excel" class="dripicons-document-new"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                footer:true
            },
            {
                extend: 'csv',
                text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                footer:true
            },
            {
                extend: 'print',
                text: '<i title="print" class="fa fa-print"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                footer:true
            },
            {
                text: '<i title="delete" class="dripicons-cross"></i>',
                className: 'buttons-delete',
                action: function ( e, dt, node, config ) {
                    if(user_verified == '1') {
                        category_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                category_id[i-1] = $(this).closest('tr').data('id');
                            }
                        });
                        if(category_id.length && confirm("If you delete category all products under this category will also be deleted. Are you sure want to delete?")) {
                            $.ajax({
                                type:'POST',
                                url:'category/deletebyselection',
                                data:{
                                    categoryIdArray: category_id
                                },
                                success:function(data){
                                    dt.rows({ page: 'current', selected: true }).deselect();
                                    dt.rows({ page: 'current', selected: true }).remove().draw(false);
                                }
                            });
                        }
                        else if(!category_id.length)
                            alert('No category is selected!');
                    }
                    else
                        alert('This feature is disable for demo!');
                }
            },
            {
                extend: 'colvis',
                text: '<i title="column visibility" class="fa fa-eye"></i>',
                columns: ':gt(0)'
            },
        ],
    } );

</script>
@endpush
