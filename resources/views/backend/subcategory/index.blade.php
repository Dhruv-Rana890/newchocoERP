@extends('backend.layout.main')

@section('content')
<x-success-message key="message" />
<x-error-message key="not_permitted" />

<section>
    <div class="container-fluid">
        @can('categories-index')
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addSubcategoryModal"><i class="dripicons-plus"></i> {{ __('Add Product Subcategory') }}</button>
        @endcan
    </div>
    <div class="table-responsive mt-3">
        <table id="subcategory-table" class="table" style="width: 100%">
            <thead>
                <tr>
                    <th>{{ __('Name (English)') }}</th>
                    <th>{{ __('Name (Arabic)') }}</th>
                    <th>{{ __('db.category') }}</th>
                    <th>{{ __('Slug') }}</th>
                    <th>{{ __('Sort Order') }}</th>
                    <th class="not-exported">{{ __('db.action') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</section>

{{-- Add Subcategory Modal --}}
<div class="modal fade" id="addSubcategoryModal" tabindex="-1" role="dialog" aria-labelledby="addSubcategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fcolor" id="addSubcategoryModalLabel">Add Product Subcategory</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="subcategoryAjaxForm" class="modal-form" action="{{ route('subcategory.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="col-12 mb-2">
                                    <label for="subcate_banner_img"><span class="fcolor">Subcategory Banner Image</span></label>
                                </div>
                                <div class="col-md-12 showBannerImage mb-3">
                                    <img src="{{ url('images/zummXD2dvAtI.png') }}" alt="Banner" class="img-thumbnail" style="max-height: 120px;">
                                </div>
                                <input type="file" name="subcate_banner_img" id="subcate_banner_img" class="form-control bannerimage">
                                <p id="errsubcate_banner_img" class="mb-0 text-danger em"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="col-12 mb-2">
                                    <label for="image"><span class="fcolor">Image</span></label>
                                </div>
                                <div class="col-md-12 showImage mb-3">
                                    <img src="{{ url('images/zummXD2dvAtI.png') }}" alt="Image" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                                <input type="file" name="image" id="image" class="form-control image">
                                <p id="errimage" class="mb-0 text-danger em"></p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="category_id"><span class="fcolor">Categories</span></label>
                        <select class="form-control" name="category_id" id="category_id">
                            <option value="">Select a Category</option>
                            @foreach($categories_list as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <p id="errcategory_id" class="mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="name_english"><span class="fcolor">Name (English)</span></label>
                        <input type="text" class="form-control" name="name_english" id="name_english" value="" placeholder="Enter name (english)">
                        <p id="errname_english" class="mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="name_arabic"><span class="fcolor">Name (Arabic)</span></label>
                        <input type="text" class="form-control" name="name_arabic" id="name_arabic" value="" placeholder="Enter name (arabic)">
                        <p id="errname_arabic" class="mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="slug"><span class="fcolor">Slug</span></label>
                        <input type="text" class="form-control" name="slug" id="slug" value="" placeholder="Enter slug">
                        <p id="errslug" class="mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="sort_order"><span class="fcolor">Sort Order</span></label>
                        <input type="number" class="form-control" name="sort_order" id="sort_order" value="0" min="0" placeholder="Enter sort order">
                        <p id="errsort_order" class="mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="description_english"><span class="fcolor">Subcategory Description (English)</span></label>
                        <textarea class="form-control" name="description_english" id="description_english" placeholder="Enter Subcategory Description (English)"></textarea>
                        <p id="errdescription_english" class="mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="description_arabic"><span class="fcolor">Subcategory Description (Arabic)</span></label>
                        <textarea class="form-control" name="description_arabic" id="description_arabic" placeholder="Enter Subcategory Description (Arabic)"></textarea>
                        <p id="errdescription_arabic" class="mb-0 text-danger em"></p>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="subcategorySubmitBtn" type="button" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>

{{-- Edit Subcategory Modal --}}
<div id="editSubcategoryModal" tabindex="-1" role="dialog" aria-labelledby="editSubcategoryModalLabel" aria-hidden="true" class="modal fade text-left">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            {{ Form::open(['url' => '', 'method' => 'PUT', 'files' => true, 'id' => 'editSubcategoryForm']) }}
            <div class="modal-header">
                <h5 class="modal-title" id="editSubcategoryModalLabel">Update Product Subcategory</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Subcategory Banner Image</label>
                            <div class="showBannerImageEdit mb-2"></div>
                            <input type="file" name="subcate_banner_img" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Image</label>
                            <div class="showImageEdit mb-2"></div>
                            <input type="file" name="image" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Categories *</label>
                    <select name="category_id" class="form-control" id="edit_category_id">
                        @foreach($categories_list as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Name (English) *</label>
                    {{ Form::text('name_english', null, ['required' => 'required', 'class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    <label>Name (Arabic)</label>
                    {{ Form::text('name_arabic', null, ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    <label>Slug</label>
                    {{ Form::text('slug', null, ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    <label>Sort Order</label>
                    {{ Form::number('sort_order', 0, ['min' => 0, 'class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    <label>Subcategory Description (English)</label>
                    {{ Form::textarea('description_english', null, ['class' => 'form-control', 'rows' => 3]) }}
                </div>
                <div class="form-group">
                    <label>Subcategory Description (Arabic)</label>
                    {{ Form::textarea('description_arabic', null, ['class' => 'form-control', 'rows' => 3]) }}
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">{{ __('db.submit') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $("ul#product").siblings('a').attr('aria-expanded', 'true');
    $("ul#product").addClass("show");
    $("ul#product #subcategory-menu").addClass("active");

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    var noImageUrl = "{{ url('images/zummXD2dvAtI.png') }}";

    // Banner image preview (add modal)
    $(document).on('change', '#subcate_banner_img', function() {
        var input = this;
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#addSubcategoryModal .showBannerImage img').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    });
    // Image preview (add modal)
    $(document).on('change', '#addSubcategoryModal #image', function() {
        var input = this;
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#addSubcategoryModal .showImage img').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    });

    // Add subcategory - AJAX submit
    $('#subcategorySubmitBtn').on('click', function() {
        var form = $('#subcategoryAjaxForm');
        var btn = $(this);
        $('.em').text('');
        btn.prop('disabled', true);
        var formData = new FormData(form[0]);
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                $('#addSubcategoryModal').modal('hide');
                form[0].reset();
                $('#addSubcategoryModal .showBannerImage img').attr('src', noImageUrl);
                $('#addSubcategoryModal .showImage img').attr('src', noImageUrl);
                $('#subcategory-table').DataTable().ajax.reload(null, false);
                if (res.message) alert(res.message);
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(field, messages) {
                        var el = $('#err' + field);
                        if (el.length) el.text(Array.isArray(messages) ? messages[0] : messages);
                    });
                } else {
                    alert(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something went wrong.');
                }
            },
            complete: function() {
                btn.prop('disabled', false);
            }
        });
    });

    // Edit - load data
    $(document).on('click', '.open-EditSubcategoryDialog', function() {
        var id = $(this).data('id');
        var url = "{{ url('subcategory') }}/" + id + "/edit";
        $.get(url, function(data) {
            $('#editSubcategoryForm').attr('action', "{{ url('subcategory') }}/" + data.id);
            $('#editSubcategoryForm select[name="category_id"]').val(data.category_id);
            $('#editSubcategoryForm input[name="name_english"]').val(data.name_english);
            $('#editSubcategoryForm input[name="name_arabic"]').val(data.name_arabic);
            $('#editSubcategoryForm input[name="slug"]').val(data.slug);
            $('#editSubcategoryForm input[name="sort_order"]').val(data.sort_order);
            $('#editSubcategoryForm textarea[name="description_english"]').val(data.description_english);
            $('#editSubcategoryForm textarea[name="description_arabic"]').val(data.description_arabic);
            var bannerUrl = data.subcate_banner_img ? "{{ url('images/subcategory/banner') }}/" + data.subcate_banner_img : noImageUrl;
            var imgUrl = data.image ? "{{ url('images/subcategory') }}/" + data.image : noImageUrl;
            $('#editSubcategoryModal .showBannerImageEdit').html('<img src="' + bannerUrl + '" alt="Banner" class="img-thumbnail" style="max-height: 100px;">');
            $('#editSubcategoryModal .showImageEdit').html('<img src="' + imgUrl + '" alt="Image" class="img-thumbnail" style="max-height: 80px;">');
        });
    });

    $('#subcategory-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('subcategory.data') }}",
            dataType: "json",
            type: "post"
        },
        columns: [
            { data: "name_english" },
            { data: "name_arabic" },
            { data: "category_name" },
            { data: "slug" },
            { data: "sort_order" },
            { data: "options", orderable: false, searchable: false }
        ],
        order: [[ 4, 'asc' ]],
        language: {
            lengthMenu: '_MENU_ {{ __("db.records per page") }}',
            info: '<small>{{ __("db.Showing") }} _START_ - _END_ (_TOTAL_)</small>',
            search: '{{ __("db.Search") }}',
            paginate: {
                previous: '<i class="dripicons-chevron-left"></i>',
                next: '<i class="dripicons-chevron-right"></i>'
            }
        },
        columnDefs: [
            { orderable: false, targets: [0, 1, 2, 3, 5] }
        ],
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lfB>rtip',
        buttons: [
            { extend: 'pdf', text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>', exportOptions: { columns: ':visible:Not(.not-exported)' } },
            { extend: 'excel', text: '<i title="export to excel" class="dripicons-document-new"></i>', exportOptions: { columns: ':visible:Not(.not-exported)' } },
            { extend: 'csv', text: '<i title="export to csv" class="fa fa-file-text-o"></i>', exportOptions: { columns: ':visible:Not(.not-exported)' } },
            { extend: 'print', text: '<i title="print" class="fa fa-print"></i>', exportOptions: { columns: ':visible:Not(.not-exported)' } }
        ]
    });
</script>
@endpush
