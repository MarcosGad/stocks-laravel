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

            <h1>المخازن <small>{{ $stores->total() }}</small> </h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">المخازن</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <form action="{{ route('dashboard.stores.index') }}" method="get">

                        <div class="row">

                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="أسم المخزن" value="{{ request()->search }}">
                            </div>
 
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> @lang('site.search')</button>
                                @if (auth()->user()->hasPermission('create-users'))
                                    <a href="{{ route('dashboard.stores.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                @else
                                    <a href="#" class="btn btn-primary disabled"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                @endif
                            </div>

                        </div>
                    </form><!-- end of form -->

                </div><!-- end of box header -->

                <div class="box-body">

                    @if ($stores->count() > 0)

                        <table class="table table-hover">

                            <thead>
                            <tr>
                                <th>#</th>
                                <th>أسم المخزن</th>
                                <th>عنوان المخزن</th>
                                <th>المسئول عن المخزن</th>
                                <th>بواسطة</th>
                                <th>تعديل</th>
                                <th>عدد الأصناف الموجودة</th>
                                <th>@lang('site.action')</th>
                            </tr>
                            </thead>
                            
                            <tbody>
                            @foreach ($stores as $index=>$store)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><a href="{{ route('dashboard.reports.store') }}?search=&category_id=&store_id={{$store->id}}&stock=1" target="_blank">{{ $store->store_name }}</a></td>
                                    <td>{{ $store->store_address }}</td>
                                    <td>
                                        @foreach ($users as $user) 
                                            @if($user->id == $store->store_respon)
                                                  <a href="{{ route('dashboard.reports.historyUser', $user->id) }}" target="_blank">{{strstr($user->email, '@', true)}}</a>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($users as $user) 
                                            @if($user->id == $store->add_by)
                                                  <a href="{{ route('dashboard.reports.historyUser', $user->id) }}" target="_blank">{{strstr($user->email, '@', true)}}</a>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                      @if($store->update_by != 0)
                                      @foreach ($users as $user) 
                                         @if($user->id == $store->update_by)
                                            <a href="{{ route('dashboard.reports.historyUser', $user->id) }}" target="_blank">{{strstr($user->email, '@', true)}}</a>
                                         @endif
                                      @endforeach
                                      @else
                                      <span style="color:red">لم يعديل</span>
                                      @endif
                                    </td>
                                    <td>
                                        <?php $number = 0; ?>
                                        @foreach ($products as $indexKey => $product) 
                                            @if($product->store_id == $store->id && $product->stock > 0)
                                                  <?php $number++ ?> 
                                            @endif
                                        @endforeach
                                        <span @if($number == 0) style="color:red" @endif>{{ $number }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('dashboard.store.transfersStore', [$store->store_respon,$store->id]) }}" class="btn btn-warning btn-sm btn-cs"><i class="fa fa-rocket"></i>تغير المسئول</a>
                                        @if (auth()->user()->hasPermission('delete-users'))
                                            <form action="{{ route('dashboard.stores.destroy', $store->id) }}" method="post">
                                                {{ csrf_field() }}
                                                {{ method_field('delete') }}
                                                 <input type="hidden" name="id" value="{{$store->id}}">
                                                <button type="submit" class="btn btn-danger delete btn-sm btn-cs"><i class="fa fa-trash"></i> @lang('site.delete')</button>
                                            </form><!-- end of form -->
                                        @else
                                            <button class="btn btn-danger btn-sm disabled"><i class="fa fa-trash"></i> @lang('site.delete')</button>
                                        @endif
                                    </td>
                                </tr>
                            
                            @endforeach
                            </tbody>

                        </table><!-- end of table -->
                        
                        {{ $stores->appends(request()->query())->links() }}
                        
                    @else
                        
                        <h2>@lang('site.no_data_found')</h2>
                        
                    @endif

                </div><!-- end of box body -->


            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@endsection
