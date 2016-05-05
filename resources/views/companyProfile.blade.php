@extends('layouts.app')

@section('title', 'Company User Profile')

@section('styles')
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">{{trans('messages.profilePanelHeadingLabel')}}</div>
                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ trans('messages.updateProfileSuccessfulMessage') }}
                            </div>
                        @endif
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/company/profile/update') }}">
                            {!! csrf_field() !!}

                            <div class="form-group {{ $errors->has('company') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label">Company</label>

                                <div class="col-md-6">
                                    <p class="form-control-static">{{ $company }}</p>
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label">Name</label>

                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="name" value="{{ $user->name or old('name') }}">

                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Lastname</label>

                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="lastname" value="{{ $user->lastname or old('lastname') }}">
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label">E-Mail Address</label>

                                <div class="col-md-6">
                                    <input type="email" class="form-control" name="email" value="{{ $user->email or old('email') }}">

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Language</label>

                                <div class="col-md-6">
                                    <select class="form-control" name="language">
                                        <option value="de" @if($userLanguage == 'de') selected @endif>Deutsch</option>
                                        <option value="en" @if($userLanguage == 'en') selected @endif>English</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Phone</label>

                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="phone" value="{{ $user->phone or old('phone') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Street</label>

                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="street" value="{{ $user->street or old('street') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Postal</label>

                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="postal" value="{{ $user->postal or old('postal') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">City</label>

                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="city" value="{{ $user->city or old('city') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">State</label>

                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="state" value="{{ $user->state or old('state') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Country</label>

                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="country" value="{{ $user->country or old('country') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-btn fa-user"></i>{{trans('messages.updateProfileButtonLabel')}}
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('div.alert').delay(3000).slideUp(300);
    </script>
@endsection