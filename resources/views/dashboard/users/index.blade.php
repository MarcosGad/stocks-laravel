@extends('layouts.dashboard.app')

@section('content')
<style>
    .btn-cs{
      display: block;
      margin: 6px;
      width: 100%;
    }
</style>

<div class="content-wrapper">

<section class="content-header">

    <h1>@lang('site.users') <small>{{ $users->total() }}</small> </h1>

    <ol class="breadcrumb">
            <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
            <li class="active">@lang('site.users')</li>
    </ol>

</section>

<section class="content">

     <div class="box box-primary">
         <div class="box-header with-border">

            <form action="{{ route('dashboard.users.index') }}" method="get">

                <div class="row">

                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="الأسم الأول أو الأخير أو البريد الألكترونى" value="{{ request()->search }}">
                    </div>

                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> @lang('site.search')</button>
                        @if (auth()->user()->hasPermission('create-users'))
                            <a href="{{ route('dashboard.users.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> @lang('site.add')</a>
                         @else
                            <a href="#" class="btn btn-primary disabled"><i class="fa fa-plus"></i> @lang('site.add')</a>
                        @endif
                    </div>

                </div>
                </form><!-- end of form -->

         </div>

         <div class="box-body">

        @if ($users->count() > 0)

                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('site.first_name')</th>
                        <th>@lang('site.last_name')</th>
                        <th>@lang('site.email')</th>
                        <th>@lang('site.image')</th>
                        <th>فواتير الشراء</th>
                        <th>العملاء</th>
                        <th>الطلبات</th>
                        <th>الربح</th>
                        <th>المخزن</th>
                        <th>بواسطة</th>
                        <th>تعديل</th>
                        <th>@lang('site.action')</th>
                    </tr>
                    </thead>

                            <tbody>
                                @foreach ($users as $index=>$user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $user->first_name }}</td>
                                        <td>{{ $user->last_name }}</td>
                                        <td><a href="{{ route('dashboard.reports.historyUser', $user->id) }}" target="_blank">{{ $user->email }}</a></td>
                                        <td><img src="{{ $user->image_path }}" style="width: 100px;" class="img-thumbnail" alt=""></td>
                                        <td>
                                            <?php $number = 0; ?>
                                            @foreach ($bills as $indexKey => $bill) 
                                                @if($bill->add_by == $user->id)
                                                      <?php $number++ ?> 
                                                @endif
                                            @endforeach
                                            <span @if($number == 0) style="color:red" @endif>{{ $number }}</span>
                                        </td>
                                        <td>
                                            <?php $numberOne = 0; ?>
                                            @foreach ($clients as $indexKey => $client) 
                                                @if($client->add_by == $user->id)
                                                      <?php $numberOne++ ?> 
                                                @endif
                                            @endforeach
                                            <span @if($numberOne == 0) style="color:red" @endif>{{ $numberOne }}</span>
                                        </td>
                                        <td>
                                            <?php $numberTwo = 0; ?>
                                            @foreach ($orders as $indexKey => $order) 
                                                @if($order->user_id == $user->id)
                                                      <?php $numberTwo++ ?> 
                                                @endif
                                            @endforeach
                                            <span @if($numberTwo == 0) style="color:red" @endif>{{ $numberTwo }}</span>
                                        </td>
                                        <td>
                                            @php
                                              $rbha = APP\Order::select(
                                                DB::raw('SUM(total_price) as total_price'),
                                                DB::raw('SUM(discount) as discount'),
                                                DB::raw('SUM(transport) as transport')
                                              )->where('user_id',$user->id)->get();
                                            @endphp
                                            <span @if($rbha[0]->total_price - ($rbha[0]->discount + $rbha[0]->transport) == 0) style="color:red" @endif>{{ number_format($rbha[0]->total_price - ($rbha[0]->discount + $rbha[0]->transport), 2) }}</span>
                                        </td>
                                        <td>
                                            @if($user->store_official == 0) <a href="{{ route('dashboard.reports.store') }}?search=&category_id=&store_id=&stock=1" target="_blank">جميع المخازن</a> @endif
                                            @foreach ($stores as $store)
                                              @if($store->id == $user->store_official) <a href="{{ route('dashboard.reports.store') }}?search=&category_id=&store_id={{$store->id}}&stock=1" target="_blank">{{$store->store_name}}</a> @endif
                                            @endforeach
                                        </td>
                                    <td>
                                      @foreach ($userFs as $userF) 
                                         @if($userF->id == $user->add_by)
                                            <a href="{{ route('dashboard.reports.historyUser', $userF->id) }}" target="_blank">{{strstr($userF->email, '@', true)}}</a>
                                         @endif
                                      @endforeach
                                    </td>
                                    <td>
                                      @if($user->update_by != 0)
                                      @foreach ($userFs as $userF) 
                                         @if($userF->id == $user->update_by)
                                            <a href="{{ route('dashboard.reports.historyUser', $userF->id) }}" target="_blank">{{strstr($userF->email, '@', true)}}</a>
                                         @endif
                                      @endforeach
                                      @else
                                      <span style="color:red">لم يعديل</span>
                                      @endif
                                    </td>
                                        <td>
                                        @if (auth()->user()->hasPermission('update-users'))
                                            <a href="{{ route('dashboard.users.edit', $user->id) }}" class="btn btn-info btn-sm btn-cs"><i class="fa fa-edit"></i> @lang('site.edit')</a>
                                        @else
                                            <a href="#" class="btn btn-info btn-sm disabled btn-cs"><i class="fa fa-edit"></i> @lang('site.edit')</a>
                                        @endif
                                        @if (auth()->user()->hasPermission('delete-users') && $number == 0 && $numberOne == 0 && $numberTwo == 0)
                                        <form action="{{ route('dashboard.users.destroy', $user->id) }}" method="post">
                                                {{ csrf_field() }}
                                                {{ method_field('delete') }}
                                                <button type="submit" class="btn btn-danger delete btn-sm btn-cs"><i class="fa fa-trash"></i> @lang('site.delete')</button>
                                            </form><!-- end of form -->
                                        @else
                                            <button class="btn btn-danger btn-sm btn-cs" style="cursor: no-drop;"><i class="fa fa-trash"></i> @lang('site.delete')</button>
                                        @endif
                                        </td>
                                    
                                    </tr>

                                @endforeach
                            </tbody>

                </table><!-- end of table -->

                {{ $users->appends(request()->query())->links() }}

            @else
                        
                <h2>@lang('site.no_data_found')</h2>
                        
            @endif
         </div>
     </div>

</section><!-- end of content -->

</div><!-- end of content wrapper -->


@endsection
