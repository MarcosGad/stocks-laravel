@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <h1>التحويلات</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">التحويلات</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">التحويلات</h3>
                </div><!-- end of box header -->
                <div class="box-body">

                    @include('partials._errors')

                    <form action="{{ route('dashboard.product.postTransfersProduct') }}" method="post">
                        
                        {{ csrf_field() }}
                        
                        <input type="hidden" name="category_id" value="{{ $product->category_id  }}">

                        @foreach (config('translatable.locales') as $locale)
                            <input type="hidden" name="{{ $locale }}[name]" value="{{ $product->name }}">
                            <input type="hidden" name="{{ $locale }}[description]" value="{{ $product->description }}">
                        @endforeach
                        
                        <input type="hidden" name="purchase_invoice_number" value="{{ $product->purchase_invoice_number  }}">
                        
                        <input type="hidden" name="purchase_price" value="{{ $product->purchase_price }}">
                        
                        <input type="hidden" name="sale_price" value="{{ $product->sale_price }}">

                        <div class="form-group">
                            <img src="{{ $product->image_path }}" style="width: 100px" class="img-thumbnail image-preview" alt="">
                        </div>
                        
                        <div class="form-group">
                            <label>
                                @foreach ($stores as $store) 
                                    @if($store->id == $product->store_id)
                                        <a href="{{ route('dashboard.reports.store') }}?search=&category_id=&store_id={{$store->id}}&stock=1" target="_blank">{{$store->store_name }}</a>
                                    @endif
                                @endforeach
                            </label>
                            <input type="number" class="form-control" value="{{$product->stock}}" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label>العدد</label>
                            <input type="number" name="stock" required class="form-control stock" max="{{$product->stock}}">
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
                            <label>المخزن</label>
                            <select name="store_id" class="form-control">
                                @foreach ($stores as $store)
                                    @if($store->id != $storeId)
                                    <option value="{{ $store->id }}">{{ $store->store_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        
                        <input type="hidden" name="productId" value="{{$productId}}">
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> تحويل</button>
                        </div>

                    </form><!-- end of form -->
                    
                    <script type="text/javascript">
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