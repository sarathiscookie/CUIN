@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">{{trans('messages.homePanelHeadingLabel')}}</div>

                @if(session()->has('updatedPlanNotice'))
                    <div class="alert alert-success" role="alert">{{trans('messages.updatedPlanNotice')}}</div>
                @endif

                @if(session()->has('cancelPlanNotice'))
                    <div class="alert alert-success" role="alert">{{trans('messages.cancelPlanNotice')}}</div>
                @endif

                @if(session()->has('cardUpdatedNotice'))
                    <div class="alert alert-success" role="alert">{{trans('messages.cardUpdatedNotice')}}</div>
                @endif


                <div class="panel-body">
                    {{trans('messages.homeWelcomeMessageLabel')}}
                    @if(Auth::user()->subscribed('main'))
                        <p>{{trans('messages.subscriptionSuccessLabel')}}</p>
                        @if(Auth::user()->subscription('main')->cancelled())
                            <p>{{trans('messages.subscriptionEndAlertLabel')}} <b>{{Auth::user()->subscription('main')->ends_at->format('D d M Y')}}</b></p>
                        @endif
                        <ul>
                            @if(!Auth::user()->subscription('main')->cancelled())
                                @if(Auth::user()->subscription('main')->onTrial())
                                    <p>Your subscription is on trial period! Trial period ends on <b>{{Auth::user()->subscription('main')->trial_ends_at->format('D d M Y')}}</b></p>
                                @endif
                                <li><a href="/subscription/plan/update/page" class="btn btn-primary btn-xs">{{trans('messages.subscriptionUpdateButton')}}</a></li>
                                <li><a href="/subscription/plan/cancel?_token={{ csrf_token() }}" class="btn btn-primary btn-xs">{{trans('messages.subscriptionCancelButton')}}</a></li>
                                <li><a href="/subscription/card/update/page" class="btn btn-primary btn-xs">{{trans('messages.updateCardButton')}}</a></li>
                            @else
                                <li><a href="/subscription/plan/resume?_token={{ csrf_token() }}" class="btn btn-primary btn-xs">{{trans('messages.subscriptionResumeButton')}}</a></li>
                            @endif
                        </ul>
                    @endif

                    <?php $invoices = Auth::user()->invoices(); ?>
                    <h1>{{trans_choice('messages.downloadInvoiceLabel', count($invoices))}}</h1>
                    <table>
                        @foreach ($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->date()->toFormattedDateString() }}</td>
                                <td>{{ $invoice->total() }}</td>
                                <td><a href="user/invoice/{{ $invoice->id }}"> Download</a></td>
                            </tr>
                        @endforeach
                    </table>
                </div>


            </div>
        </div>
    </div>
</div>
@endsection
