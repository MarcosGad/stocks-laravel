@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <h1>@lang('site.products')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li><a href="{{ route('dashboard.products.index') }}"> @lang('site.products')</a></li>
                <li class="active">@lang('site.edit')</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">@lang('site.edit')</h3>
                </div><!-- end of box header -->
                <div class="box-body">

                    @include('partials._errors')

                    <form action="{{ route('dashboard.products.update', $product->id) }}" method="post" enctype="multipart/form-data">

                        {{ csrf_field() }}
                        {{ method_field('put') }}

                        <div class="form-group">
                            <label>@lang('site.categories')</label>
                            <select name="category_id" class="form-control">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>فاتورة الشراء</label>
                            <select name="purchase_invoice_number" class="form-control">
                                @foreach ($bills as $bill)
                                    <option value="{{ $bill->invoice_number }}" {{ $product->purchase_invoice_number == $bill->invoice_number ? 'selected' : '' }}>{{ $bill->invoice_number }} - {{ $bill->supplier_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        @foreach (config('translatable.locales') as $locale)
                            <div class="form-group">
                                <label>@lang('site.' . $locale . '.name')</label>
                                <input type="text" name="{{ $locale }}[name]" required class="form-control" value="{{ $product->name }}">
                            </div>

                            <div class="form-group">
                                <label>@lang('site.' . $locale . '.description')</label>
                                <textarea name="{{ $locale }}[description]" class="form-control ckeditor">{{ $product->description }}</textarea>
                            </div>

                        @endforeach

                        <div class="form-group">
                            <label>@lang('site.image')</label>
                            <input type="file" name="image" class="form-control image">
                        </div>

                        <div class="form-group">
                            <img src="{{ $product->image_path }}" style="width: 100px" class="img-thumbnail image-preview" alt="">
                        </div>
                        
                        <div class="form-group">
                            <label>@lang('site.purchase_price')</label>
                            <input type="number" name="purchase_price" required step="0.01" class="form-control purchasePrice" value="{{ $product->purchase_price }}">
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
                            <input type="number" name="sale_price" required step="0.01" class="form-control sale-price" value="{{ $product->sale_price }}">
                        </div>
                        
                        <div class="form-group">
                            <label>@lang('site.stock')</label>
                            <input type="number" name="stock" required class="form-control" value="{{ $product->stock}}" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label>الرصيد</label>
                            <input type="number" name="real_stock" required class="form-control stock" value="{{ $product->real_stock}}">
                            <h3 id="flip" class="btn btn-primary">ادخال الأرقام التسلسلية</h3>
                        </div>
                        
                        <script>
                        
                            $(".stock").keyup(function() {
                                $(".serial_numbers").remove();
                                var stockNumber = $(this).val();
                                var stockNumberR = <?php echo $product->stock; ?>;
                                for (i = 0; i < stockNumber - stockNumberR; i++) {
                                  var serial_numbers = `<div class="form-group serial_numbers">
                                <input type="text" name="serial_numbers[]" class="form-control">
                            </div>`;
                                  $("#apend").append(serial_numbers);
                                }
                            }); 
                            
                            $(document).ready(function(){
                                $("#flip").click(function(){
                                    $("#apendTwo").slideToggle("fast");
                                    $(this).text(function(i, v){
                                      return v === 'ادخال الأرقام التسلسلية' ? ' أخفاء' : 'ادخال الأرقام التسلسلية'
                                  })
                                });
                            });
                        </script>
                        
                        <div id="apendTwo" style="display: none">
                        <label>الأرقام التسلسلية</label>
                        @for ($i = 0; $i < $product->stock; $i++)
                            <div class="form-group">
                                <input type="text" name="serial_numbers[]" class="form-control" value="{{ $product->serial_numbers[$i] ?? '' }}">
                            </div>
                        @endfor
                        <div id="apend"></div>
                        </div>
                       
                        <div class="form-group">
                            <label>أسم المخزن</label>
                            <select name="store_id" class="form-control">
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}" {{ $product->store_id == $store->id ? 'selected' : '' }}>{{ $store->store_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>@lang('site.product_code')</label>
                            <input type="text" name="productCode" class="form-control" value="{{ $product->productCode}}">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> @lang('site.edit')</button>
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
