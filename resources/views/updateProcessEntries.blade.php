@extends('layouts.app')

@section('title', 'Update Process Entries')

@section('styles')
    <link href="/css/dropzone.css" rel='stylesheet' type='text/css'>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{trans('messages.updateProcessEntriesPanelLabel')}}</h3>
                    </div>
                    @if(session()->has('fileUploadMessage'))
                        <div class="alert alert-success">{{trans('messages.createProcessEntriesFileUpload')}}<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                    @endif
                    <div class="panel-body">
                        @if(isset($listProcessEntry))
                            <form action="/customers/list/process/entries/{{ $listProcessEntry->id }}/update" method="post">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="recipient-name" class="control-label">Title:</label>
                                    <input type="text" class="form-control" id="title" name="title" value="{{ $listProcessEntry->title }}">
                                </div>
                                <div class="form-group">
                                    <label for="message-text" class="control-label">Description:</label>
                                    <textarea class="form-control" id="description" name="description">{{ $listProcessEntry->description }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Confirmation: </label>
                                    <label class="radio-inline"><input type="radio" name="confirmation" value="yes" @if($listProcessEntry->confirmation=='yes') checked @endif>Yes</label>
                                    <label class="radio-inline"><input type="radio" name="confirmation" value="no"  @if($listProcessEntry->confirmation=='no') checked @endif>No</label>
                                </div>
                                <div class="form-group">
                                    <label>Comments open: </label>
                                    <label class="radio-inline"><input type="radio" name="comments" value="yes" @if($listProcessEntry->comments_open=='yes') checked @endif>Yes</label>
                                    <label class="radio-inline"><input type="radio" name="comments" value="no"  @if($listProcessEntry->comments_open=='no') checked @endif>No</label>
                                </div>

                                <div class="form-group">
                                    <label for="message-text" class="control-label loadProcessEntryFile">Files:</label>
                                    @if($listProcessFileNames)
                                        @foreach($listProcessFileNames as $listProcessFileName)
                                            <div>
                                                <a class="btn btn-sm btn-default download" href="/customers/list/process/entries/{{ $listProcessEntry->id }}/file/download/{{$listProcessFileName->id}}" data-file="{{$listProcessFileName->id}}" data-toggle="tooltip" data-placement="left" title="Download">{{$listProcessFileName->title}}</a>

                                                <a class="btn btn-info btn-sm hrefButtonID" data-toggle="modal" data-fileid="{{$listProcessFileName->id}}" data-target="#fileDetailModal">Edit Details</a>

                                                <a href="/customers/list/process/entries/{{ $listProcessEntry->id }}/file/delete/{{$listProcessFileName->id}}" class="removeProcessEntryFile" style="color: #333; cursor: pointer;" data-toggle="tooltip" data-placement="right" title="Delete this file"><i class="fa fa-trash"></i></a>

                                            </div>
                                            <br>
                                        @endforeach
                                    @endif

                                </div>
                                <!-- Dropzone Modal Button -->
                                <div class="form-group">
                                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#dropzoneModal">{{trans('messages.updateProcessEntriesUploadMoreButton')}}</button>
                                </div>
                                <button type="submit" id="submit-all" class="btn btn-primary">{{trans('messages.updateProcessEntriesButtonLabel')}}</button>
                                <button type="button" class="btn btn-danger pull-right del-entry" id="delEntry_{{ $listProcessEntry->id }}" >{{trans('messages.deleteProcessEntriesButtonLabel')}}</button>

                            </form>

                            <!-- Dropzone Modal Begin-->
                            <div class="modal fade" id="dropzoneModal" tabindex="-1" role="dialog" aria-labelledby="dropzoneModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title">Upload Files</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-success createProcessEntriesFileUpload" style="display: none">{{trans('messages.createProcessEntriesFileUpload')}}<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="/customers/list/process/entries/{{ $listProcessEntry->id }}/update/file" class="dropzone" method="post" id="dropzone" enctype="multipart/form-data">
                                                {!! csrf_field() !!}
                                                <div class="dz-message">{{trans('messages.updateProcessEntriesDropFiles')}}</div>
                                            </form>
                                            <br>
                                            <button type="submit" class="btn btn-primary" id="finishUpload">{{trans('messages.updateProcessEntriesFinishUploadButton')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Dropzone Modal End-->

                            <!-- Edit file details Modal Begin-->
                            <div class="modal fade" id="fileDetailModal" tabindex="-1" role="dialog" aria-labelledby="fileEditModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title">File Details</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-success fileEditedMessage" style="display: none">{{trans('messages.fileDetailsEditMessages')}}<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="" method="post" id="">
                                                {!! csrf_field() !!}
                                                <div class="form-group">
                                                    <label for="recipient-name" class="control-label">Title</label>
                                                    <input type="text" class="form-control" id="titleFileDetails" name="titleFileDetails" value="">
                                                    <input type="hidden" value="" name="hiddenFileId" id="hiddenFileId" class="hiddenFileId">
                                                </div>
                                                <div class="form-group">
                                                    <label for="message-text" class="control-label">Description</label>
                                                    <textarea class="form-control" id="descriptionFileDetails" name="descriptionFileDetails"></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary" id="saveFileDetails">Save Details</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Edit file details Modal End-->

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/js/dropzone.js"></script>
    <script>
        $(function(){
            $('[data-toggle="tooltip"]').tooltip();

            $('div.alert').delay(4000).slideUp(300);

            /* File details begin*/
            /* Getting file details */
            $(".hrefButtonID").on("click", function(){
               var fileid = $(this).data("fileid");
                $.post("/customers/list/process/entries/file/details/get", {fileid: fileid}, function(response){
                    $("#titleFileDetails").attr({"value" : response.getProcessFileNames.title});
                    $("#descriptionFileDetails").val(response.getProcessFileNames.description);
                    $(".hiddenFileId").attr({"value" : response.getProcessFileNames.id});
                });
            });

            /* Update file details */
            $("#saveFileDetails").on("click", function(e){
                e.preventDefault();
                var titleFileDetails       = $("#titleFileDetails").val();
                var descriptionFileDetails = $("#descriptionFileDetails").val();
                var processFileID          = $(".hiddenFileId").val();
                $.post("/customers/list/process/entries/{{$listProcessEntry->id}}/file/details/"+processFileID, {titleFileDetails: titleFileDetails, descriptionFileDetails: descriptionFileDetails}, function(response){
                    $(".fileEditedMessage").show();
                    location.reload(true);
                });
            });
            /* Update file details end*/
        });

        /* Dropzone begins */
        Dropzone.options.dropzone = {
            autoDiscover: false,
            autoProcessQueue: false,
            parallelUploads: 20,
            uploadMultiple: true,
            maxFilesize: 10,
            maxFiles: 20,
            acceptedFiles: ".gif,.png,.jpeg,.jpg,.docx,.doc,.pdf,.psd,.zip",
            init: function() {
                Dropzone = this;

                $("#finishUpload").click(function(){
                    Dropzone.processQueue();
                });

                this.on("success", function(file, responseText) {
                    Dropzone.removeFile(file);
                    location.reload();
                    if(responseText.success == true){
                        $(".createProcessEntriesFileUpload").show();
                        $("#dropzone").hide();
                        $("#finishUpload").hide();
                    };
                });
            }
        };
        /* Dropzone ends*/

        //Delete Entry - Ajax
        $('.del-entry').click( function () {
            var btnID    = $(this).attr('id');
            var entry_id = btnID.split('_')[1];
            if (confirm("Do you really want to delete this Entry?")) {
                $.post("/process/entry/delete/" + entry_id, function (data) {
                    if ($('#alert_div').length) {
                        $('#alert_div').remove();
                    }
                    if (data.mes == "done") {
                        htmlstr = '<div class="alert alert-success fade in" id="alert_div" role="alert">' +
                                '<span id="alert_msg">Deleted successfully, reloading&hellip;</span>' +
                                '</div>';
                        $(htmlstr).insertAfter('#' + btnID);
                        $('div.alert').delay(3000).slideUp("slow", function () {
                            location.href = '/customers/list/process/{{ $listProcessEntry->process_id }}/entries';
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