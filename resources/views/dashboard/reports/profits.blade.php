@extends('layouts.dashboard.app')

@section('content')
<style>
    body{font-size: 12px;}
    .btn-cs{
      margin: 6px;
    }
</style>
    <div class="content-wrapper">

        <section class="content-header">
            <h1>الأرباح</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">الأرباح</li>
            </ol>
        </section>

        <section class="content">

            <div class="row">

                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h4>الأرباح اليومية</h4>
                        </div><!-- end of box header -->
                        <div class="box-body">
                            @if ($day->count() > 0)
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اليوم</th>
                                    <th>الأجمالى</th>
                                    <th>الخصومات</th>
                                    <th>مصاريف النقل</th>
                                    <th>الأجمالى الصافى</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($day as $index=>$dataD)
                                    <tr @if($tomonth == $dataD['month']) style="background-color:#b4dcb4;" @endif>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $dataD['day'] }}</td>
                                        <td>{{ $dataD['total_price'] }}</td>
                                        <td @if($dataD->discount > 0) style="color:red" @endif>
                                            @if($dataD['discount'])
                                               {{ $dataD['discount'] }}
                                            @else
                                               0.00
                                            @endif
                                        </td>
                                        <td @if($dataD->transport > 0) style="color:red" @endif>
                                            @if($dataD['transport'])
                                               {{ $dataD['transport'] }}
                                            @else
                                               0.00
                                            @endif
                                        </td>
                                        <td>{{ number_format( $dataD['total_price'] - ($dataD['discount'] + $dataD['transport']), 2) }}</td>
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
                            <h4>الأرباح الشهرية</h4>
                        </div><!-- end of box header -->
                        <div class="box-body">
                            @if ($month->count() > 0)
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الشهر</th>
                                    <th>الأجمالى</th>
                                    <th>الخصومات</th>
                                    <th>النقل</th>
                                    <th>المبيعات</th>
                                    <th>المشتريات</th>
                                    <th>الصافى</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($month as $index=>$data)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $data['month'] }} - {{ $data['year'] }}</td>
                                        <td>{{ $data['total_price'] }}</td>
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
                                        <td>{{ number_format( $data['total_price'] - ($data['discount'] + $data['transport']), 2) }}</td>
                                          @foreach ($monthIn as $dataIn)
                                                @if($data['month']-$data['year'] == $dataIn['month']-$dataIn['year'])
                                                   <td style="color:red">{{ number_format($dataIn['total'], 2) }}</td>
                                                   <td @if($data['total_price'] - ($data['discount'] + $data['transport']) < 0) style="color:red" @else style="color:green" @endif>
                                                        {{ number_format( $data['total_price'] - ($data['discount'] + $data['transport']) - $dataIn['total'], 2) }}
                                                   </td>
                                                @endif
                                          @endforeach
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