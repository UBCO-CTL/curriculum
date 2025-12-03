@extends('layouts.app')

@section('content')
<div class="alert alert-warning">
        <!-- <i class="bi bi-info-circle-fill pr-2 fs-3"></i> -->
        <button type="button" class="close" data-dismiss="alert">×</button>
        <div>
            The UBC Curriculum MAP will have scheduled downtime on <b>Friday, December 5th from 9:00AM to 12:00PM </b> for regular maintenance. We apologize for this inconvenience. Please email ctl.helpdesk@ubc.ca with any questions or concerns.
        </div>
    </div>
<div class="container">
    <div class="row justify-content-center" style="padding-bottom:265px;">
        <div class="col-md-8">
            @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </div>
            @elseif (!session('resent'))
                <div class="alert alert-success" role="alert">
                    {{ __('An email has been sent to you for verification. Please be sure to check your spam/junk folder.') }}
                </div>
            @endif
            <div class="card">
                <div class="card-body">
                <div class="card-header">{{ __('Verify Your Email Address') }}</div>
                <div class="card-body">
                    {{ __('Before proceeding, please check your email for a verification link.') }}
                    {{ __('If you did not receive the email') }},
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
