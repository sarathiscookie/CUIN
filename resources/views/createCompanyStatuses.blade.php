@extends('layouts.app')

@section('title', 'Create Company Statuses')

@section('styles')
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{{trans('messages.createCompanyStatusesPanelLabel')}}</h3>
                </div>
                <div class="panel-body">
                    <form action="{{ url('/company/statuses/save') }}" method="post">
                        {{ csrf_field() }}
                        <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                            <label for="salutation">Title</label>
                            <input type="text" class="form-control" name="title" id="title">
                            @if ($errors->has('title'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label  for="description">Description</label>
                            <textarea class="form-control" rows="3" name="description" id="description"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="salutation">Light</label>
                            <select class="form-control" name="light" id="light">
                                <option value="1">Red</option>
                                <option value="2">Yellow</option>
                                <option value="3">Green</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" >{{trans('messages.createCompanyStatusesButtonLabel')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection