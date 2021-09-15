@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <h1>المرتجعات</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li><a href="{{ route('dashboard.returns.index') }}">المرتجعات</a></li>
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

                    <form action="{{ route('dashboard.returns.store') }}" method="post">

                        {{ csrf_field() }}
                        {{ method_field('post') }}

                        <div class="form-group">
                            <label>نوع المترجع</label>
                            <select name="return_type" class="form-control" id="return_type">
                                    <option value="1">من العميل</option>
                            </select>
                        </div>
                        
                        <input type="hidden" name="bill_number_o" value="{{ $orderNumber }}">
                        
                        <div class="form-group" style="margin-top: 10px;">
                            <label>عدد الأصناف</label>
                            <input type="number" required name="Varieties_number" min="1" max="{{$productsC}}" class="form-control stock">
                        </div>
                        <script>
                            $(".stock").keyup(function() {
                                $(".products").remove();
                                var stockNumber = $(this).val();
                                for (i = 0; i < stockNumber; i++) {
                                  var serial_numbers = `
                                <div class="form-group">
                                    <select name="products[]" class="form-control products">
                                        <option value="">أختار المنتج</option>
                                        @foreach ($products as $product)
                                            <option value="{{$product->name}} كود :- {{ $product->productCode }}">{{$product->name}} كود :- {{ $product->productCode }}</option>
                                        @endforeach
                                    </select>
                                    <input type="number" name="quantity[]" class="form-control products" placeholder="الكمية">
                                </div>`;
                                  $("#apend").append(serial_numbers);
                                }
                            }); 
                            
                            $(document).ready(function(){
                                $("#flip").click(function(){
                                    $("#apend").slideToggle("fast");
                                    $(this).text(function(i, v){
                                      return v === 'ادخال المنتجات ' ? ' أخفاء' : 'ادخال المنتجات '
                                  })
                                });
                            });
                        </script>
                        <div id="apend"></div>
                        
                        
                        <h3 id="flip2" style="margin-bottom: 20px;margin-top: 1px;" class="btn btn-primary hidden">ادخال الأرقام التسلسلية</h3>
                        <script>
                            $(".stock").keyup(function() {
                                $(".serial_numbers").remove();
                                var stockNumber = $(this).val();
                                for (i = 0; i < stockNumber; i++) {
                                  var serial_numbers = `<div class="form-group serial_numbers">
                                <input type="text" name="serial_numbers[]" class="form-control">
                            </div>`;
                                  $("#apend2").append(serial_numbers);
                                }
                                
                                if(stockNumber > 0){
                                    $('#flip2').removeClass('hidden')
                                    $('#label_serialNumber').removeClass('hidden')
                                }else{
                                    $('#flip2').addClass('hidden')
                                    $('#label_serialNumber').addClass('hidden')
                                }
                                
                            }); 
                            
                            $(document).ready(function(){
                                $("#flip2").click(function(){
                                    $("#apend2").slideToggle("fast");
                                    $(this).text(function(i, v){
                                      return v === 'ادخال الأرقام التسلسلية' ? ' أخفاء' : 'ادخال الأرقام التسلسلية'
                                  })
                                });
                            });
                        </script>
                        <div id="apend2" style="display: none"><label id="label_serialNumber" class="hidden">الأرقام التسلسلية</label></div>
                        
                        <div class="form-group">
                            <label>ملاحظات</label>
                            <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
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