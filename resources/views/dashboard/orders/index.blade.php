@extends('layouts.dashboard.app')

@section('content')
<style>
    .btn-cs{
        display: block;
        margin-bottom: 6px;
        width: 100%;
        font-size: 8px;
        font-weight: bold;
    }
    @media (max-width: 767px) {
      .table-responsive .dropdown-menu {
        position: relative; /* Sometimes needs !important */
      }
    }
    .open>.dropdown-menu {
        padding:5px;
    }
    .btn-group>.btn:first-child{
      width: 85px;
      height: 23px;
    }
    .dropdown-menu {
      min-width: 85px;
    }
    .btn .caret{
      margin-top: -10px;
    }
    .trans {position:relative;color: #f39c12;}
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0,0,0,0);
        border: 0;
    }
    .icon-trans + .sr-only {
        padding: 0.25em;
        margin: 0;
        color: #000;
        background: #eee;
        border: 1px solid #ccc;
        border-radius: 2px;
        font: 11px sans-serif;
        z-index: 2; 
    }
    .trans:focus .icon-trans + .sr-only, 
    .trans:hover .icon-trans + .sr-only {
      clip: auto;
      width: auto;
      height: auto;
      bottom: 100%;
      left: 100%;
    }
</style>
    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.orders')
                <small>{{ $orders->total() }}</small>
            </h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">@lang('site.orders')</li>
            </ol>
        </section>

        <section class="content box-r">

            <div class="row">

                <div class="col-md-8">

                    <div class="box box-primary">

                        <div class="box-header">

                            <form action="{{ route('dashboard.orders.index') }}" method="get">

                                <div class="row">

                                    <div class="col-md-2">
                                        <input type="text" name="search" class="form-control" placeholder="أسم العميل" value="{{ request()->search }}">
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <select name="id" class="form-control">
                                            <option value="">الفواتير</option>
                                            @foreach ($ordersIds as $ordersId)
                                                <option value="{{ $ordersId->id }}" {{ request()->id == $ordersId->id ? 'selected' : '' }}>{{ $ordersId->id }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <select name="created_at" class="form-control">
                                            <option value="">الأضافة</option>
                                            @foreach ($ordersDate as $orderDate)
                                                <option value="{{ $orderDate[0]->date }}" {{ request()->created_at == $orderDate[0]->date ? 'selected' : '' }}>{{ $orderDate[0]->date }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <select name="dateO" class="form-control">
                                            <option value="">الطلب</option>
                                            @foreach ($ordersDateO as $orderDateO)
                                                <option value="{{ $orderDateO[0]->dateO }}" {{ request()->dateO == $orderDateO[0]->dateO ? 'selected' : '' }}>{{ $orderDateO[0]->dateO }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    @if(in_array(Auth::id(),array(1,2,3)))
                                        <div class="col-md-2">
                                            <select name="user_id" class="form-control">
                                                <option value="">كل المستخدمين</option>
                                                @foreach ($users as $user)
                                                <option value="{{ $user->id }}" {{ request()->user_id == $user->id ? 'selected' : '' }}>{{ $user->email }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                    </div>

                                </div><!-- end of row -->

                            </form><!-- end of form -->

                        </div><!-- end of box header -->

                        @if ($orders->count() > 0)

                            <div class="box-body table-responsive">

                                <table class="table table-hover">
                                    <tr>
                                        <th>@lang('site.client_name')</th>
                                        <th>@lang('site.price')</th>
                                    {{--<th>@lang('site.status')</th>--}}
                                        <th>الطلب</th>
                                        <th>بواسطة</th>
                                        <th>حالة</th>
                                        <th>المرتجع</th>
                                        <th></th>
                                        <th></th>
                                    </tr>

                                    @foreach ($orders as $order)
                                        <tr>
                                            <td><a href="{{ route('dashboard.reports.historyclient', $order->client->id) }}" target="_blank">{{ $order->client->name }}</a></td>
                                            <td @if($order->attainment == 1) style="color:green;font-weight: bold;" @endif>{{ number_format($order->total_price + $order->transport - $order->discount, 2) }}</td>
                                            {{--<td>--}}
                                                {{--<button--}}
                                                    {{--data-status="@lang('site.' . $order->status)"--}}
                                                    {{--data-url="{{ route('dashboard.orders.update_status', $order->id) }}"--}}
                                                    {{--data-method="put"--}}
                                                    {{--data-available-status='["@lang('site.processing')", "@lang('site.finished') "]'--}}
                                                    {{--class="order-status-btn btn {{ $order->status == 'processing' ? 'btn-warning' : 'btn-success disabled' }} btn-sm"--}}
                                                {{-->--}}
                                                    {{--@lang('site.' . $order->status)--}}
                                                {{--</button>--}}
                                            {{--</td>--}}
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
                                                   تم التنفيذ بواسطة 
                                                   @foreach ($users as $user) 
                                                        @if($user->id == $order->status_userId)
                                                           <a href="{{ route('dashboard.reports.historyUser', $user->id) }}" target="_blank">{{strstr($user->email, '@', true)}}</a>
                                                        @endif
                                                    @endforeach
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
                                            <td>
                                                @if($order->trans != 0)
                                                <a href="#" class="trans"><i class="fa fa-rocket icon-trans" aria-hidden="true"></i><span class="sr-only">
                                                  @foreach ($users as $user) 
                                                     @if($user->id == $order->trans)
                                                        <span>{{strstr($user->email, '@', true)}}</span>
                                                     @endif
                                                  @endforeach
                                                </span></a>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                  <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                  </button>
                                                  <ul class="dropdown-menu" role="menu">
                                                    <button class="btn btn-primary btn-sm order-products btn-cs" data-url="{{ route('dashboard.orders.products', $order->id) }}" data-method="get"><i class="fa fa-list"></i>
                                                      @lang('site.show')
                                                    </button>
                                                    @if(in_array(Auth::id(),array(1,2,3)) && $order->status == 0)
                                                       <a href="{{ route('dashboard.orders.status', $order->id) }}" class="btn btn-primary btn-sm btn-cs"><i class="fa fa-check"></i> @lang('site.confirm')</a>
                                                    @endif
                                                    @if($order->attainment == 0 && in_array(Auth::id(),array(1,2)))
                                                       <a href="{{ route('dashboard.orders.attainment', $order->id) }} btn-cs" class="btn btn-primary btn-sm btn-cs"><i class="fa fa-check"></i>تم التحصيل</a>
                                                    @endif
                                                    <a href="{{ route('dashboard.orders.orderReturn', $order->id) }}" class="btn btn-warning btn-sm btn-cs"><i class="fa fa-undo"></i> مرتجع</a>
                                                    <!--@if (auth()->user()->hasPermission('update-orders'))-->
                                                    <!--@else-->
                                                    <!--@endif-->
                                                    @if(Auth::user()->id == 1 || Auth::user()->id == 2)
                                                        <a href="{{ route('dashboard.orders.transfersOrder', [$order->user_id,$order->id]) }}" class="btn btn-warning btn-sm btn-cs"><i class="fa fa-rocket"></i> تحويل الطلب</a>
                                                    @endif
                                                    @if(in_array(Auth::id(),array(1,2)) || $order->status == 0)
                                                      <a href="{{ route('dashboard.clients.orders.edit', ['client' => $order->client->id, 'order' => $order->id]) }}" class="btn btn-warning btn-sm btn-cs edit-order"><i class="fa fa-pencil"></i> @lang('site.edit')</a>
                                                    @endif
                                                    @if(in_array(Auth::id(),array(1,2)))
                                                       <a href="{{ route('dashboard.orders.discount', $order->id) }} btn-cs" class="btn btn-warning btn-sm btn-cs"><i class="fa fa-usd"></i> خصم ونقل</a>
                                                    @endif
                                                    @if (auth()->user()->hasPermission('delete-orders'))
                                                        @if($order->status == 0)
                                                        <form action="{{ route('dashboard.orders.destroy', $order->id) }}" method="post">
                                                            {{ csrf_field() }}
                                                            {{ method_field('delete') }}
                                                            <button type="submit" class="btn btn-danger btn-sm delete btn-cs"><i class="fa fa-trash"></i> @lang('site.delete')</button>
                                                        </form>
                                                        @endif
                                                        @if($order->status == 1 && in_array(Auth::id(),array(1,2)))
                                                        <form action="{{ route('dashboard.orders.destroy', $order->id) }}" method="post">
                                                            {{ csrf_field() }}
                                                            {{ method_field('delete') }}
                                                            <button type="submit" class="btn btn-danger btn-sm delete btn-cs"><i class="fa fa-trash"></i> @lang('site.delete')</button>
                                                        </form>
                                                        @endif
                                                    @else
                                                        <a href="#" class="btn btn-danger btn-sm btn-cs" disabled><i class="fa fa-trash"></i> @lang('site.delete')</a>
                                                    @endif
                                                  </ul>
                                                </div>
                                            </td>
                                        </tr>

                                    @endforeach

                                </table><!-- end of table -->

                                {{ $orders->appends(request()->query())->links() }}

                            </div>

                        @else

                            <div class="box-body">
                                <h3>@lang('site.no_records')</h3>
                            </div>

                        @endif

                    </div><!-- end of box -->

                </div><!-- end of col -->

                <div class="col-md-4">

                    <div class="box box-primary">

                        <div class="box-header">
                            <h3 class="box-title" style="margin-bottom: 10px">@lang('site.show_products')</h3>
                        </div><!-- end of box header -->

                        <div class="box-body">

                            <div style="display: none; flex-direction: column; align-items: center;" id="loading">
                                <div class="loader"></div>
                                <p style="margin-top: 10px">@lang('site.loading')</p>
                            </div>

                            <div id="order-product-list">

                            </div><!-- end of order product list -->

                        </div><!-- end of box body -->

                    </div><!-- end of box -->

                </div><!-- end of col -->

            </div><!-- end of row -->

        </section><!-- end of content section -->

    </div><!-- end of content wrapper -->
    <script>
        setInterval(function() {
          window.location.reload();
        }, 300000); 
    </script>

@endsection
