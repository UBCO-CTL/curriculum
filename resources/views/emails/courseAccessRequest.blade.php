@component('mail::message')

# Course access requested

{{ $requester->name }} ({{ $requester->email }}) has requested {{ strtoupper($accessType) }} access to **{{ $course->course_code }} {{ $course->course_num }} - {{ $course->course_title }}**.

@if($accessType === 'edit')
> Granting edit access allows the user to duplicate the course.
@endif

@isset($requestMessage)
---

**Message from requester:**

{{ $requestMessage }}

---
@endisset

You can review and update course collaborators from the course wizard.

@component('mail::button', ['url' => route('courseWizard.step1', ['course' => $course->course_id])])
Manage Course Access
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent


