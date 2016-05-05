@extends('layouts.frontend')

@section('title', 'Customer Page')

@section('styles')
    <link rel="stylesheet" href="/assets/css/reset.css"> <!-- CSS reset -->
    <link rel="stylesheet" href="/assets/css/style.css"> <!-- Resource style -->
    <link rel="stylesheet" href="/assets/css/blueimp-gallery.min.css">
    <link rel="stylesheet" href="/assets/css/bootstrap-image-gallery.min.css">
@endsection

@inject('cfpObj', 'App\Http\Controllers\CustomerFrontendPageController')

@section('content')
    @if($customerProcessEntries)
    <section id="cd-timeline" class="cd-container">
        @if($customerProcessEntries)
            @foreach($customerProcessEntries as $processEntry)
        <div class="cd-timeline-block">
            <div class="cd-timeline-img cd-picture">
                <img src="/assets/img/cd-icon-picture.svg" alt="Picture">
            </div>
            <div class="cd-timeline-content">
                <h2>{{ title_case($processEntry->title) }}</h2>
                <p>{{ $processEntry->description }}</p>
                {!! $cfpObj->getEntryImage($processEntry->id) !!}
                {!! $cfpObj->getEntryFiles($processEntry->id) !!}
                {!! $cfpObj->getEntryGallery($processEntry->id) !!}
                @if($processEntry->confirmation=='yes')
                    <div class="pull-right confirmBox">
                       {!!  $cfpObj->getEntryHistory($processEntry->id) !!}
                    </div>
                    <div class="clearfix"></div>
                @endif
                @if($processEntry->comments_open=='yes')
                    <div class="commentBox" style="margin: 20px 0;">
                        {!!  $cfpObj->getEntryComments($processEntry->id) !!}
                    </div>
                @endif
                <span class="cd-date">{{ date('d.m.Y H:i', strtotime($processEntry->created_at)) }}</span>
            </div>
        </div>
            @endforeach
        @endif
    </section>
    @else
       <section>
           <div class="well well-lg">
               @if($notice->notice_external)
                   {!! $notice->notice_external !!}
               @else
                   Hello {{ Auth::guard('customer')->user()->name }}, Welcome to your CUIN Timeline!
               @endif
           </div>
       </section>
    @endif
    <!-- The Bootstrap Image Gallery lightbox, should be a child element of the document body -->
    <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls"  data-use-bootstrap-modal="false">
        <!-- The container for the modal slides -->
        <div class="slides"></div>
        <!-- Controls for the borderless lightbox -->
        <h3 class="title"></h3>
        <a class="prev">‹</a>
        <a class="next">›</a>
        <a class="close">×</a>
        <a class="play-pause"></a>
        <ol class="indicator"></ol>
        <!-- The modal dialog, which will be used to wrap the lightbox content -->
        <div class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" aria-hidden="true">&times;</button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body next"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left prev">
                            <i class="glyphicon glyphicon-chevron-left"></i>
                            Previous
                        </button>
                        <button type="button" class="btn btn-primary next">
                            Next
                            <i class="glyphicon glyphicon-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div> {{--gallery end--}}
@endsection

@section('scripts')
    <script src="/assets/js/modernizr.js"></script> <!-- Modernizr -->
    <script src="/assets/js/main.js"></script> <!-- Resource jQuery -->
    <script src="/assets/js/jquery.blueimp-gallery.min.js"></script>
    <script src="/assets/js/bootstrap-image-gallery.min.js"></script>
    <script>
        // Action - confirm/reject
        $('.confirmBox').on('click','.updateHist', function () {
            var entry_id= $(this).attr('id');
            var parent = $(this).parent();
            $(parent).prepend('<img src="/assets/img/loading.gif" alt="loading" class="media-middle loadingIcn" width="24px">');
            $.post("/entry/history/update", { entry_id:entry_id }, function (data) {
                if (data.mes != "") {
                    $('.loadingIcn').remove();
                    $(data.mes).insertBefore(parent);
                    $(parent).remove();
                }
            }, "json");
        });

        //Action - post comments
        $('.commentBox').on('click','.commentBtn', function () {
            $('<img src="/assets/img/loading.gif" alt="loading" class="pull-right media-middle loadingIcn" width="24px">').insertAfter($(this));
            var entry_id= $(this).attr('id').split('_')[1];
            var contents= $.trim($('#contents_'+entry_id).val());
            var parent = $('#container_'+entry_id).parent();
            $.post("/entry/comment/post", { entry_id:entry_id, contents : contents }, function (data) {
                if ($('#alert_div').length) {
                    $('#alert_div').remove();
                }
                $('.loadingIcn').remove();
                if (data.mes == "done") {
                    $(parent).prepend(data.text);
                    $('#contents_'+entry_id).val('');
                    $('#cmtPost_'+entry_id).addClass('disabled').attr('disabled',true).attr('title','Enter comments to post');
                }
                else {
                    htmlstr = '<div class="alert alert-danger fade in" id="alert_div" role="alert">' +
                            '<span id="alert_msg">' + data.mes + '</span>' +
                            '</div>';
                    $(htmlstr).insertBefore('#container_'+entry_id);
                    $("#alert_div").fadeTo(5000, 500).slideUp(500, function () {
                        $("#alert_div").alert('close');
                    });
                }
            }, "json");
        });

        //Manage comment post button status
        $('.commentBox').on('keyup','.txt-comment', function () {
            var id = $(this).attr('id').split('_')[1];
            if($.trim($(this).val()) !='') {
                $('#cmtPost_'+id).removeClass('disabled').attr('disabled',false).attr('title','Post comments');
            }
            else {
                $('#cmtPost_'+id).addClass('disabled').attr('disabled',true).attr('title','Enter comments to post');
            }
        });

        //csrf token for AJAX Request
        var token = "{{ csrf_token() }}";
        jQuery.ajaxSetup({
            headers: { 'X-CSRF-Token' : token }
        });
    </script>
@endsection