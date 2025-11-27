<x-mail::message>
# Action required: clone {{ $courseLabel }}

{{ $requesterName }} would like to create a fresh copy of **{{ $courseLabel }}** for the program **{{ $programName }}**. Approving keeps your original course untouched and sets up the clone automatically.

<x-mail::button :url="$approveUrl">
Approve clone
</x-mail::button>

If you do not want to share this course:

<x-mail::button :url="$denyUrl" color="error">
Deny request
</x-mail::button>

@if ($cloneRequest->expires_at)
This request will expire on {{ $cloneRequest->expires_at->toDayDateTimeString() }}.
@endif

Thanks for helping keep program data up to date!<br>
{{ config('app.name') }}
</x-mail::message>
