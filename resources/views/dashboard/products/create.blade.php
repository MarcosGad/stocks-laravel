@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <h1>@lang('site.products')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li><a href="{{ route('dashboard.products.index') }}"> @lang('site.products')</a></li>
                <li class="active">@lang('site.add')</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">@lang('site.add')</h3>
                </div><!-- end of box header -->
                <div class="box-body">

                    @include('partials._errors')

                    <form action="{{ route('dashboard.products.store') }}" method="post" enctype="multipart/form-data">

                        {{ csrf_field() }}
                        {{ method_field('post') }}

                        <div class="form-group">
                            <label>@lang('site.categories')</label>
                            <select name="category_id" class="form-control">
                                <option value="">@lang('site.all_categories')</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>فاتورة الشراء</label>
                            <select name="purchase_invoice_number" class="form-control">
                                @foreach ($bills as $bill)
                                    <option value="{{ $bill->invoice_number }}">{{ $bill->invoice_number }} - {{ $bill->supplier_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        

                        @foreach (config('translatable.locales') as $locale)
                            <div class="form-group">
                                <label>@lang('site.' . $locale . '.name')</label>
                                <input type="text" name="{{ $locale }}[name]" required class="form-control" value="{{ old($locale . '.name') }}">
                            </div>

                            <div class="form-group">
                                <label>@lang('site.' . $locale . '.description')</label>
                                <textarea name="{{ $locale }}[description]" class="form-control ckeditor">{{ old($locale . '.description') }}</textarea>
                            </div>

                        @endforeach

                        <div class="form-group">
                            <label>@lang('site.image')</label>
                            <input type="file" name="image" class="form-control image">
                        </div>

                        <div class="form-group">
                            <img src="{{ asset('uploads/product_images/default.png') }}" style="width: 100px" class="img-thumbnail image-preview" alt="">
                        </div>
                        
                        <div class="form-group">
                            <label>@lang('site.purchase_price')</label>
                            <input type="number" name="purchase_price" required step="0.01" class="form-control purchasePrice" value="{{ old('purchase_price') }}">
                        </div>
                        <script>
                        $(".purchasePrice").keyup(function() {
                            var val = parseFloat($(this).val());
                            var salePriceN = val * 5/100;
                            var salePrice = val + salePriceN;
                            $('.sale-price').val(salePrice.toFixed(2));
                        }); 
                        </script>
                        <div class="form-group">
                            <label>@lang('site.sale_price')</label>
                            <input type="number" name="sale_price" step="0.01" required class="form-control sale-price" value="{{ old('sale_price') }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.stock')</label>
                            <input type="number" name="stock" required class="form-control stock">
                            <h3 id="flip" class="btn btn-primary hidden">ادخال الأرقام التسلسلية</h3>
                        </div>
                        <script>
                            $(".stock").keyup(function() {
                                $(".serial_numbers").remove();
                                var stockNumber = $(this).val();
                                for (i = 0; i < stockNumber; i++) {
                                  var serial_numbers = `<div class="form-group serial_numbers">
                                <input type="text" name="serial_numbers[]" class="form-control">
                            </div>`;
                                  $("#apend").append(serial_numbers);
                                }
                                
                                if(stockNumber > 0){
                                    $('#flip').removeClass('hidden')
                                    $('#label_serialNumber').removeClass('hidden')
                                }else{
                                    $('#flip').addClass('hidden')
                                    $('#label_serialNumber').addClass('hidden')
                                }
                                
                            }); 
                            
                            $(document).ready(function(){
                                $("#flip").click(function(){
                                    $("#apend").slideToggle("fast");
                                    $(this).text(function(i, v){
                                      return v === 'ادخال الأرقام التسلسلية' ? ' أخفاء' : 'ادخال الأرقام التسلسلية'
                                  })
                                });
                            });
                        </script>
                        <div id="apend" style="display: none"><label id="label_serialNumber" class="hidden">الأرقام التسلسلية</label></div>
                
                        <div class="form-group">
                            <label>أسم المخزن</label>
                            <select name="store_id" class="form-control">
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->store_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> @lang('site.add')</button>
                        </div>

                    </form><!-- end of form -->
                    
                    <script type="text/javascript">
                    
                    $('body').on('keydown', 'input, select', function(e) {
                        if (e.key === "Enter") {
                            var self = $(this), form = self.parents('form:eq(0)'), focusable, next;
                            focusable = form.find('input,a,select,button,textarea').filter(':visible');
                            next = focusable.eq(focusable.index(this)+1);
                            if (next.length) {
                                next.focus();
                            } else {
                                form.submit();
                            }
                            return false;
                        }
                    });

                  $(document).ready(function() {
                    $(window).keydown(function(event){
                        if((event.keyCode == 13) && ($(event.target)[0]!=$("input")[0])) {
                            event.preventDefault();
                            return false;
                        }
                    });
                  });
                  
                    </script>

                </div><!-- end of box body -->

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection
