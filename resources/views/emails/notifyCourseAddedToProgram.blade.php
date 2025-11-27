@component('mail::message')
# {{ $course_code }} {{ $course_num }} Added to {{ $program_title }}

Your course **{{ $course_code }} {{ $course_num }} - {{ $course_title }}** has been added to the program **{{ $program_title }}**.

The course has been set as {{ $is_required ? 'required' : 'not required' }} in this program.

You can now proceed with mapping your course learning outcomes to the program learning outcomes.

@component('mail::button', ['url' => route('courseWizard.step5', ['course' => $course_id])])
Map {{ $course_code }} {{ $course_num }}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent