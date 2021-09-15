@extends('layouts.dashboard.app')

@section('content')

<div class="content-wrapper">

<section class="content-header">

    <h1>بياناتك</h1>

    <ol class="breadcrumb">
        <li class="active">@lang('site.edit')</li>
    </ol>
    
</section>

<section class="content">

<div class="box box-primary">

    <div class="box-header">
        <h3 class="box-title">@lang('site.edit')</h3>
    </div><!-- end of box header -->

        <div class="box-body">
         @include('partials._errors')

                <form action="{{route('dashboard.users.postProfile')}}" method="post" enctype="multipart/form-data">
                    {{ csrf_field()}}
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div> 
                    @endif
                    
                    <div class="form-group">
                        <label>@lang('site.first_name')</label>
                        <input type="text" required name="first_name" class="form-control" value="{{ $user->first_name }}">
                    </div>

                    <div class="form-group">
                        <label>@lang('site.last_name')</label>
                        <input type="text" required name="last_name" class="form-control" value="{{ $user->last_name }}">
                    </div>

                    <div class="form-group">
                        <label>@lang('site.email')</label>
                        <input type="email" name="email" required class="form-control" readonly value="{{ $user->email }}">
                    </div>

                    <div class="form-group">
                        <label>@lang('site.image')</label>
                        <input type="file" name="image" class="form-control image">
                    </div>

                    <div class="form-group">
                        <img src="{{ $user->image_path }}" style="width: 100px" class="img-thumbnail image-preview" alt="">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">كلمة السر القديمة</label>
                        <input type="text" class="form-control @error('password') is-invalid @enderror" name="password">
                        @error('date_of_birth')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                           @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password">كلمة السر الجديدة</label>
                        <input type="text" class="form-control" name="new_password">
                        @error('new_password')
                            <span class="invalid-feedback  @error('new_password') is-invalid @enderror" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                         @enderror
                    </div>

                    
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-edit"></i> تحديث البيانات</button>
                </div>

                </form><!-- end of form -->

        </div><!-- end of box body -->

    </div><!-- end of box -->

</section><!-- end of content -->

</div><!-- end of content wrapper -->

@endsection