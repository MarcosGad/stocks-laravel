@extends('layouts.dashboard.app')

@section('content')
<style>
    .btn-cs{
      margin: 6px;
    }
</style>
    <div class="content-wrapper">

        <section class="content-header">
            <h1>أشعارت المدفوعات</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">أشعارات المدفوعات</li>
            </ol>
        </section>

        <section class="content">

            <div class="row">

                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h4>الفواتير المستحقة الدفع من العملاء</h4>
                        </div><!-- end of box header -->
                        <div class="box-body">
                            @if ($backorderOs->count() > 0)
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>تاريخ</th>
                                    <th>أسم العميل</th>
                                    <th></th>
                                </tr>
                                </thead>
                                
                                <tbody>
                                @foreach ($backorderOs as $index=>$backorderO)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><a href="{{ route('dashboard.orders.index') }}?search=&id={{$backorderO->number}}&created_at=" target="_blank">{{ $backorderO->date }}</a></td>
                                        @php
                                            $order = App\Order::where('id', $backorderO->number)->get();
                                        @endphp
                                        <td><a href="{{ route('dashboard.reports.historyclient', $order[0]->client->id) }}" target="_blank">{{$order[0]->client->name}}</a></td>
                                        <td>
                                         @if(in_array(Auth::id(),array(1,2)) && $backorderO->status == 0)
                                            <a href="{{ route('dashboard.reports.paymentNoticesStatus', $backorderO->id) }}" class="btn btn-danger btn-sm btn-cs"><i class="fa fa-check"></i></a>
                                         @endif
                                         @if($order[0]->attainment == 0 && in_array(Auth::id(),array(1,2)))
                                            <a href="{{ route('dashboard.reports.paymentNoticesAttainment', [$backorderO->number,$backorderO->id]) }}" class="btn btn-primary btn-sm btn-cs"><i class="fa fa-check"></i> تم التحصيل</a>
                                         @endif
                                         @if($order[0]->attainment == 1 && in_array(Auth::id(),array(1,2)))
                                            <a href="#" class="btn btn-success btn-sm btn-cs" disabled>مدفوعة</a>
                                         @endif
                                        </td>
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
                            <h4>الفواتير المستحقة الدفع الى الموردين</h4>
                        </div><!-- end of box header -->
                        <div class="box-body">
                            @if ($backorderBs->count() > 0)
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>تاريخ</th>
                                    <th>أسم المورد</th>
                                    <th></th>
                                </tr>
                                </thead>
                                
                                <tbody>
                                @foreach ($backorderBs as $index=>$backorderB)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        @php
                                            $bill = App\Bill::select('id','supplier_name')->where('invoice_number', $backorderB->number)->first();
                                        @endphp
                                        @if(auth()->user()->id == 1 || auth()->user()->id == 2)
                                        <td>
                                            <a href="{{ route('dashboard.reports.reportsPurchaseInvoicesShow', [$bill->id,$backorderB->number]) }}" target="_blank">{{ $backorderB->date }}</a>
                                        </td>
                                        @else
                                        <td>{{ $backorderB->date }}</td>
                                        @endif
                                        <td>{{$bill->supplier_name}}</td>
                                        <td>
                                            @if(in_array(Auth::id(),array(1,2)) && $backorderB->status == 0)
                                                  <a href="{{ route('dashboard.reports.paymentNoticesStatus', $backorderB->id) }}" class="btn btn-danger btn-sm btn-cs"><i class="fa fa-check"></i></a>
                                            @endif
                                        </td>
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