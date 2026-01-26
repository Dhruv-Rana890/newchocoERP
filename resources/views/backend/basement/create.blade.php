@extends('backend.layout.main')

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{__('db.add_basement')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}.</small></p>
                        <form id="basement-form">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('db.Basement Name')}} *</strong> </label>
                                        <input type="text" name="name" class="form-control" id="name" required>
                                        <span class="validation-msg" id="name-error"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Name (Arabic)</strong> </label>
                                        <input type="text" name="name_arabic" class="form-control" id="name_arabic">
                                        <span class="validation-msg" id="name_arabic-error"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('db.Code')}} *</strong> </label>
                                        <div class="input-group">
                                            <input type="text" name="code" class="form-control" id="code" required>
                                            <div class="input-group-append">
                                                <button id="genbutton" type="button" class="btn btn-sm btn-default" title="{{__('db.Generate')}}"><i class="fa fa-refresh"></i></button>
                                            </div>
                                        </div>
                                        <span class="validation-msg" id="code-error"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('db.Barcode Symbology')}} *</strong> </label>
                                        <select name="barcode_symbology" required class="form-control selectpicker">
                                            <option value="C128">Code 128</option>
                                            <option value="C39">Code 39</option>
                                            <option value="UPCA">UPC-A</option>
                                            <option value="UPCE">UPC-E</option>
                                            <option value="EAN8">EAN-8</option>
                                            <option value="EAN13">EAN-13</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('db.Brand')}}</strong> </label>
                                        <div class="input-group pos">
                                          <select name="brand_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Brand...">
                                            @foreach($lims_brand_list as $brand)
                                                <option value="{{$brand->id}}">{{$brand->title}}</option>
                                            @endforeach
                                          </select>
                                      </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('db.category')}} *</strong> </label>
                                        <div class="input-group pos">
                                          <select name="category_id" required class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Category...">
                                            @foreach($lims_category_list as $category)
                                                <option value="{{$category->id}}">{{$category->name}}</option>
                                            @endforeach
                                          </select>
                                      </div>
                                      <span class="validation-msg"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('db.Unit')}} *</strong> </label>
                                        <div class="input-group pos">
                                          <select name="unit_id" required class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Unit...">
                                            @foreach($lims_unit_list as $unit)
                                                <option value="{{$unit->id}}">{{$unit->unit_name}}</option>
                                            @endforeach
                                          </select>
                                      </div>
                                      <span class="validation-msg"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('db.Cost')}} *</strong> </label>
                                        <input type="number" name="cost" required class="form-control" step="any">
                                        <span class="validation-msg"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('db.Price')}} *</strong> </label>
                                        <input type="number" name="price" required class="form-control" step="any">
                                        <span class="validation-msg"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('db.Alert Quantity')}}</strong> </label>
                                        <input type="number" name="alert_quantity" class="form-control" step="any">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('db.Tax')}}</label>
                                        <div class="input-group pos">
                                        <select name="tax_id" class="selectpicker form-control">
                                            <option value="">No Tax</option>
                                            @foreach($lims_tax_list as $tax)
                                                <option value="{{$tax->id}}">{{$tax->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('db.Tax Method')}}</strong> </label>
                                        <select name="tax_method" class="form-control selectpicker">
                                            <option value="1">{{__('db.Exclusive')}}</option>
                                            <option value="2">{{__('db.Inclusive')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{__('db.Image')}}</strong> </label>
                                        <input type="file" name="image[]" class="form-control" multiple accept="image/*">
                                        <span class="validation-msg" id="image-error"></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{__('db.Details')}}</label>
                                        <textarea name="product_details" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <button type="submit" id="submit-btn" class="btn btn-primary">{{__('db.submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    $("#genbutton").on("click", function() {
        $.get('{{ route("basement.gencode") }}', function(data) {
            $("#code").val(data);
        });
    });

    $('#basement-form').on('submit', function(e) {
        e.preventDefault();
        if ($("#basement-form").valid()) {
            $('#submit-btn').attr('disabled','true').html('<span class="spinner-border text-light" role="status"></span> {{__("db.Saving")}}...');
            var formData = new FormData();
            var data = $("#basement-form").serializeArray();
            $.each(data, function (key, el) {
                formData.append(el.name, el.value);
            });
            var images = $('#basement-form input[name="image[]"]')[0].files;
            for (var i = 0; i < images.length; i++) {
                formData.append('image[]', images[i]);
            }

            $.ajax({
                type:'POST',
                url:"{{ route('basements.store') }}",
                data: formData,
                contentType: false,
                processData: false,
                success:function(response) {
                    location.href = '{{ route("basements.index") }}';
                },
                error:function(response) {
                    $('#submit-btn').attr('disabled',false).html('{{__("db.submit")}}');
                    if(response.responseJSON.errors.name) {
                        $("#name-error").text(response.responseJSON.errors.name[0]);
                    }
                    if(response.responseJSON.errors.code) {
                        $("#code-error").text(response.responseJSON.errors.code[0]);
                    }
                },
            });
        }
    });
</script>
@endpush
