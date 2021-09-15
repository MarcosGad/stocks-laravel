@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <h1>المرتجعات</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li><a href="{{ route('dashboard.products.index') }}">المرتجعات</a></li>
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
                                    <option value="1">من عميل</option>
                                    <option value="2">الى مورد</option>
                            </select>
                        </div>
                        
                        <label>رقم الفاتورة</label>
                        <div class="form-group" style="margin-bottom: 1px;">
                            <select name="bill_number_o" class="form-control" id="bill_number_one">
                                @foreach ($orders as $order)
                                    <option value="{{ $order->id }}">{{ $order->id }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group" hidden="hidden" id="bill_number_two">
                            <select name="bill_number_b" class="form-control">
                                @foreach ($bills as $bill)
                                    <option value="{{ $bill->invoice_number }}">{{ $bill->invoice_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group" style="margin-top: 10px;">
                            <label>عدد الأصناف</label>
                            <input type="number" required name="Varieties_number" class="form-control stock">
                        </div>
                        <script>
                            $(".stock").keyup(function() {
                                $("#myproducts").remove();
                                var stockNumber = $(this).val();
                                for (i = 0; i < stockNumber; i++) {
                                  var serial_numbers = `
                                <input list="products" id="myproducts" name="products[]" class="form-control"/>
                                <datalist id="products">
                                  @foreach ($products as $product)
                                     <option value="{{$product->name}} كود :- {{ $product->productCode }}">
                                  @endforeach
                                </datalist>
                                <input type="number" name="quantity[]" class="form-control" placeholder="الكمية">
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
                        
                        <div class="form-group">
                            <label>رقم المترجع من الفاتورة</label>
                            <input type="number" required name="return_number" class="form-control stock">
                        </div>
                        
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