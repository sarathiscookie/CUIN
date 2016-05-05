@extends('layouts.app')

@section('title', 'Process Entry Comments')

@section('styles')

@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">{{trans('messages.listEntryCommentsPanelLabel')}}
                    <a href="{{ url('/customers/list/process/'.$entry->process_id.'/entries') }}" role="button" class="btn btn-info btn-sm pull-right"><span aria-hidden="true">&larr;</span> Entries</a>
                    <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <h4>{{ $entry->title }}</h4>
                        <p>{{ $entry->description }}</p>
                        <small>{{ date('d.m.Y H:i', strtotime($entry->created_at)) }}</small>
                        <h4>Comments</h4>
                        <div id="container" class="commentBox">
                            @foreach($comments as $comment)
                                <div class="well well-sm">
                                    {{ $comment->content }}
                                    <h6><small>{{ date('d.m.Y H:i', strtotime($comment->created_at)) }}</small></h6>
                                </div>
                            @endforeach

                            <div id="cmtTxtBox" class="form-group">
                                <textarea id="contents" class="form-control txt-comment" name="contents" placeholder="write in your comments"></textarea>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary btn-sm pull-right disabled commentBtn" id="cmtPostBtn_{{ $entry->id }}" title="Post comments" disabled="disabled">Post</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        //Action - post comments
        $('.commentBox').on('click','.commentBtn', function () {
            $('<img src="/assets/img/loading.gif" alt="loading" class="pull-right media-middle loadingIcn" width="24px">').insertAfter($(this));
            var entry_id= $(this).attr('id').split('_')[1];
            var contents= $.trim($('#contents').val());
            var parent = $('#container');
            $.post("/process/entry/comment/post", { entry_id:entry_id, contents : contents }, function (data) {
                if ($('#alert_div').length) {
                    $('#alert_div').remove();
                }
                $('.loadingIcn').remove();
                if (data.mes == "done") {
                    $(parent).prepend(data.text);
                    $('#contents').val('');
                    $('.commentBtn').addClass('disabled').attr('disabled',true).attr('title','Enter comments to post');
                }
                else {
                    htmlstr = '<div class="alert alert-danger fade in" id="alert_div" role="alert">' +
                            '<span id="alert_msg">' + data.mes + '</span>' +
                            '</div>';
                    $(htmlstr).insertBefore('#cmtTxtBox');
                    $("#alert_div").fadeTo(5000, 500).slideUp(500, function () {
                        $("#alert_div").alert('close');
                    });
                }
            }, "json");
        });

        //Manage comment post button status
        $('.commentBox').on('keyup','.txt-comment', function () {
            if($.trim($(this).val()) !='') {
                $('.commentBtn').removeClass('disabled').attr('disabled',false).attr('title','Post comments');
            }
            else {
                $('.commentBtn').addClass('disabled').attr('disabled',true).attr('title','Enter comments to post');
            }
        });

        //csrf token for AJAX Request
        var token = "{{ csrf_token() }}";
        jQuery.ajaxSetup({
            headers: { 'X-CSRF-Token' : token }
        });
    </script>
@endsection