@extends('layouts.app')

@section('title', 'Update Process')

@section('styles')
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{trans('messages.updateProcessPanelLabel')}}</h3>
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
                        <form action="{{ url('/process/update/'.$process->id.$redirect) }}" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="referenceId">Reference ID</label>
                                <input type="text" class="form-control" id="referenceId" placeholder="Reference ID" name="referenceId" value="{{ $process->reference_id }}">
                            </div>
                            <div class="form-group">
                                <label for="statusId">Status</label>
                                <select class="form-control" name="statusId" id="statusId">
                                    @if(isset($statusIds))
                                        @foreach($statusIds as $statusId)
                                            <option @if($process->status_id==$statusId->id) selected="selected" @endif value="{{$statusId->id}}">{{$statusId->title}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="customername">Title</label>
                                <input type="text" class="form-control" id="title" placeholder="Title" name="title" value="{{ $process->title }}">
                            </div>
                            <div class="form-group">
                                <label for="noticeexternal">Description</label>
                                <textarea class="form-control" rows="3" name="description" id="description">{{ $process->description }}</textarea>
                            </div>
                            <input type="hidden" name="update" value="1">

                            <button type="submit" class="btn btn-primary" >{{trans('messages.updateProcessButtonLabel')}}</button>
                            <a role="button" class="btn btn-default" href="{{ url()->previous()  }}" >{{trans('messages.cancelProcessButtonLabel')}}</a>
                            <button type="button" class="btn btn-danger pull-right del-process" id="delProcess_{{ $process->id }}" >{{trans('messages.deleteProcessButtonLabel')}}</button>
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

        //Delete process - Ajax
        $('.del-process').click( function () {
            var redirect = '{{ $redirect }}';
            var btnID    = $(this).attr('id');
            var process_id = btnID.split('_')[1];
            if (confirm("Do you really want to delete this Process?")) {
                $.post("/process/delete/" + process_id, function (data) {
                    if ($('#alert_div').length) {
                        $('#alert_div').remove();
                    }
                    if (data.mes == "done") {
                        htmlstr = '<div class="alert alert-success fade in" id="alert_div" role="alert">' +
                                '<span id="alert_msg">Deleted successfully, reloading&hellip;</span>' +
                                '</div>';
                        $(htmlstr).insertAfter('#' + btnID);
                        $('div.alert').delay(3000).slideUp("slow", function () {
                            if(redirect!='')
                                location.href = '/processes';
                            else
                                location.href = '/customers/list/process/{{$process->customer_id}}';
                        });
                    }
                    else {
                        htmlstr = '<div class="alert alert-danger fade in" id="alert_div" role="alert">' +
                                '<span id="alert_msg">' + data.mes + '</span>' +
                                '</div>';
                        $(htmlstr).insertAfter('#' + btnID);
                        $('div.alert').delay(5000).slideUp(300);
                    }
                }, "json");
            }
        });

        //csrf token for AJAX Request
        var token = "{{ csrf_token() }}";
        jQuery.ajaxSetup({
            headers: { 'X-CSRF-Token' : token }
        });
    </script>
@endsection