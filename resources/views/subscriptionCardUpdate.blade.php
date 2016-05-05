@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">{{trans('messages.updateCardPanel')}}</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/subscription/card/update') }}" id="payment-form">

                            {!! csrf_field() !!}

                            <div class="alert alert-danger payment-errors" role="alert" style="display: none"></div>

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
                                        {{trans('messages.updateCardButton')}}
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