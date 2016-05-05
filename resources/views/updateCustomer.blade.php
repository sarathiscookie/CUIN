@extends('layouts.app')

@section('title', 'Update Customer')

@section('styles')
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{trans('messages.updateCustomerPanelLabel')}}</h3>
                    </div>
                    <div class="panel-body">
                        <form action="{{ url('/customer/update/'.$customer->id) }}" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="salutation">Salutation</label>
                                <select class="form-control" name="salutation" id="salutation">
                                    <option @if($customer->salutation=='Mr') selected="selected" @endif>Mr</option>
                                    <option @if($customer->salutation=='Ms') selected="selected" @endif>Ms</option>
                                    <option @if($customer->salutation=='Company') selected="selected" @endif>Company</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="referenceId">Reference ID</label>
                                <input type="text" class="form-control" id="referenceId" placeholder="Reference ID" name="referenceId" value="{{ $customer->reference_id }}">
                            </div>
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="customername">Name</label>
                                <input type="text" class="form-control" id="name" placeholder="Name" name="name" value="{{ $customer->name }}">
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="customeremail">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="example@cuin.com" name="email" value="{{ $customer->email }}">
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label  for="noticeinternal">Notice Internal</label>
                                <textarea class="form-control" rows="3" name="noticeinternal" id="noticeinternal">{{ $customer->notice_internal }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="noticeexternal">Notice External</label>
                                <textarea class="form-control" rows="3" name="noticeexternal" id="noticeexternal">{{ $customer->notice_external }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary" >{{trans('messages.updateCustomerButtonLabel')}}</button>
                            <a role="button" class="btn btn-default" href="{{ url()->previous()  }}" >{{trans('messages.cancelCustomerButtonLabel')}}</a>
                            <button type="button" class="btn btn-danger pull-right del-customer" id="delCustomer_{{ $customer->id }}" >{{trans('messages.deleteCustomerButtonLabel')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        //Delete customer - Ajax
        $('.del-customer').click( function () {
            var btnID    = $(this).attr('id');
            var customer_id = btnID.split('_')[1];
            if (confirm("Do you really want to delete this Customer?")) {
                $.post("/customer/delete/" + customer_id, function (data) {
                    if ($('#alert_div').length) {
                        $('#alert_div').remove();
                    }
                    if (data.mes == "done") {
                        htmlstr = '<div class="alert alert-success fade in" id="alert_div" role="alert">' +
                                '<span id="alert_msg">Deleted successfully, reloading&hellip;</span>' +
                                '</div>';
                        $(htmlstr).insertAfter('#' + btnID);
                        $('div.alert').delay(3000).slideUp("slow", function () {
                            location.href = '/customers';
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