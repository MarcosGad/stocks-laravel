@extends('layouts.dashboard.app')

@section('content')
<style>
    body{font-size: 12px;}
</style>
    <div class="content-wrapper">

        <section class="content-header">
            <h1>الاحصائيات</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">الأحصائيات</li>
            </ol>
        </section>

        <section class="content">

            <div class="row">

                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h4>الأحصائيات الشهرية</h4>
                        </div><!-- end of box header -->
                        <div class="box-body">
                            @if ($monthOrders->count() > 0)
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الشهر</th>
                                    <th>الطلبات</th>
                                    <th>العملاء</th>
                                    <th>فواتير الشراء</th>
                                    <th>المنتجات</th>
                                    <th>المرتجعات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($monthOrders as $index=>$monthOrder)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $monthOrder->month }} - {{ $monthOrder->year }}</td>
                                        <td>{{ $monthOrder->countOrder }}</td>
                                        
                                        @php $foundClients = 0 @endphp
                                        @foreach ($monthClients as $monthClient)
                                            @if($monthOrder['month']-$monthOrder['year'] == $monthClient['month']-$monthClient['year'])
                                               <td>{{ $monthClient->countClient }}</td>
                                               @php $foundClients = $foundClients + 1 @endphp
                                            @endif
                                        @endforeach
                                        @if($foundClients == 0) <td style="color:red">0</td> @endif
                                        
                                        @php $foundBills = 0 @endphp
                                        @foreach ($monthBills as $monthBill)
                                            @if($monthOrder['month']-$monthOrder['year'] == $monthBill['month']-$monthBill['year'])
                                               <td>{{ $monthBill->countBill }}</td>
                                               @php $foundBills = $foundBills + 1 @endphp
                                            @endif
                                        @endforeach
                                        @if($foundBills == 0) <td style="color:red">0</td> @endif
                                        
                                        @php $foundProducts = 0 @endphp
                                        @foreach ($monthProducts as $monthProduct)
                                            @if($monthOrder['month']-$monthOrder['year'] == $monthProduct['month']-$monthProduct['year'])
                                               <td>{{ $monthProduct->countProduct }}</td>
                                               @php $foundProducts = $foundProducts + 1 @endphp
                                            @endif
                                        @endforeach
                                        @if($foundProducts == 0) <td style="color:red">0</td> @endif
                                        
                                        @php $foundBounceds = 0 @endphp
                                        @foreach ($monthBounceds as $monthBounced)
                                            @if($monthOrder['month']-$monthOrder['year'] == $monthBounced['month']-$monthBounced['year'])
                                               <td>{{ $monthBounced->countBounced }}</td>
                                               @php $foundBounceds = $foundBounceds + 1 @endphp
                                            @endif
                                        @endforeach
                                        @if($foundBounceds == 0) <td style="color:red">0</td> @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table><!-- end of table -->
                            @else
                                <h5>@lang('site.no_data_found')</h5>
                            @endif
                        </div><!-- end of box body -->
                    </div><!-- end of box -->
                    
                </div><!-- end of col -->

                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h4>الأحصائيات السانوية</h4>
                        </div><!-- end of box header -->
                        <div class="box-body">
                             @if ($yearOrders->count() > 0)
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>السنة</th>
                                    <th>الطلبات</th>
                                    <th>العملاء</th>
                                    <th>فواتير الشراء</th>
                                    <th>المنتجات</th>
                                    <th>المرتجعات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($yearOrders as $index=>$yearOrder)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $yearOrder->year }}</td>
                                        <td>{{ $yearOrder->countOrderyear }}</td>
                                        
                                        @php $foundyClients = 0 @endphp
                                        @foreach ($yearClients as $yearClient)
                                            @if($yearOrder['year'] == $yearClient['year'])
                                               <td>{{ $yearClient->countClientyear }}</td>
                                               @php $foundyClients = $foundyClients + 1 @endphp
                                            @endif
                                        @endforeach
                                        @if($foundyClients == 0) <td style="color:red">0</td> @endif
                                        
                                        @php $foundyBills = 0 @endphp
                                        @foreach ($yearBills as $yearBills)
                                            @if($yearOrder['year'] == $yearBills['year'])
                                               <td>{{ $yearBills->countBillyear }}</td>
                                               @php $foundyBills = $foundyBills + 1 @endphp
                                            @endif
                                        @endforeach
                                        @if($foundyBills == 0) <td style="color:red">0</td> @endif
                                        
                                        @php $foundyProducts = 0 @endphp
                                        @foreach ($yearProducts as $yearProduct)
                                            @if($yearOrder['year'] == $yearProduct['year'])
                                               <td>{{ $yearProduct->countProductyear }}</td>
                                               @php $foundyProducts = $foundyProducts + 1 @endphp
                                            @endif
                                        @endforeach
                                        @if($foundyProducts == 0) <td style="color:red">0</td> @endif
                                        
                                        @php $foundyBounceds = 0 @endphp
                                        @foreach ($yearBounceds as $yearBounced)
                                            @if($yearOrder['year'] == $yearBounced['year'])
                                               <td>{{ $yearBounced->countBouncedyear }}</td>
                                               @php $foundyBounceds = $foundyBounceds + 1 @endphp
                                            @endif
                                        @endforeach
                                        @if($foundyBounceds == 0) <td style="color:red">0</td> @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table><!-- end of table -->
                            @else
                                <h5>@lang('site.no_data_found')</h5>
                            @endif
                        </div><!-- end of box body -->
                    </div><!-- end of box -->
                </div><!-- end of col -->

            </div><!-- end of row -->

        </section><!-- end of content section -->

    </div><!-- end of content wrapper -->
@endsection