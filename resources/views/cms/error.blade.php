@extends('app')

@section('style')
<style>
    .error-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        margin-left: 250px;
        height: calc(100vh - 100px);
    }
    .locked-icon {
        font-size: 50px;
        color: gray;
    }
    .error-message {
        text-align: center; /* Center the text */
        color: gray;
    }
    .error-message2 {
        text-align: center; /* Center the text */
        color: gray;
    }
</style>
@endsection

@section('content')
    <div class="error-container">
        <div class="locked-icon">
            <i class="fa fa-lock"></i>
        </div>
        <div class="error-message">
            Your Workstation is not authorized to handle the {{ $message }}.
        </div>
        <div class="error-message">
            {{ $message2 ?? '' }}.
        </div>
    </div>
@endsection
