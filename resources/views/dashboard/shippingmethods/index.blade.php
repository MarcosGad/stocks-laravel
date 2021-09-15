@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>طرق واماكن الشحن <small>{{ $shippingmethods->total() }}</small> </h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">طرق واماكن الشحن</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <form action="{{ route('dashboard.shippingmethods.index') }}" method="get">

                        <div class="row">

                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="أسم طريقة الشحن أو رقم التليفون" value="{{ request()->search }}">
                            </div>

                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> @lang('site.search')</button>
                                @if (auth()->user()->hasPermission('create-users'))
                                    <a href="{{ route('dashboard.shippingmethods.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                @else
                                    <a href="#" class="btn btn-primary disabled"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                @endif
                            </div>

                        </div>
                    </form><!-- end of form -->

                </div><!-- end of box header -->

                <div class="box-body">

                    @if ($shippingmethods->count() > 0)

                        <table class="table table-hover">

                            <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('site.name')</th>
                                <th>@lang('site.phone')</th>
                                <th>بواسطة</th>
                                <th>تعديل</th>
                                <th>@lang('site.action')</th>
                            </tr>
                            </thead>
                            
                            <tbody>
                            @foreach ($shippingmethods as $index=>$shippingmethod)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><a href="{{ route('dashboard.reports.historyShippingmethod', $shippingmethod->id) }}" target="_blank">{{ $shippingmethod->name }}</a></td>
                                    <td>{{ is_array($shippingmethod->phone) ? implode('-', $shippingmethod->phone) : $shippingmethod->phone }}</td>
                                    <td>
                                        @foreach ($users as $user) 
                                            @if($user->id == $shippingmethod->add_by)
                                                  <a href="{{ route('dashboard.reports.historyUser', $user->id) }}" target="_blank">{{strstr($user->email, '@', true)}}</a>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                      @if($shippingmethod->update_by != 0)
                                      @foreach ($users as $user) 
                                         @if($user->id == $shippingmethod->update_by)
                                            <a href="{{ route('dashboard.reports.historyUser', $user->id) }}" target="_blank">{{strstr($user->email, '@', true)}}</a>
                                         @endif
                                      @endforeach
                                      @else
                                      <span style="color:red">لم يعديل</span>
                                      @endif
                                    </td>
                                    <td>
                                        @if (auth()->user()->hasPermission('update-users'))
                                            <a href="{{ route('dashboard.shippingmethods.edit', $shippingmethod->id) }}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i> @lang('site.edit')</a>
                                        @else
                                            <a href="#" class="btn btn-info btn-sm disabled"><i class="fa fa-edit"></i> @lang('site.edit')</a>
                                        @endif
                                        @if (auth()->user()->hasPermission('delete-users'))
                                            <form action="{{ route('dashboard.shippingmethods.destroy', $shippingmethod->id) }}" method="post" style="display: inline-block">
                                                {{ csrf_field() }}
                                                {{ method_field('delete') }}
                                                <button type="submit" class="btn btn-danger delete btn-sm"><i class="fa fa-trash"></i> @lang('site.delete')</button>
                                            </form><!-- end of form -->
                                        @else
                                            <button class="btn btn-danger btn-sm disabled"><i class="fa fa-trash"></i> @lang('site.delete')</button>
                                        @endif
                                    </td>
                                </tr>
                            
                            @endforeach
                            </tbody>

                        </table><!-- end of table -->
                        
                        {{ $shippingmethods->appends(request()->query())->links() }}
                        
                    @else
                        
                        <h2>@lang('site.no_data_found')</h2>
                        
                    @endif

                </div><!-- end of box body -->


            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@endsection