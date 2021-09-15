@extends('layouts.dashboard.app')

@section('content')
<style>
    .stats {
      margin: 5px;
    }
    .stats .col {
      margin: 0;
      padding: 3;
    }
    .statContainer {
      margin: 5px;
      width: 100%;
      font-size: 13px;
      border-radius: 3px;
      background-color: #fff;
      padding: 0;
      overflow: hidden;
    }
    .statContainer .title {
      padding: 5px 10px;
      color: #fff;
    }
    .statContainer.blue .title {
      background-color: #2d72c0;
    }
    .statContainer.blue .status {
      color: #2d72c0;
    }
    
    .statContainer.yellow .title {
      background-color: #f3a254;
    }
    .statContainer.yellow .status {
      color: #f3a254;
    }
    
    .statContainer.fountainBlue .title {
      background-color: #6abebf;
    }
    .statContainer.fountainBlue .status {
      color: #6abebf;
    }
    
    .statContainer.lightBlue .title {
      background-color: #52a1e5;
    }
    .statContainer.lightBlue .status {
      color: #52a1e5;
    }
    
    .statContainer.purple .title {
      background-color: #916df6;
    }
    .statContainer.purple .status {
      color: #916df6;
    }
    
    .statContainer.pink .title {
      background-color: #ef6e85;
    }
    .statContainer.pink .status {
      color: #ef6e85;
    }
    
    .statContainer.orange .title {
      background-color: #ff7043;
    }
    .statContainer.orange .status {
      color: #ff7043;
    }
    .box-today{
        border: none;
        display: block;
        background-color: #fff;
        padding: 4px;
        text-align: center;
        margin: auto;
        width: 50%;
        border-radius: 10px;
    }
    @media (max-width: 1200px) {
      .stats .col {
        min-width: 20% !important;
      }
    }
    
    @media (max-width: 887px) {
      .stats .col {
        min-width: 25% !important;
      }
    }
    @media (max-width: 768px) {
      .stats .col {
        min-width: 50% !important;
      }
    }
    @media (max-width: 525px) {
      .stats .col {
        min-width: 100% !important;
      }
    }
</style>
    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.dashboard')</h1>

            <ol class="breadcrumb">
                <li class="active"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</li>
            </ol>
        </section>

        <section class="content">

            <div class="row">

                {{-- categories--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{ $categories_count }}</h3>

                            <p>@lang('site.categories')</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        @if (auth()->user()->hasPermission('read-categories'))
                        <a href="{{ route('dashboard.categories.index') }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                        @else
                        <a href="#" class="small-box-footer" style="cursor: no-drop;">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                        @endif
                    </div>
                </div>

                {{--products--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3>{{ $products_count }}</h3>

                            <p>@lang('site.products')</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        @if (auth()->user()->hasPermission('read-products'))
                        <a href="{{ route('dashboard.products.index') }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                        @else
                        <a href="#" class="small-box-footer" style="cursor: no-drop;">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                        @endif
                    </div>
                </div>

                {{--clients--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3>{{ $clients_count }}</h3>

                            <p>@lang('site.clients')</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-user"></i>
                        </div>
                        @if (auth()->user()->hasPermission('read-clients'))
                        <a href="{{ route('dashboard.clients.index') }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                        @else
                        <a href="#" class="small-box-footer" style="cursor: no-drop;">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                        @endif
                    </div>
                </div>

                {{--users--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3>{{ $users_count }}</h3>

                            <p>@lang('site.users')</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-users"></i>
                        </div>
                        @if (auth()->user()->hasPermission('read-users'))
                        <a href="{{ route('dashboard.users.index') }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                        @else
                        <a href="#" class="small-box-footer" style="cursor: no-drop;">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                        @endif
                    </div>
                </div>

            </div><!-- end of row -->

            <div class="box box-solid">

                <div class="box-header">
                    <h3 class="box-title">الرسم البياني للمبيعات</h3>
                </div>
                <div class="box-body border-radius-none">
                    <div class="chart" id="line-chart" style="height: 250px;"></div>
                </div>
                <!-- /.box-body -->
            </div>
            
            <h4 class="box-today">
                @php
                 $mytime = Carbon\Carbon::now('Africa/Cairo');
                 echo $mytime->format('Y-m-d');
                @endphp
            </h4>  
            <div class="row stats">
              <div class="col-lg-2 col-lg-offset-1 col-xs-6">
                <div class="statContainer blue shadow-sm">
                  <div class="title text-center">العملاء</div>
                  <div class="d-flex">
                    <div class="p-2 flex-fill text-center">
                      <h5 class="font-weight-bold" @if($clientInDay == 0) style="color:red" @endif>{{$clientInDay}}</h5>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-xs-6">
                <div class="statContainer blue shadow-sm">
                  <div class="title text-center">الطلبات</div>
                  <div class="d-flex">
                    <div class="p-2 flex-fill text-center">
                      <h5 class="font-weight-bold" @if($orderInDay == 0) style="color:red" @endif>{{$orderInDay}}</h5>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-xs-6">
                <div class="statContainer blue shadow-sm">
                  <div class="title text-center">فواتير الشراء</div>
                  <div class="d-flex">
                    <div class="p-2 flex-fill text-center">
                      <h5 class="font-weight-bold" @if($billInDay == 0) style="color:red" @endif>{{$billInDay}}</h5>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-xs-6">
                <div class="statContainer blue shadow-sm">
                  <div class="title text-center">المنتجات</div>
                  <div class="d-flex">
                    <div class="p-2 flex-fill text-center">
                      <h5 class="font-weight-bold" @if($productInDay == 0) style="color:red" @endif>{{$productInDay}}</h5>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-xs-6">
                <div class="statContainer blue shadow-sm">
                  <div class="title text-center">المرتجعات</div>
                  <div class="d-flex">
                    <div class="p-2 flex-fill text-center">
                      <h5 class="font-weight-bold" @if($bouncedInDay == 0) style="color:red" @endif>{{$bouncedInDay}}</h5>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <h4 class="box-today" style="margin-top: 20px;">
                هذا الشهر
            </h4>  
            <div class="row stats">
              <div class="col-lg-2 col-lg-offset-1 col-xs-6">
                <div class="statContainer blue shadow-sm">
                  <div class="title text-center">العملاء</div>
                  <div class="d-flex">
                    <div class="p-2 flex-fill text-center">
                      <h5 class="font-weight-bold" @if($clientInMonth == 0) style="color:red" @endif>{{$clientInMonth}}</h5>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-xs-6">
                <div class="statContainer blue shadow-sm">
                  <div class="title text-center">الطلبات</div>
                  <div class="d-flex">
                    <div class="p-2 flex-fill text-center">
                      <h5 class="font-weight-bold" @if($orderInMonth == 0) style="color:red" @endif>{{$orderInMonth}}</h5>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-xs-6">
                <div class="statContainer blue shadow-sm">
                  <div class="title text-center">فواتير الشراء</div>
                  <div class="d-flex">
                    <div class="p-2 flex-fill text-center">
                      <h5 class="font-weight-bold" @if($billInMonth == 0) style="color:red" @endif>{{$billInMonth}}</h5>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-xs-6">
                <div class="statContainer blue shadow-sm">
                  <div class="title text-center">المنتجات</div>
                  <div class="d-flex">
                    <div class="p-2 flex-fill text-center">
                      <h5 class="font-weight-bold" @if($productInMonth == 0) style="color:red" @endif>{{$productInMonth}}</h5>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-xs-6">
                <div class="statContainer blue shadow-sm">
                  <div class="title text-center">المرتجعات</div>
                  <div class="d-flex">
                    <div class="p-2 flex-fill text-center">
                      <h5 class="font-weight-bold" @if($bouncedInMonth == 0) style="color:red" @endif>{{$bouncedInMonth}}</h5>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <h4 class="box-today" style="margin-top: 20px;">
                هذه السنة 
            </h4>  
            <div class="row stats">
              <div class="col-lg-2 col-lg-offset-1 col-xs-6">
                <div class="statContainer blue shadow-sm">
                  <div class="title text-center">العملاء</div>
                  <div class="d-flex">
                    <div class="p-2 flex-fill text-center">
                      <h5 class="font-weight-bold" @if($clientInYear == 0) style="color:red" @endif>{{$clientInYear}}</h5>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-xs-6">
                <div class="statContainer blue shadow-sm">
                  <div class="title text-center">الطلبات</div>
                  <div class="d-flex">
                    <div class="p-2 flex-fill text-center">
                      <h5 class="font-weight-bold" @if($orderInYear == 0) style="color:red" @endif>{{$orderInYear}}</h5>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-xs-6">
                <div class="statContainer blue shadow-sm">
                  <div class="title text-center">فواتير الشراء</div>
                  <div class="d-flex">
                    <div class="p-2 flex-fill text-center">
                      <h5 class="font-weight-bold" @if($billInYear == 0) style="color:red" @endif>{{$billInYear}}</h5>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-xs-6">
                <div class="statContainer blue shadow-sm">
                  <div class="title text-center">المنتجات</div>
                  <div class="d-flex">
                    <div class="p-2 flex-fill text-center">
                      <h5 class="font-weight-bold" @if($productInYear == 0) style="color:red" @endif>{{$productInYear}}</h5>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-xs-6">
                <div class="statContainer blue shadow-sm">
                  <div class="title text-center">المرتجعات</div>
                  <div class="d-flex">
                    <div class="p-2 flex-fill text-center">
                      <h5 class="font-weight-bold" @if($bouncedInYear == 0) style="color:red" @endif>{{$bouncedInYear}}</h5>
                    </div>
                  </div>
                </div>
              </div>
            </div>

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@endsection

@push('scripts')

    <script>

        //line chart
        var line = new Morris.Line({
            element: 'line-chart',
            resize: true,
            data: [
                @foreach ($sales_data as $data)
                {
                    ym: "{{ $data->year }}-{{ $data->month }}", sum: "{{ $data->sum }}"
                },
                @endforeach
            ],
            xkey: 'ym',
            ykeys: ['sum'],
            labels: ['@lang('site.total')'],
            lineWidth: 2,
            hideHover: 'auto',
            gridStrokeWidth: 0.4,
            pointSize: 4,
            gridTextFamily: 'Open Sans',
            gridTextSize: 10
        });
    </script>

@endpush