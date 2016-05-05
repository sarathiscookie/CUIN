@extends('layouts.app')

@section('title', 'Create Process Entries')

@section('styles')
    <link href="/css/dropzone.css" rel='stylesheet' type='text/css'>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{trans('messages.createProcessEntriesPanelLabel')}}</h3>
                    </div>
                    @if(session()->has('createProcessMessage'))
                        <div class="alert alert-success">
                            {{trans('messages.createProcessMessage')}}
                        </div>
                    @endif

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="recipient-name" class="control-label">Title:</label>
                            <input type="text" class="form-control" id="title" name="title">
                        </div>
                        <input type="hidden" name="processId" id="processId" value="{{ $id }}">
                        <div class="form-group">
                            <label for="message-text" class="control-label">Description:</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Confirmation: </label>
                            <label class="radio-inline"><input type="radio" name="confirmation" value="yes">Yes</label>
                            <label class="radio-inline"><input type="radio" name="confirmation" value="no" checked="checked">No</label>
                        </div>
                        <div class="form-group">
                            <label>Comments open: </label>
                            <label class="radio-inline"><input type="radio" name="comments" value="yes">Yes</label>
                            <label class="radio-inline"><input type="radio" name="comments" value="no" checked="checked">No</label>
                        </div>
                        <div class="form-group">
                            <label for="statusId">Status:</label>
                            <select class="form-control" name="statusId" id="statusId">
                                @if(isset($statusIds))
                                    <option value="">No status changes</option>
                                    @foreach($statusIds as $statusId)
                                        <option value="{{$statusId->id}}">{{$statusId->title}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <button type="submit" id="submit-all" data-loading-text="Loading..." class="btn btn-primary" autocomplete="off">{{trans('messages.createProcessEntriesButtonLabel')}}</button>
                        <br/>

                        <div class="alert alert-success createProcessEntriesMessage" style="display: none"> {{trans('messages.createProcessEntriesMessage')}} <button type="submit" id="cancel" class="btn btn-primary">{{trans('messages.createProcessEntriesCancelUpload')}}</button></div>
                        <br>
                        <div class="form-group" style="display: none" id="dropzoneDiv">
                            <form action="/customers/list/process/{{ $id }}/entries/file" class="dropzone" method="post" id="dropzone" enctype="multipart/form-data">
                                {!! csrf_field() !!}
                                <div class="dz-message">{{trans('messages.createProcessEntriesDropFiles')}}</div>
                                <input type="hidden" value="" name="entryID" id="entryID">
                            </form>
                            <br>
                            <button type="submit" class="btn btn-primary" id="finishUpload">{{trans('messages.createProcessEntriesFinishUpload')}}</button>
                        </div>
                        <div class="form-group" id="filenameFormGroup" style="display: none">
                            <label for="file" class="control-label" id="filename">Files:</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/js/dropzone.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(function(){
            $("#submit-all").click(function(e){
                e.preventDefault();
                var $btn         = $(this).button('loading')
                var title        = $("#title").val();
                var processId    = $("#processId").val();
                var description  = $("#description").val();
                var confirmation = $("[name='confirmation']:checked").val();
                var comments     = $("[name='comments']:checked").val();
                var status       = $("#statusId").val();
                $.post('/customers/list/process/{{ $id }}/entries/save', {title: title, processId: processId, description: description, confirmation: confirmation, comments: comments, status: status}, function(response)
                {
                    if(response.success == true){
                        $btn.button('reset');
                        $(".createProcessEntriesMessage").show();
                        $("input[name='entryID']").attr("value", response.entry);
                        $("#submit-all").hide();
                        $("#dropzoneDiv").show();
                        $("#cancel").click(function(){
                            location.href = '<?php echo url("/");?>/customers/list/process/<?php echo $id;?>/entries';
                        });
                    }
                });
            });
        });

        /*Dropzone Begin*/
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
                    if(responseText.success == true){
                        $(".createProcessEntriesMessage").hide();
                        $("#dropzoneDiv").hide();
                        location.href = '<?php echo url("/");?>/customers/list/process/entries/'+ $("#entryID").val();
                    };
                });
            }
        };
        /* Dropzone ends*/

        $('div.alert').delay(3000).slideUp(300);
    </script>
@endsection