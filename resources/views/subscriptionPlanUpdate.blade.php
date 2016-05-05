@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">{{trans('messages.updateSubscriptionPanel')}}</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/subscription/plan/update/save') }}">
                            {!! csrf_field() !!}
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

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary submit">
                                        {{trans('messages.updateSubscriptionButton')}}
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