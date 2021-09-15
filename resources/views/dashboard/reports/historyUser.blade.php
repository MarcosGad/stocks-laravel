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

            <h1>تاريخ المستخدم</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">تاريخ المستخدم</li>
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
                    <p>أسم المستخدم :- {{ $user->first_name }} {{ $user->last_name }}</p>
                    <p>البريد الألكترونى  :- {{ $user->email }}</p>
                    <p>مسؤال عن :-
                    @if($user->store_official == 0) <a href="{{ route('dashboard.reports.store') }}?search=&category_id=&store_id=&stock=1" target="_blank">جميع المخازن</a> @endif
                    @foreach ($stores as $store)
                      @if($store->id == $user->store_official) <a href="{{ route('dashboard.reports.store') }}?search=&category_id=&store_id={{$store->id}}&stock=1" target="_blank">{{$store->store_name}}</a> @endif
                    @endforeach
                    </p>
                        @if ($month->count() > 0)
                            <table class="table table-hover" id="table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الشهر</th>
                                    <th>عدد فواتير الشراء</th>
                                    <th>عدد العملاء</th>
                                    <th>عدد الطلبات</th>
                                    <th>الأجمالى</th>
                                    <th>الخصومات</th>
                                    <th>النقل</th>
                                    <th>الصافى</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($month as $index=>$data)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td style="display: none;" id="loop">{{ $data['total_price'] - ($data['discount'] + $data['transport']) }}</td>
                                        <td>{{ $data['month'] }} - {{ $data['year'] }}</td>
                                        <td>
                                            @php $foundBills = 0 @endphp
                                            @if(!$monthIn->isEmpty())
                                            @foreach ($monthIn as $monthI)
                                                @if($data['month']-$data['year'] == $monthI['month']-$monthI['year'])
                                                    {{ $monthI['numberBills'] }}
                                                    @php $foundBills = $foundBills + 1 @endphp
                                                @endif
                                            @endforeach
                                            @if($foundBills == 0) <span style="color:red">0</span> @endif
                                            @else <span style="color:red">0</span> @endif
                                        </td>
                                        <td>
                                            @php $foundclients = 0 @endphp
                                            @if(!$clients->isEmpty())
                                            @foreach ($clients as $client)
                                                @if($data['month']-$data['year'] == $client['month']-$client['year'])
                                                    {{ $client['numberClients'] }} 
                                                    @php $foundclients = $foundclients + 1 @endphp
                                                @endif
                                            @endforeach
                                            @if($foundclients == 0) <span style="color:red">0</span> @endif
                                            @else <span style="color:red">0</span> @endif
                                        </td>
                                        <td>{{ $data['numberOrders'] }}</td>
                                        <td>{{ number_format($data['total_price'], 2) }}</td>
                                        <td style="color:red">
                                            @if($data['discount'])
                                               {{ $data['discount'] }}
                                            @else
                                               0.00
                                            @endif
                                        </td>
                                        <td style="color:red">
                                            @if($data['transport'])
                                               {{ $data['transport'] }}
                                            @else
                                               0.00
                                            @endif
                                        </td>
                                        <td>{{ number_format($data['total_price'] - ($data['discount'] + $data['transport']), 2) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table><!-- end of table -->
                            <span id="val"></span>
                            @else
                                <h5>@lang('site.no_data_found')</h5>
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