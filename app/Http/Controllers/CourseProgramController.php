<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseProgram;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\NotifyCourseAddedToProgramMail;
use Illuminate\Support\Facades\Mail;

class CourseProgramController extends Controller
{
    //

    /**
     * Add courses to a program
     */
    public function addCoursesToProgram(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'program_id' => 'required',
        ]);

        $programId = $request->input('program_id');
        // if courseIds is null, use an empty array
        if (! $courseIds = $request->input('selectedCourses')) {
            $courseIds = [];
        }

        $numCoursesAddedSuccessfully = 0;

        // Get program details
        $program = Program::find($programId);

        // update or create a programCourse for each course
        foreach ($courseIds as $index => $courseId) {
            $isCourseRequired = $request->input('require'.$courseId);
            // if a courseProgram with course_id and program_id exists then update course_required field else create a new courseProgram record
            CourseProgram::updateOrCreate(
                ['course_id' => $courseId, 'program_id' => $programId],
                ['course_required' => ($isCourseRequired) ? 1 : 0]
            );

            // Get course details and course owner
            $course = Course::find($courseId);
            $courseOwners = $course->users()->where('permission', 1)->get();

            // Send email notification to course owners
            foreach ($courseOwners as $owner) {
                Mail::to($owner->email)->send(new NotifyCourseAddedToProgramMail(
                    $course->course_id,
                    $course->course_code,
                    $course->course_num ?? '',
                    $course->course_title,
                    $program->program,
                    $program->program_id,
                    (bool)$isCourseRequired
                ));
            }

            $numCoursesAddedSuccessfully++;
        }

        if ($numCoursesAddedSuccessfully == count($courseIds)) {
            // update courses 'updated_at' field
            $program->touch();

            // get users name for last_modified_user
            $user = User::find(Auth::id());
            $program->last_modified_user = $user->name;
            $program->save();

            $request->session()->flash('success', 'Successfully added '.strval(count($courseIds)).' course(s) to this program.');
        } else {
            $request->session()->flash('error', 'There was an error adding '.strval(count($courseIds) - $numCoursesAddedSuccessfully));
        }

        return redirect()->route('programWizard.step3', $request->input('program_id'));
    }

    public function editCourseRequired(Request $request): RedirectResponse
    {
        $courseId = $request->input('course_id');
        $programId = $request->input('program_id');
        $required = $request->input('required');
        $note = $request->input('note');

        $course = Course::where('course_id', $courseId)->first();

        if ($courseId != null && $programId != null && $required != null) {
            CourseProgram::updateOrCreate(
                ['course_id' => $courseId, 'program_id' => $programId],
                ['course_required' => $required]
            );

            CourseProgram::where(['course_id' => $courseId, 'program_id' => $programId])->update(['note' => $note]);
            // update courses 'updated_at' field
            $program = Program::find($request->input('program_id'));
            $program->touch();

            // get users name for last_modified_user
            $user = User::find(Auth::id());
            $program->last_modified_user = $user->name;
            $program->save();

            $request->session()->flash('success', 'Successfully updated: '.strval($course->course_title));
        } else {
            $request->session()->flash('error', 'There was an error updating the course');
        }

        return redirect()->route('programWizard.step3', $request->input('program_id'));
    }
}
