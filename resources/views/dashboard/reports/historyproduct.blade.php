@extends('layouts.dashboard.app')

@section('content')
<style>
    #val{
        border: 1px solid #222d32;
        padding: 8px;
        display: inline-block;
        border-radius: 10px;
        margin-top: 15PX;
        font-size: 15px;
    }
</style>

    <div class="content-wrapper">

        <section class="content-header">

            <h1>تاريخ المنتج</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">تاريخ المنتج</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="row">
                        <div class="col-md-7"></div>
                        <div class="col-md-3">
                        @if($product->serial_numbers)
                        @if(array_search(null, $product->serial_numbers) !== 0)
                            <a href="{{ route('dashboard.products.showSerial', $product->id) }}" target="_blank" class="btn btn-block btn-primary"><i class="fa fa-eye"></i> عرض الأرقام التسلسلية</a>
                        @endif
                        @endif
                        </div>
                        <div class="col-md-2">
                            <a class="btn btn-block btn-primary print-btn"><i class="fa fa-print"></i> @lang('site.print')</a>
                        </div>
                    </div>
                </div><!-- end of box header -->
                <div id="print-area">
                    <style type="text/css" media="print">
                    @page {
                        size: auto;   
                        margin: 30px; 
                        margin-top:5px;
                    }
                    .box-body{
                        font-size:10px;
                    }
                    .btn-primary{
                      display: none;
                    }
                    a[href]:after {
                      content: none !important;
                    }
                    #val{
                        border: 1px solid #222d32;
                        padding: 8px;
                        display: inline-block;
                        border-radius: 10px;
                        margin-top: 15PX;
                        font-size: 15px;
                    }
                    </style>

                <div class="box-body">
                    <p>أسم المنتج :- {{ $product->name }}</p>
                    <p>كود المنتج :- {{ $product->productCode }}</p>
                    <p @if($product->real_stock == 0) style="color:red" @endif>الرصيد :- {{ $product->real_stock }}</p>
                    <p @if($product->stock == 0) style="color:red" @endif>المخزن :- {{ $product->stock }}  -  
                     @foreach ($stores as $store) 
                        @if($store->id == $product->store_id)
                            <a href="{{ route('dashboard.reports.store') }}?search=&category_id=&store_id={{$store->id}}&stock=1" target="_blank">{{$store->store_name }}</a>
                        @endif
                     @endforeach
                    </p>
                    <p>
                        @if($product->transformer_product == 1)
                          محول من :- 
                           @foreach ($stores as $store) 
                                @if($store->id == $product->store_original)
                                    <a href="{{ route('dashboard.reports.store') }}?search=&category_id=&store_id={{$store->id}}&stock=1" target="_blank">{{$store->store_name }}</a>
                                @endif
                           @endforeach
                        @endif
                        @if($product->transformer_product == 2)
                         تم التحويل منه الى :- 
                         @foreach ($stores as $store) 
                            @if($store->id == $product->store_to)
                                <a href="{{ route('dashboard.reports.store') }}?search=&category_id=&store_id={{$store->id}}&stock=1" target="_blank">{{$store->store_name }}</a>
                            @endif
                          @endforeach
                        @endif
                    </p>
                    @if($lastPrices->count() > 0)

                        <table class="table table-hover" id="table">

                            <thead>
                            <tr>
                                <th>#</th>
                                <th>رقم الأوردر</th>
                                <th>@lang('site.client_name')</th>
                                <th>العدد</th>
                                <th>السعر</th>
                            </tr>
                            </thead>
                            
                            <tbody>
                            @foreach ($lastPrices as $index=>$lastPrice)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td style="display: none;" id="loop">
                                        @foreach ($productQs as $productQ)
                                            @if($productQ->order_id == $lastPrice->order_id)
                                                {{ $productQ->quantity * $lastPrice->last_price}}
                                            @endif
                                       @endforeach
                                    </td>
                                    <td><a href="{{ route('dashboard.orders.index') }}?search=&id={{$lastPrice->order_id}}&created_at=" target="_blank">{{ $lastPrice->order_id }}</a></td>
                                    <td>
                                        @foreach ($orders as $order)
                                           @if($order->id == $lastPrice->order_id)
                                              <a href="{{ route('dashboard.reports.historyclient', $order->client->id) }}" target="_blank">{{ $order->client->name }}</a>
                                           @endif
                                        @endforeach
                                    </td>
                                    <td>
                                       @foreach ($productQs as $productQ)
                                            @if($productQ->order_id == $lastPrice->order_id)
                                                {{ $productQ->quantity }}
                                            @endif
                                       @endforeach
                                    </td>
                                    <td>{{ number_format($lastPrice->last_price, 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table><!-- end of table -->
                        <span id="val"></span>
                    @else
                        
                        <h3>@lang('site.no_data_found')</h3>
                        
                    @endif
                    
                </div><!-- end of box body -->
            </div><!-- end of box -->
            </div>
        </section><!-- end of content -->

    </div><!-- end of content wrapper -->
    <script type="text/javascript">
        $(function() {
           var TotalValue = 0;
           $("tr #loop").each(function(index,value){
             currentRow = parseFloat($(this).text());
             TotalValue += currentRow
           });
           document.getElementById("val").innerHTML = " الأجمالى = " + TotalValue.toFixed(2);
        });
    </script>

@endsection