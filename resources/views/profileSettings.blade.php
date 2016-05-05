@extends('layouts.app')

@section('title', 'Company profile Settings')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#changePW" aria-controls="changePW" role="tab" data-toggle="tab">Change Password</a></li>
                        <li role="presentation"><a href="#companyStatus" aria-controls="companyStatus" role="tab" data-toggle="tab">Company Statuses</a></li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade in active" id="changePW">
                            <div class="panel panel-default">
                                <div class="panel-heading">{{trans('messages.changePasswordPanelHeadingLabel')}}</div>
                                <div class="panel-body">
                                    @if (session('status'))
                                        <div class="alert alert-success">
                                            {{ session('status') }}
                                        </div>
                                    @endif
                                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/company/profile/password/update') }}">
                                            {!! csrf_field() !!}
                                            <div class="form-group{{ $errors->has('old_password') ? ' has-error' : '' }}">
                                                <label class="col-md-4 control-label">Old Password</label>

                                                <div class="col-md-6">
                                                    <input type="password" class="form-control" name="old_password" id="old_password">

                                                    @if ($errors->has('old_password'))
                                                        <span class="help-block">
                                        <strong>{{ $errors->first('old_password') }}</strong>
                                    </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                                <label class="col-md-4 control-label">Password</label>

                                                <div class="col-md-6">
                                                    <input type="password" class="form-control" name="password">

                                                    @if ($errors->has('password'))
                                                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                                <label class="col-md-4 control-label">Confirm Password</label>
                                                <div class="col-md-6">
                                                    <input type="password" class="form-control" name="password_confirmation">

                                                    @if ($errors->has('password_confirmation'))
                                                        <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-md-6 col-md-offset-4">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fa fa-btn fa-wrench"></i>Update Password
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="companyStatus">
                            <div class="panel panel-default">
                                <div class="panel-heading">{{trans('messages.listCompanyStatusesPanelLabel')}}
                                    <a href="{{ url('/company/statuses/create') }}" class="btn btn-primary btn-sm pull-right">{{trans('messages.listCompanyStatusesButtonLabel')}}</a>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="panel-body">
                                    @if(session()->has('updatemessage'))
                                        <div class="alert alert-success">
                                            {{trans('messages.updateCompanyStatusesMessage')}}
                                        </div>
                                    @endif
                                    @if(session()->has('successmessage'))
                                        <div class="alert alert-success">
                                            {{trans('messages.createCompanyStatusesMessage')}}
                                        </div>
                                    @endif

                                    <script type="text/x-template" id="grid-template">
                                        <table class="table table-hover table-bordered">
                                            <thead>
                                            <tr>
                                                <th v-for="key in columns">@{{key | capitalize}}</th>
                                            </tr>
                                            </thead>
                                            <tbody v-sortable.tr="data">
                                            <tr v-for="entry in data | filterBy filterKey | orderBy sortKey sortOrders[sortKey]" track-by="$index">
                                                <td>
                                                    <a href="/company/statuses/list/@{{ entry.id }}" role="button">@{{entry.title}}</a>
                                                    <br>
                                                    <small>@{{entry.description}}</small>
                                                </td>
                                                <td v-if="entry.light == 1">
                                                    <svg width="50" height="50">
                                                        <circle cx="25" cy="25" r="20" fill="red" />
                                                    </svg>
                                                </td>
                                                <td v-if="entry.light == 2">
                                                    <svg width="50" height="50">
                                                        <circle cx="25" cy="25" r="20" fill="yellow" />
                                                    </svg>
                                                </td>
                                                <td v-if="entry.light == 3">
                                                    <svg width="50" height="50">
                                                        <circle cx="25" cy="25" r="20" fill="green" />
                                                    </svg>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </script>
                                    <div id="app">
                                        <demo-grid  :data="{{$listCompanyStatus}}"  :columns="gridColumns"></demo-grid>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="/js/vue.js"></script>
    <script src="/js/vue-resource.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.4.2/Sortable.min.js"></script>
    <script>
        //password field make empty
        $('#old_password').val('');

        //token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        Vue.directive('sortable', {
            twoWay: true,
            deep: true,
            bind: function () {
                var that = this;

                var options = {
                    draggable: Object.keys(this.modifiers)[0],
                    ghostClass: "sortable-ghost",  // Class name for the drop placeholder
                };

                this.sortable = Sortable.create(this.el, options);
                //console.log('sortable bound!')

                this.sortable.option("onUpdate", function (e) {
                    //console.log("update-1");
                    that.value.splice(e.newIndex, 0, that.value.splice(e.oldIndex, 1)[0]);

                    that.value.forEach(function (task, index) {
                        task.position = index + 1;
                        task.sort_id = index + 1;
                        $.post('/company/statuses/list/update/sort/'+task.id+'/'+task.position, function( data ) {
                        });
                    });
                });

                this.onUpdate = function(value) {
                    //console.log("update-3");
                    that.value = value;
                }
            },
            update: function (value) {
                //console.log("update-2");
                //console.log(value);
                this.onUpdate(value);
            }
        });

        Vue.component('demo-grid', {
            template: '#grid-template',
            props: {
                data: Array,
                columns: Array,
                filterKey: String
            },
            data: function () {
                var sortOrders = {}
                this.columns.forEach(function (key) {
                    sortOrders[key] = 1
                })
                return {
                    sortKey: '',
                    sortOrders: sortOrders
                }
            },
            methods: {
                sortBy: function (key) {
                    this.sortKey = key
                    this.sortOrders[key] = this.sortOrders[key] * -1
                }
            }
        });

        // bootstrap the demo
        var demo = new Vue({
            el: '#app',
            data: {
                searchQuery: '',
                gridColumns: ['title', 'light'],
                gridData: null
            },

            created: function() {
                this.fetchData()
            },

            methods: {
                fetchData: function () {
                    var self = this;
                    $.get('/company/statuses', function( data ) {
                        self.gridData = data;
                    });
                }
            }
        });

        //alert display toggle
        $('div.alert').delay(3000).slideUp(300);

        var hash = document.location.hash;
        var prefix = "tab_";
        if (hash) {
            $('.nav-tabs a[href='+hash+']').tab('show');
        }

        // Change hash for page-reload
        $('.nav-tabs a').on('shown', function (e) {
            window.location.hash = e.target.hash;
        });
    </script>
@endsection
