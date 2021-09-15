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

            <h1>طريقة الشحن</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">طريقة الشحن</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="row">
                        <div class="col-md-10">
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
                    <p>رقم طريقة الشحن :- {{ $shippingmethod->id }}</p>
                    <p>أسم طريقة الشحن :- {{ $shippingmethod->name }}</p>

                    @if ($orders->count() > 0)

                        <table class="table table-hover" id="table">

                            <thead>
                            <tr>
                                <th>#</th>
                                <th>رقم الأوردر</th>
                                <th>@lang('site.price')</th>
                                <th>@lang('site.created_at')</th>
                                <th>تاريخ الطلب</th>
                                <th>عن طريق</th>
                                <th>حالة الطلب</th>
                                <th>المرتجع</th>
                            </tr>
                            </thead>
                            
                            <tbody>
                            @foreach ($orders as $index=>$order)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td style="display: none;" id="loop">{{ $order['total_price'] - ($order['discount'] + $order['transport']) }}</td>
                                    <td><a href="{{ route('dashboard.orders.index') }}?search=&id={{$order->id}}&created_at=" target="_blank">{{ $order->id }}</a></td>
                                    <td>{{ number_format($order->total_price + $order->transport - $order->discount, 2) }}</td>
                                    <td>{{ $order->created_at->toFormattedDateString() }}</td>
                                    <td>@if($order->date) {{$order->date}} @else {{ $order->created_at->toFormattedDateString() }} @endif</td>
                                    <td>
                                    @foreach ($users as $user) 
                                        @if($user->id == $order->user_id)
                                           <a href="{{ route('dashboard.reports.historyUser', $user->id) }}" target="_blank">{{strstr($user->email, '@', true)}}</a>
                                        @endif
                                    @endforeach
                                    </td>
                                    <td>
                                        @if($order->status == 0) قيد التنفيذ @endif
                                        @if($order->status == 1)
                                           تم التنفيذ عن طريق :-
                                           @foreach ($users as $user) 
                                                @if($user->id == $order->status_userId)
                                                   <a href="{{ route('dashboard.reports.historyUser', $user->id) }}" target="_blank">{{strstr($user->email, '@', true)}}</a>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($order->attainment == 0)
                                            <p style="color:red">لم يتم التحصيل</p>
                                        @endif
                                        @if($order->attainment == 1)
                                           <p style="color:green">مدفوعة</p>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->bounceds_s == 0) <span style="color:red">لا يوجد</span> @endif
                                        @if($order->bounceds_s > 0) 
                                        <a href="{{ route('dashboard.returns.index') }}?search={{$order->id}}" target="_blank">
                                            يوجد عدد {{$order->bounceds_s}} مرتجع
                                        </a>
                                        @endif
                                    </td>
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
           document.getElementById("val").innerHTML = "اجمالى الصافى = " + TotalValue.toFixed(2);
        });
    </script>

@endsection