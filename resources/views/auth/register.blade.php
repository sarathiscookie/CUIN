@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{trans('messages.registerPanelHeadingLabel')}}</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}" id="payment-form">
                        {!! csrf_field() !!}


                        <div class="alert alert-danger payment-errors" role="alert" style="display: none"></div>

                        <div class="form-group {{ $errors->has('company') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Company</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="company" value="{{ old('company') }}">

                                @if ($errors->has('company'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('company') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Name</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" data-stripe="name">

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
                                <input type="text" class="form-control" name="lastname" value="{{ old('lastname') }}">
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Confirm Password</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password_confirmation">

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Language</label>

                            <div class="col-md-6">
                                <select class="form-control" name="language">
                                    <option value="de">Deutsch</option>
                                    <option value="en">English</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Phone</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Street</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="street" value="{{ old('street') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Postal</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="postal" value="{{ old('postal') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">City</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="city" value="{{ old('city') }}" data-stripe="address_city">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">State</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="state" value="{{ old('state') }}" data-stripe="address_state">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Country</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="country" value="{{ old('country') }}" data-stripe="country">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Chosen Plan</label>

                            <div class="col-md-6">
                                <select class="form-control" name="plan">
                                    <option value="small">Small (€2/day)</option>
                                    <option value="large">Large (€20/month)</option>
                                    <option value="medium">Medium (€15/month) Trial for 1 day</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row form-group">
                            <label class="col-md-4 control-label">
                                <span>Card Number</span>
                            </label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" size="20" data-stripe="number">
                            </div>
                        </div>

                        <div class="form-row form-group">
                            <label class="col-md-4 control-label">
                                <span>Expiration (MM/YY)</span>
                            </label>

                            <div class="col-md-6">
                                <input type="text" size="2" data-stripe="exp_month">
                                <span> / </span>
                                <input type="text" size="2" data-stripe="exp_year">
                            </div>
                        </div>

                        <div class="form-row form-group">
                            <label class="col-md-4 control-label">
                                <span>CVC</span>
                            </label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" size="4" data-stripe="cvc">
                            </div>

                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary submit">
                                    <i class="fa fa-btn fa-user"></i>{{trans('messages.registerSubmitButtonLabel')}}
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
    <script src="/js/stripe.js"></script>
@endsection

