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
                        <h3 class="panel-title">{{trans('messages.updateCompanyStatusesPanelLabel')}}</h3>
                    </div>

                    <div class="panel-body">
                        @if($companyStatus)
                            <form action="/company/statuses/list/{{$id}}/update" method="post">
                                {{ csrf_field() }}
                                <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                                    <label for="salutation">Title</label>
                                    <input type="text" class="form-control" name="title" id="title" value="{{$companyStatus->title}}">
                                    @if ($errors->has('title'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label  for="description">Description</label>
                                    <textarea class="form-control" rows="3" name="description" id="description">{{$companyStatus->description}}</textarea>
                                </div>

                                <div class="form-group">
                                    <label for="salutation">Light</label>
                                    <select class="form-control" name="light" id="light">
                                        <option value="1" @if($companyStatus->light == 1) selected @endif>Red</option>
                                        <option value="2" @if($companyStatus->light == 2) selected @endif>Yellow</option>
                                        <option value="3" @if($companyStatus->light == 3) selected @endif>Green</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary" >{{trans('messages.updateCompanyStatusesButtonLabel')}}</button>
                                <button type="button" class="btn btn-danger del-status" id="delStatus_{{ $id }}">{{trans('messages.deleteCompanyStatusesButtonLabel')}}</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('.del-status').click( function () {
            var btnID    = $(this).attr('id');
            var statusid = btnID.split('_')[1];
            if (confirm("Do you really want to delete this Status?")) {
                $.post("/company/status/delete/" + statusid, function (data) {
                    if ($('#alert_div').length) {
                        $('#alert_div').remove();
                    }
                    if (data.mes == "done") {
                        htmlstr = '<div class="alert alert-success fade in" id="alert_div" role="alert">' +
                                '<span id="alert_msg">Deleted successfully, reloading&hellip;</span>' +
                                '</div>';
                        $(htmlstr).insertAfter('#' + btnID);
                        $('div.alert').delay(3000).slideUp("slow", function () {
                            location.href = '/company/profile/settings#companyStatus';
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