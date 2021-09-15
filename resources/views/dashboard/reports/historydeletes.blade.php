@extends('layouts.dashboard.app')

@section('content')

<div class="content-wrapper">

        <section class="content-header">

            <h1>عماليات الحذف</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">عماليات الحذف</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <form action="{{ route('dashboard.reports.historydeletes') }}" method="get">

                        <div class="row">

                            <div class="col-md-2">
                                <input type="text" name="search" class="form-control" placeholder="بحث" value="{{ request()->search }}">
                            </div>
                            
                            <div class="col-md-2">
                                <select name="date" class="form-control">
                                    <option value="">جميع التواريخ</option>
                                    @foreach ($HistorydeletesDate as $HistorydeleteDate)
                                        <option value="{{ \Carbon\Carbon::parse($HistorydeleteDate[0]->date)->format('Y-m-d') }}" {{ request()->date == \Carbon\Carbon::parse($HistorydeleteDate[0]->date)->format('Y-m-d') ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($HistorydeleteDate[0]->date)->format('Y-m-d') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <select name="user_id" class="form-control">
                                    <option value="">كل المستخدمين</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ request()->user_id == $user->id ? 'selected' : '' }}>{{ $user->email }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <select name="type" class="form-control">
                                    <option value="">جميع الأماكن</option>
                                    <option value="1" {{ request()->type == 1 ? 'selected' : '' }}>الطلبات</option>
                                      <option value="11" {{ request()->type == 11 ? 'selected' : '' }}>الطلبات للتعديل</option>
                                    <option value="2" {{ request()->type == 2 ? 'selected' : '' }}>فواتير الشراء</option>
                                    <option value="3" {{ request()->type == 3 ? 'selected' : '' }}>العملاء</option>
                                    <option value="4" {{ request()->type == 4 ? 'selected' : '' }}>الأقسام</option>
                                    <option value="5" {{ request()->type == 5 ? 'selected' : '' }}>المنتجات</option>
                                    <option value="6" {{ request()->type == 6 ? 'selected' : '' }}>المرتجعات</option>
                                    <option value="7" {{ request()->type == 7 ? 'selected' : '' }}>المخازن</option>
                                    <option value="8" {{ request()->type == 8 ? 'selected' : '' }}>المناديب</option>
                                    <option value="9" {{ request()->type == 9 ? 'selected' : '' }}>طرق الشحن</option>
                                    <option value="10" {{ request()->type == 10 ? 'selected' : '' }}>المشرفين</option>
                                </select>
                            </div>
                    
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                <button type="button" class="btn btn-primary print-btn"><i class="fa fa-print"></i></button>
                            </div>
                            
                        </div>
                    </form><!-- end of form -->

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
                </style>
                <div class="box-body">

                    @if ($historydeletes->count() > 0)

                        <table class="table table-hover">

                            <thead>
                            <tr>
                                <th>#</th>
                                <th>مكان الحذف</th>
                                <th>بواسطة</th>
                                <th>تاريخ الحذف</th>
                                <th>التفاصيل</th>
                                <th></th>
                            </tr>
                            </thead>
                            
                            <tbody>
                            @foreach ($historydeletes as $index=>$historydelete)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($historydelete->type == 1)
                                          <span>الطلبات</span>
                                        @endif
                                        @if($historydelete->type == 11)
                                          <span>الطلبات</span>
                                        @endif
                                        @if($historydelete->type == 2)
                                          <span>فواتير الشراء</span>
                                        @endif
                                        @if($historydelete->type == 3)
                                          <span>العملاء</span>
                                        @endif
                                        @if($historydelete->type == 4)
                                          <span>الأقسام</span>
                                        @endif
                                        @if($historydelete->type == 5)
                                          <span>المنتجات</span>
                                        @endif
                                        @if($historydelete->type == 6)
                                          <span>المرتجعات</span>
                                        @endif
                                        @if($historydelete->type == 7)
                                          <span>المخازن</span>
                                        @endif
                                        @if($historydelete->type == 8)
                                          <span>المناديب</span>
                                        @endif
                                        @if($historydelete->type == 9)
                                          <span>طرق الشحن</span>
                                        @endif
                                        @if($historydelete->type == 10)
                                          <span>المشرفين</span>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach ($users as $user) 
                                            @if($user->id == $historydelete->user_id)
                                                 <a href="{{ route('dashboard.reports.historyUser', $user->id) }}" target="_blank">{{strstr($user->email, '@', true)}}</a>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>{{ $historydelete->created_at->toFormattedDateString() }}</td>
                                    <td>
                                        @if($historydelete->type == 1)
                                          رقم الأوردر :- {{$historydelete->type_id}}
                                          اسم العميل :- {{$historydelete->info_one}}
                                          @if($historydelete->info_two == 1) طريقة الدفع نقديا @endif
                                          @if($historydelete->info_two == 2) طريقة الدفع أجل @endif
                                          @if($historydelete->info_two == 3) طريقة الدفع مدفوعة جزائيا @endif
                                          الأجمالى :- {{$historydelete->info_three}}
                                        @endif
                                        @if($historydelete->type == 11)
                                          رقم الأوردر :- {{$historydelete->type_id}}
                                          اسم العميل :- {{$historydelete->info_one}}
                                          @if($historydelete->info_two == 1) طريقة الدفع نقديا @endif
                                          @if($historydelete->info_two == 2) طريقة الدفع أجل @endif
                                          @if($historydelete->info_two == 3) طريقة الدفع مدفوعة جزائيا @endif
                                          الأجمالى :- {{$historydelete->info_three}}
                                          <span style="color: red;">تعديل الطلب</span>
                                        @endif
                                        @if($historydelete->type == 2)
                                           رقم فاتورة الشراء :- {{$historydelete->info_one}}
                                           أسم المورد :- {{$historydelete->info_two}}
                                           الاجمالى :- {{$historydelete->info_three}}
                                        @endif
                                        @if($historydelete->type == 3)
                                          أسم العميل :- {{$historydelete->info_one}}
                                          رقم التليفون :- {{$historydelete->info_two}}
                                        @endif
                                        @if($historydelete->type == 4)
                                         أسم القسم :- {{$historydelete->info_one}}
                                        @endif
                                        @if($historydelete->type == 5)
                                         أسم المنتج :- {{$historydelete->info_one}}
                                         كود المنتج :- {{$historydelete->info_two}}
                                        @endif
                                        @if($historydelete->type == 6)
                                          @if($historydelete->info_one == 'order') من عميل @endif 
                                          @if($historydelete->info_one == 'bill') ألى المورد @endif
                                          رقم الفاتورة :- {{$historydelete->info_two}}
                                          المنتجات :- {{$historydelete->info_three}}
                                        @endif
                                        @if($historydelete->type == 7)
                                         أسم المخزن :- {{$historydelete->info_one}}
                                         المسؤال عن المخزن :- 
                                         @foreach ($users as $user) 
                                            @if($user->id == $historydelete->info_two)
                                                 <a href="{{ route('dashboard.reports.historyUser', $user->id) }}" target="_blank">{{strstr($user->email, '@', true)}}</a>
                                            @endif
                                         @endforeach
                                         عن طريق 
                                          @foreach ($users as $user) 
                                            @if($user->id == $historydelete->info_three)
                                                 <a href="{{ route('dashboard.reports.historyUser', $user->id) }}" target="_blank">{{strstr($user->email, '@', true)}}</a>
                                            @endif
                                          @endforeach
                                        @endif
                                        @if($historydelete->type == 8)
                                         أسم المندوب :- {{$historydelete->info_one}}
                                         رقم التليفون :- {{$historydelete->info_two}}
                                         عن طريق :- 
                                         @foreach ($users as $user) 
                                            @if($user->id == $historydelete->info_three)
                                                 <a href="{{ route('dashboard.reports.historyUser', $user->id) }}" target="_blank">{{strstr($user->email, '@', true)}}</a>
                                            @endif
                                          @endforeach
                                        @endif
                                        @if($historydelete->type == 9)
                                         أسم طريقة الشحن :- {{$historydelete->info_one}}
                                         رقم التليفون :- {{$historydelete->info_two}}
                                         عن طريق :- 
                                         @foreach ($users as $user) 
                                            @if($user->id == $historydelete->info_three)
                                                 <a href="{{ route('dashboard.reports.historyUser', $user->id) }}" target="_blank">{{strstr($user->email, '@', true)}}</a>
                                            @endif
                                         @endforeach
                                        @endif
                                        @if($historydelete->type == 10)
                                          البريد الألكترونى :- {{$historydelete->info_one}}
                                          @if($historydelete->info_two == 0) مسؤال عن جميع المخازن @endif
                                          @foreach ($stores as $store) 
                                            @if($store->id == $historydelete->info_two)
                                                <a href="{{ route('dashboard.reports.store') }}?search=&category_id=&store_id={{$store->id}}&stock=1" target="_blank">{{$store->store_name }}</a>
                                            @endif
                                          @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @if(in_array(Auth::id(),array(1,2)) && $historydelete->status == 0)
                                          <a href="{{ route('dashboard.reports.historydeletesStatus', $historydelete->id) }}" class="btn btn-danger btn-sm btn-cs"><i class="fa fa-check"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table><!-- end of table -->
                        
                    @else
                        
                        <h3>@lang('site.no_data_found')</h3>
                        
                    @endif

                </div><!-- end of box body -->
            </div>
            
            </div><!-- end of box -->
            

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@endsection