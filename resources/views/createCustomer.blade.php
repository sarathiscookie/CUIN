@extends('layouts.app')

@section('title', 'Create Customer')

@section('styles')
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{trans('messages.createCustomerPanelLabel')}}</h3>
                    </div>
                    <div class="panel-body">
                        <form action="{{ url('/customer/save') }}" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="salutation">Salutation</label>
                                <select class="form-control" name="salutation" id="salutation">
                                    <option>Mr</option>
                                    <option>Ms</option>
                                    <option>Company</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="referenceId">Reference ID</label>
                                <input type="text" class="form-control" id="referenceId" placeholder="Reference ID" name="referenceId" value="{{ old('referenceId') }}">
                            </div>
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="customername">Name</label>
                                <input type="text" class="form-control" id="name" placeholder="Name" name="name" value="{{ old('name') }}">
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="customeremail">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="example@cuin.com" name="email" value="{{ old('email') }}">
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label  for="noticeinternal">Notice Internal</label>
                                <textarea class="form-control" rows="3" name="noticeinternal" id="noticeinternal">{{ old('noticeinternal') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="noticeexternal">Notice External</label>
                                <textarea class="form-control" rows="3" name="noticeexternal" id="noticeexternal">{{ old('noticeexternal') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary" >{{trans('messages.createCustomerButtonLabel')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection