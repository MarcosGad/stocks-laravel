@extends('layouts.dashboard.app')

@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet" type="text/css" />
    <style>
        /*.all-box{*/
        /*    position: fixed;*/
        /*    overflow: scroll;*/
        /*    height: 100%;*/
        /*    width: 40%;*/
        /*    top: 105px;*/
        /*}*/
        #datepicker > span:hover{cursor: pointer;}
        .datepicker.dropdown-menu{
            right: 80%;
            width: 220px;
        }
    </style>
    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.add_order')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li><a href="{{ route('dashboard.clients.index') }}">@lang('site.clients')</a></li>
                <li class="active">@lang('site.add_order')</li>
            </ol>
        </section>

        <section class="content">
            
            <!--<span id="value">5</span>-->
            <!--<input type="button" class="plus" value="+" />-->
            <!--<input type="button" class="minus" value="-" />-->
            <!--<script>-->
            <!--$('.plus').click(function() { changeValue(1); });-->
            <!--$('.minus').click(function() { changeValue(-1); });-->
            
            <!--function changeValue(val) {-->
            <!--    var container = $('#value');-->
            <!--    var current = parseInt(container.html(), 10);-->
            <!--    console.log(current);-->
            <!--    if(current == 15){-->
            <!--         $('.plus').attr("disabled","disabled");-->
            <!--    }else{-->
            <!--        $('.plus').removeAttr('disabled');-->
            <!--    }-->
            <!--    container.html(Math.max(0, current + val).toString());-->
            <!--}-->
            <!--</script>-->


            <div class="row">

                <div class="col-md-6">

                    <div class="box box-primary">

                        <div class="box-header">

                            <h3 class="box-title" style="margin-bottom: 10px">@lang('site.categories')</h3>

                        </div><!-- end of box header -->

                        <div class="box-body">
                        
                            @foreach ($categories as $category)
                                
                                <div class="panel-group">

                                    <div class="panel panel-info">

                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" href="#{{ str_replace(' ', '-', $category->name) }}">{{ $category->name }}</a>
                                            </h4>
                                        </div>

                                        <div id="{{ str_replace(' ', '-', $category->name) }}" class="panel-collapse collapse">

                                            <div class="panel-body">

                                                @if ($category->products->count() > 0)
                         
                                                <nav class="navbar navbar-inverse navbar-fixed-top">
                                                  <div class="container">
                                                       <form style="margin-top: 8px;">
                                                        <input type="text" class="form-control search_field" placeholder="بحث.........">
                                                      </form>
                                                  </div>
                                                </nav>
                                                    <table class="table table-hover fid_table">
                                                        <thead>
                                                            <tr class="t_head">
                                                                <th>@lang('site.name')</th>
                                                                <th>فى</th>
                                                                <th>@lang('site.stock')</th>
                                                                <th>@lang('site.price')</th>
                                                                <th></th>
                                                                <th>@lang('site.add')</th>
                                                            </tr>
                                                        </thead>
                                                        @foreach ($category->products as $product)
                                                            @if($product->stock > 0 && Auth::user()->store_official == $product->store_id || $product->stock > 0 && Auth::user()->store_official == 0)
                                                            <tr>
                                                                <td>{{ $product->name }}</td>
                                                                <td>{{ $product->stock }}</td>
                                                                <td>
                                                                    @foreach ($stores as $store) 
                                                                        @if($store->id == $product->store_id)
                                                                               {{$store->store_name }}
                                                                        @endif
                                                                    @endforeach
                                                                </td>
                                                                <td>{{ number_format($product->sale_price, 2) }}</td>
                                                                <td><input type="text" class="form-control input-sm sale_price" data-idp="{{$product->id}}" value="0" min="0"></td>
                                                                @if($product->stock > 0)
                                                                <td>
                                                                    <a href=""
                                                                       id="product-{{ $product->id }}"
                                                                       data-name="{{ $product->name }}"
                                                                       data-id="{{ $product->id }}"
                                                                       data-cprice="{{ $product->purchase_price }}"
                                                                       data-price="{{ $product->sale_price }}"
                                                                       data-stock="{{ $product->stock }}"
                                                                       class="btn btn-success btn-sm add-product-btn disabled">
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>
                                                                </td>
                                                                @else
                                                                <td>
                                                                    <a href=""
                                                                       class="btn btn-success btn-sm disabled">
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>
                                                                </td>
                                                                @endif
                                                            </tr>
                                                            @endif
                                                        @endforeach

                                                    </table><!-- end of table -->

                                                @else
                                                    <h5>@lang('site.no_records')</h5>
                                                @endif

                                            </div><!-- end of panel body -->

                                        </div><!-- end of panel collapse -->

                                    </div><!-- end of panel primary -->

                                </div><!-- end of panel group -->

                            @endforeach

                        </div><!-- end of box body -->

                    </div><!-- end of box -->

                </div><!-- end of col -->

                <div class="col-md-6">
                 <div class="all-box">

                    <div class="box box-primary">

                        <div class="box-header">

                            <h3 class="box-title">@lang('site.orders')</h3>


                        </div><!-- end of box header -->

                        <div class="box-body">

                            <form action="{{ route('dashboard.clients.orders.store', $client->id) }}" method="post">

                                {{ csrf_field() }}
                                {{ method_field('post') }}

                                @include('partials._errors')
                                
                                <div class="form-group">
                                    <div id="datepicker" class="input-group date" data-date-format="yyyy-mm-dd">
                                        <input class="form-control" type="text" readonly name="date" value="{{\Carbon\Carbon::today()->format('Y-m-d')}}"/>
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                    </div>
                                </div>

                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>@lang('site.product')</th>
                                        <th>@lang('site.quantity')</th>
                                        <th>القطعة</th>
                                        <th>@lang('site.price')</th>
                                    </tr>
                                    </thead>

                                    <tbody class="order-list">


                                    </tbody>

                                </table><!-- end of table -->
                                <h4>@lang('site.total') : <span class="total-price">0</span></h4>
                                
                                 <div class="form-group" style="margin-top:20px">
                                    <label for="transport">مصاريف النقل</label>
                                    <input type="text" class="form-control" id="transport" name="transport" min="0" value="0">
                                 </div>
                                 
                                 @if(auth()->user()->id == 1 || auth()->user()->id == 2)
                                 <div class="form-group" style="margin-top:20px">
                                    <label for="discount">خصم</label>
                                    <input type="text" class="form-control" id="discount" name="discount" min="0" value="0">
                                 </div>
                                 @endif
                                 
                                 <div class="form-group">
                                    <label>طريقة الدفع</label>
                                    <select name="payment_type" class="form-control" id="payment_methodd">
                                        <option value="1">نقديا</option>
                                        <option value="2">أجل</option>
                                        <option value="3">مدفوعة جزائيا</option>
                                    </select>
                                </div>
                                
                                <div class="form-group" id="number_of_days" hidden="hidden">
                                    <label>مدة الاجل بالأيام</label>
                                    <input type="number" name="number_of_days" class="form-control">
                                </div>
                                
                                <div class="form-group partially_price" hidden="hidden">
                                    <label>المبلغ المدفوع</label>
                                    <input type="number" name="partially_price" class="form-control">
                                </div>
                                <div class="form-group partially_price" hidden="hidden">
                                    <label>الباقى فى خلال بالأيام</label>
                                    <input type="number" name="the_rest_in_through" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label for="shipping">شحن عن طريق</label>
                                    <select name="shipping" class="form-control" id="shipping">
                                        @foreach ($shippingmethods as $shippingmethod)
                                            <option value="{{ $shippingmethod->id }}">{{ $shippingmethod->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>أسم المندوب</label>
                                    <select name="representative_id" class="form-control">
                                        <option value="">لا يوجد</option>
                                        @foreach ($representatives as $representative)
                                            <option value="{{ $representative->id }}">{{ $representative->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                 
                                  <div class="form-group">
                                    <label for="notes">الأرقام التسلسلية</label>
                                    <textarea class="form-control" id="serial_numbers" rows="3" name="serial_numbers"></textarea>
                                  </div>
                                  
                                  <div class="form-group">
                                    <label for="notes">ملاحظات عن الفاتورة</label>
                                    <textarea class="form-control" id="notes" rows="2" name="notes"></textarea>
                                  </div>
                                  
                                  <div id="w-order" class="hidden" style="margin-bottom: 10px;color: red;font-size: 16px;">قيمة الفاتورة لم تتعد قيمة التكلفة</div>
                                 
                                <button class="btn btn-primary btn-block disabled" id="add-order-form-btn"><i class="fa fa-plus"></i> @lang('site.add_order')</button>

                            </form>

                        </div><!-- end of box body -->

                    </div><!-- end of box -->

                    @if ($client->orders->count() > 0)

                        <div class="box box-primary">

                            <div class="box-header">

                                <h3 class="box-title" style="margin-bottom: 10px">@lang('site.previous_orders')
                                    <small>{{ $orders->total() }}</small>
                                </h3>

                            </div><!-- end of box header -->

                            <div class="box-body">

                                @foreach ($orders as $order)

                                    <div class="panel-group">

                                        <div class="panel panel-success">

                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" href="#{{ $order->date }}">{{ $order->date }}</a>
                                                </h4>
                                            </div>

                                            <div id="{{ $order->date }}" class="panel-collapse collapse">

                                                <div class="panel-body">
                                                    <h5>رقم الطلب :- <a href="{{ route('dashboard.orders.index') }}?search=&id={{$order->id}}&created_at=" target="_blank">{{ $order->id }}</a> </h5>
                                                    <ul class="list-group">
                                                        <table class="table table-hover table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th class="font-print">الصنف</th>
                                                                    <th class="font-print">كود الصنف</th>
                                                                    <th class="font-print">@lang('site.quantity')</th>
                                                                    <th class="font-print">سعر الوحدة</th>
                                                                    <th class="font-print">@lang('site.price')</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach ($order->products as $product)
                                                                <tr>
                                                                    <td class="font-print">{{ $product->name }}</td>
                                                                    <td class="font-print-two">{{ $product->productCode }}</td>
                                                                    <td class="font-print-two">{{ $product->pivot->quantity }}</td>
                                                                    @php
                                                                       $last_price = DB::table('last_price_product_order')->select('last_price')->where('order_id', $order->id)->where('product_id', $product->id)->first();
                                                                    @endphp
                                                                    <td class="font-print-two">{{ $last_price->last_price }}</td>
                                                                    <td class="font-print-two">{{ number_format($product->pivot->quantity * $last_price->last_price, 2) }}</td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                        <h4>@lang('site.total') :- <span>{{ number_format($order->total_price, 2) }}</span></h4>
                                                    </ul>
                                                </div><!-- end of panel body -->

                                            </div><!-- end of panel collapse -->

                                        </div><!-- end of panel primary -->

                                    </div><!-- end of panel group -->

                                @endforeach

                                {{ $orders->links() }}

                            </div><!-- end of box body -->

                        </div><!-- end of box -->

                    @endif
                  </div>
                </div><!-- end of col -->

            </div><!-- end of row -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->
    
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
<script>
$(function () {
  $("#datepicker").datepicker({ 
        autoclose: true, 
        todayHighlight: true
  });
});
</script>
@endsection
