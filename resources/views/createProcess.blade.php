@extends('layouts.app')

@section('title', 'Create Process')

@section('styles')
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{trans('messages.createProcessPanelLabel')}}</h3>
                    </div>
                    @if(session()->has('successmessage'))
                        <div class="alert alert-success">
                            {{trans('messages.createCustomerMessage')}}
                        </div>
                    @endif
                    <div class="panel-body">
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ url('/customer/process/save') }}" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="referenceId">Reference ID</label>
                                <input type="text" class="form-control" id="referenceId" placeholder="Reference ID" name="referenceId">
                            </div>
                            @if(isset($id))
                                <input type="hidden" name="customersId" value="{{ $id }}">
                            @endif
                            <div class="form-group">
                                <label for="statusId">Status</label>
                                <select class="form-control" name="statusId" id="statusId">
                                    @if(isset($statusIds))
                                        @foreach($statusIds as $statusId)
                                            <option value="{{$statusId->id}}">{{$statusId->title}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="customername">Title</label>
                                <input type="text" class="form-control" id="title" placeholder="Title" name="title">
                            </div>
                            <div class="form-group">
                                <label for="noticeexternal">Description</label>
                                <textarea class="form-control" rows="3" name="description" id="description"></textarea>
                            </div>
                            @if(isset($mode) && $mode=='new')
                                <input type="hidden" name="relist" value="1">
                                <button type="submit" class="btn btn-primary" >{{trans('messages.createProcessButtonCreateLabel')}}</button>
                            @else
                                <button type="submit" class="btn btn-primary" >{{trans('messages.createProcessButtonLabel')}}</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function(){
            $('div.alert').delay(3000).slideUp(300);
        });
    </script>
@endsection