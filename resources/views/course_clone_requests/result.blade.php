@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h1 class="h4 mb-3">{{ $title }}</h1>
                        <p class="mb-0">{{ $message }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

