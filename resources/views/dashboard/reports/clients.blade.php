@extends('layouts.dashboard.app')

@section('content')
<style>
    #val,#valTwo{
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

            <h1>العملاء والطلبات</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">العملاء والطلبات</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <form action="{{ route('dashboard.reports.clients') }}" method="get">

                        <div class="row">

                            <div class="col-md-1">
                                <input type="text" name="search" class="form-control" placeholder="أسم العميل" value="{{ request()->search }}">
                            </div>
                    
                            <div class="col-md-1">
                                <select name="id" class="form-control">
                                    <option value="">فواتير</option>
                                    @foreach ($ordersIds as $ordersId)
                                        <option value="{{ $ordersId->id }}" {{ request()->id == $ordersId->id ? 'selected' : '' }}>{{ $ordersId->id }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="date" class="form-control">
                                    <option value="">جميع التواريخ</option>
                                    @foreach ($ordersDate as $orderDate)
                                        <option value="{{ $orderDate[0]->date }}" {{ request()->date == $orderDate[0]->date ? 'selected' : '' }}>{{ $orderDate[0]->date }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <select name="user_id" class="form-control">
                                    <option value="">كل المستخدمين</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ request()->user_id == $user->id ? 'selected' : '' }}>{{ $user->email }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <select name="shipping" class="form-control">
                                    <option value="">جميع طرق الشحن</option>
                                    @foreach ($shippingmethods as $shippingmethod)
                                        <option value="{{ $shippingmethod->id }}" {{ request()->shipping == $shippingmethod->id ? 'selected' : '' }}>{{ $shippingmethod->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <select name="representative_id" class="form-control">
                                    <option value="">جميع المناديب</option>
                                    @foreach ($representatives as $representative)
                                        <option value="{{ $representative->id }}" {{ request()->representative_id == $representative->id ? 'selected' : '' }}>{{ $representative->name }}</option>
                                    @endforeach
                                </select>
                            </div>
            
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                <button type="button" class="btn btn-primary print-btn"><i class="fa fa-print"></i></button>
                            </div>
                            
                        </div>
                    </form><!-- end of form -->

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
                #val,#valTwo{
                    border: 1px solid #222d32;
                    padding: 8px;
                    display: inline-block;
                    border-radius: 10px;
                    margin-top: 15PX;
                    font-size: 15px;
                }
                </style>
                <div class="box-body">

                    @if ($orders->count() > 0)

                        <table class="table table-hover" id="table">

                            <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('site.client_name')</th>
                                <th>الفاتورة</th>
                                <th>النقل</th>
                                <th>الخصم</th>
                                <th>الأجمالى</th>
                                @if(in_array(Auth::id(),array(1,2)))
                                <th>التكلفة</th>
                                <th>الربح</th>
                                @endif
                                <th>حالة الطلب</th>
                                <th>الدفع</th>
                                <th>الشحن</th>
                                <th>المندوب</th>
                                <th>التحصيل</th>
                            </tr>
                            </thead>
                            
                            <tbody>
                            @foreach ($orders as $index=>$order)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    @if(in_array(Auth::id(),array(1,2)))
                                    <td style="display: none;" id="loop">{{$order->total_cost}}</td>
                                    <td style="display: none;"  id="loopTwo">{{($order->total_price + $order->transport - $order->discount) -  $order->total_cost}}</td>
                                    @endif
                                    <td><a href="{{ route('dashboard.reports.historyclient', $order->client->id) }}" target="_blank">{{ $order->client->name }}</a></td>
                                    <td><a href="{{ route('dashboard.orders.index') }}?search=&id={{$order->id}}&created_at=" target="_blank">{{ $order->id }}</a></td>
                                    <td @if($order->transport > 0) style="color:red" @endif>
                                        @if($order->transport)
                                          {{ $order->transport }}
                                        @else
                                        0.00
                                        @endif
                                    </td>
                                    <td @if($order->discount > 0) style="color:red" @endif>
                                        @if($order->discount)
                                          {{ $order->discount }}
                                        @else
                                        0.00
                                        @endif
                                    </td>
                                    <td>{{ number_format($order->total_price + $order->transport - $order->discount, 2) }}</td>
                                    @if(in_array(Auth::id(),array(1,2)))
                                    <td>{{ $order->total_cost }}</td>
                                    <td>{{ number_format( ($order->total_price + $order->transport - $order->discount) -  $order->total_cost, 2) }}</td>
                                    @endif
                                    <td>
                                        @if($order->status == 0) قيد التنفيذ @endif
                                        @if($order->status == 1)
                                           تم التنفيذ عن طريق
                                           @foreach ($users as $user) 
                                                @if($user->id == $order->status_userId)
                                                   <a href="{{ route('dashboard.reports.historyUser', $user->id) }}" target="_blank">{{strstr($user->email, '@', true)}}</a>
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                        			 	@if($order->payment_type == 1)
                        			 	نقدا
                        				@endif
                        				@if($order->payment_type == 2)
                        				   أجل
                                         لمدة {{ $order->number_of_days }} يوم
                        				@endif
                        				@if($order->payment_type == 3)
                        				 مدفوعة جزائيا
                                         {{ $order->partially_price }}
                                         الباقى فى خلال {{  $order->the_rest_in_through}}يوم 
                        				@endif
                                    </td>
                                    <td>
                                        @foreach ($shippingmethods as $shippingmethod) 
                                           @if($shippingmethod->id == $order->shipping)
                                             <a href="{{ route('dashboard.reports.historyShippingmethod', $shippingmethod->id) }}" target="_blank">{{$shippingmethod->name }}</a>
                                           @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($order->representative_id)
                                        @foreach ($representatives as $representative) 
                                            @if($representative->id == $order->representative_id)
                                               <a href="{{ route('dashboard.reports.historyDelegate', $representative->id) }}" target="_blank">{{$representative->name}}</a>
                                            @endif
                                        @endforeach
                                        @else
                                        <span style="color:red">لا يوجد</span> 
                                        @endif
                                    </td>
                                    <td>
                                         @if($order->attainment == 0) لم يتم التحصيل @endif
                                         @if($order->attainment == 1) تم التحصيل@endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table><!-- end of table -->
                        @if(in_array(Auth::id(),array(1,2)))
                        <span id="val"></span>
                        <span id="valTwo"></span>
                        @endif
                    @else
                        
                        <h2>@lang('site.no_data_found')</h2>
                        
                    @endif

                </div><!-- end of box body -->
            </div>
            
            </div><!-- end of box -->
            

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->
    <script>
        var table = document.getElementById("table"), sumVal = 0; sumValTwo = 0;
        
        for(var row = 1; row < table.rows.length; row++)
        {
            sumVal = sumVal + parseInt(table.rows[row].cells[1].innerHTML);
            sumValTwo = sumValTwo + parseInt(table.rows[row].cells[2].innerHTML);
        }
        
        document.getElementById("val").innerHTML = "اجمالى التكلفة = " + sumVal.toFixed(2);
        document.getElementById("valTwo").innerHTML = " اجمالى الربح = " + sumValTwo.toFixed(2);
    </script>
     <script type="text/javascript">
        $(function() {
           var TotalValue = 0;
           var TotalValueTwo = 0;
           
           $("tr #loop").each(function(index,value){
             currentRow = parseFloat($(this).text());
             TotalValue += currentRow
           });
           
           $("tr #loopTwo").each(function(index,value){
             currentRowTwo = parseFloat($(this).text());
             TotalValueTwo += currentRowTwo
           });
        
           document.getElementById("val").innerHTML = "اجمالى التكلفة = " + TotalValue.toFixed(2);
           document.getElementById("valTwo").innerHTML = " اجمالى الربح = " + TotalValueTwo.toFixed(2);
        });
    </script>

@endsection
