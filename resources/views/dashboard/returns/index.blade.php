@extends('layouts.dashboard.app')

@section('content')
    <div class="content-wrapper">

        <section class="content-header">
            <h1>المرتجعات <small>{{ $bounceds->total() }}</small> </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">المرتجعات</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <form action="{{ route('dashboard.returns.index') }}" method="get">

                        <div class="row">

                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="رقم الفاتورة" value="{{ request()->search }}">
                            </div>
                            
                            @php
                              $returns = array(
                              "1"=>"من عميل",
                              "2"=>"الى مورد");
                            @endphp
                            <div class="col-md-4">
                                <select name="return_type" class="form-control">
                                     <option value="">كل المترجعات</option>
                                     @foreach ($returns as $return=>$returnValue)
                                        <option value="{{ $return }}" {{ request()->return_type == $return ? 'selected' : '' }}>{{ $returnValue }}</option>
                                     @endforeach
                                </select>
                            </div>
 
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> @lang('site.search')</button>
                                <!--@if (auth()->user()->hasPermission('create-orders'))-->
                                <!--    <a href="{{ route('dashboard.returns.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> @lang('site.add')</a>-->
                                <!--@else-->
                                <!--    <a href="#" class="btn btn-primary disabled"><i class="fa fa-plus"></i> @lang('site.add')</a>-->
                                <!--@endif-->
                            </div>

                        </div>
                    </form><!-- end of form -->

                </div><!-- end of box header -->

                <div class="box-body">

                    @if ($bounceds->count() > 0)

                        <table class="table table-hover">

                            <thead>
                            <tr>
                                <th>#</th>
                                <th>رقم الفاتورة</th>
                                <th>نوع المرتجع</th>
                                <th>بواسطة</th>
                                <th>حالة المرتجع</th>
                                <th>@lang('site.created_at')</th>
                                <th></th>
                            </tr>
                            </thead>
                            
                            <tbody>
                            @foreach ($bounceds as $index=>$bounced)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($bounced->return_type == 1) <a href="{{ route('dashboard.orders.index') }}?search=&id={{$bounced->bill_number_o}}&created_at=" target="_blank">{{$bounced->bill_number_o}}</a> @endif
                                        @if($bounced->return_type == 2) <a href="{{ route('dashboard.purchaseInvoices.index') }}?search={{$bounced->bill_number_b}}" target="_blank">{{$bounced->bill_number_b}}</a> @endif
                                    </td>
                                    <td>
                                        @if($bounced->return_type == 1)من عميل@endif
                                        @if($bounced->return_type == 2)الى مورد@endif
                                    </td>
                                    <td>
                                        @foreach ($users as $user) 
                                            @if($user->id == $bounced->add_by)
                                               <a href="{{ route('dashboard.reports.historyUser', $user->id) }}" target="_blank">{{strstr($user->email, '@', true)}}</a>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($bounced->status == 0) قيد التنفيذ @endif
                                        @if($bounced->status == 1)
                                           تم التنفيذ عن طريق :-
                                           @foreach ($users as $user) 
                                                @if($user->id == $bounced->status_userId)
                                                   {{$user->email }}
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>{{ $bounced->created_at->toFormattedDateString() }}</td>
                                    <td>
                                        @if (auth()->user()->hasPermission('delete-orders'))
                                            <form action="{{ route('dashboard.returns.destroy', $bounced->id) }}" method="post" style="display: inline-block;">
                                                {{ csrf_field() }}
                                                {{ method_field('delete') }}
                                                 <input type="hidden" name="id" value="{{$bounced->id}}">
                                                 <input type="hidden" name="return_type" value="{{$bounced->return_type}}">
                                                 <input type="hidden" name="bill_number_o" value="{{$bounced->bill_number_o}}">
                                                 <input type="hidden" name="bill_number_b" value="{{$bounced->bill_number_b}}">
                                                <button type="submit" class="btn btn-danger delete btn-sm"><i class="fa fa-trash"></i> @lang('site.delete')</button>
                                            </form><!-- end of form -->
                                        @else
                                            <button class="btn btn-danger btn-sm disabled"><i class="fa fa-trash"></i> @lang('site.delete')</button>
                                        @endif
                                        @if($bounced->status == 0 && in_array(Auth::id(),array(1,2,3)))
                                            <a href="{{ route('dashboard.return.status', $bounced->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-check"></i> تأكيد المرتجع</a>
                                        @endif
                                        @if(in_array(Auth::id(),array(1,2,3)))
                                            <a href="{{ route('dashboard.return.returnsShow', $bounced->id) }}" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-check"></i> عرض المرتجع </a>
                                        @endif
                                    </td>
                                </tr>
                            
                            @endforeach
                            </tbody>

                        </table><!-- end of table -->
                        
                        {{ $bounceds->appends(request()->query())->links() }}
                        
                    @else
                        
                        <h2>@lang('site.no_data_found')</h2>
                        
                    @endif

                </div><!-- end of box body -->


            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@endsection