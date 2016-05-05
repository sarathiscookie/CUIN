@extends('layouts.app')

@section('title', 'Process Entries')

@section('styles')
@endsection

@inject('entryObj', 'App\Http\Controllers\ProcessEntriesController')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">{{trans('messages.listProcessEntriesPanelLabel')}}
                        @if(isset($processId))
                            <a href="/customers/list/process/{{ $processId }}/entries/create" class="btn btn-primary btn-sm pull-right" role="button">{{trans('messages.listProcessEntriesPanelButtonLabel')}}</a>
                        @endif
                        <div class="clearfix"></div>
                    </div>
                    @if(session()->has('updateProcessEntriesMessage'))
                        <div class="alert alert-success">
                            {{trans('messages.updateProcessEntriesMessage')}}
                        </div>
                    @endif
                    @if(session()->has('createProcessEntriesMessage'))
                        <div class="alert alert-success">
                            {{trans('messages.createProcessEntriesMessage')}}
                        </div>
                    @endif
                    <div class="panel-body">
                        @if(session()->has('successmessage'))
                            <div class="alert alert-success">
                                {{session()->get('successmessage')}}
                            </div>
                        @endif
                        <table class="table table-hover table-bordered">
                            <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Date</th>
                                <th>Comments</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i=1;
                            ?>
                            @if(isset($listProcessEntries))
                                @forelse ($listProcessEntries as $listProcessEntry)
                                    <tr>
                                        <td>
                                            <a href="/customers/list/process/entries/{{$listProcessEntry->id}}">{{$listProcessEntry->title}}</a>
                                            {!! $entryObj->getFileCount($listProcessEntry->id) !!}
                                        </td>
                                        <td>{{$listProcessEntry->description}}</td>
                                        <td>{{$listProcessEntry->created_at->format('Y-m-d')}}</td>
                                        <td>{!! $entryObj->getCommentButton($listProcessEntry->id) !!}</td>
                                    </tr>
                                    <?php $i++; ?>
                                @empty
                                    <p>{{trans('messages.notListingProcessEntries')}}</p>
                                @endforelse
                            @endif
                            </tbody>
                        </table>

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