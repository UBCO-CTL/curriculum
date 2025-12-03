<?php

namespace App\Http\Middleware;

use App\Models\Course;
use App\Models\Program;
use App\Models\syllabus\Syllabus;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HasAccessMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $courseParam = $request->route()->parameter('course');
        $programParam = $request->route()->parameter('program');
        $syllabusParam = $request->route()->parameter('syllabusId');
        // get current user
        $user = Auth::user();

        if (!$user) {
            session()->flash('error', 'Authentication required');
            return redirect()->route('home');
        }
        $course = $this->resolveCourse($courseParam);
        $program = $this->resolveProgram($programParam);
        $syllabus = $this->resolveSyllabus($syllabusParam);

        if ($course instanceof Course) {
            // get all users for the course
            $courseUsers = $course->users;
            // check if the current user belongs to this course
            if (! in_array($user->id, $courseUsers->pluck('id')->toArray())) {
                // user does not belong to this course
                session()->flash('error', 'You do not have access to this course');

                return redirect()->route('home');
            } else {
                // get users permission level for this syllabus
                $userPermission = $courseUsers->where('id', Auth::id())->first()->pivot->permission;
                switch ($userPermission) {
                    case 1:
                        // Owner
                        break;
                    case 2:
                        // Editor
                        $request['isEditor'] = true;
                        break;
                    case 3:
                        // Viewer
                        $request['isViewer'] = true;
                        break;
                    default:
                        // default
                }
            }
        } elseif ($program instanceof Program) {
            // get all users for the program
            $programUsers = $program->users;
            // check if the current user belongs to this program
            if (! in_array($user->id, $programUsers->pluck('id')->toArray())) {
                // user does not belong to this program
                session()->flash('error', 'You do not have access to this program');

                return redirect()->route('home');
            } else {
                // get users permission level for this syllabus
                $userPermission = $programUsers->where('id', Auth::id())->first()->pivot->permission;
                switch ($userPermission) {
                    case 1:
                        // Owner
                        break;
                    case 2:
                        // Editor
                        $request['isEditor'] = true;
                        break;
                    case 3:
                        // Viewer
                        $request['isViewer'] = true;
                        break;
                    default:
                        // default
                }
            }

        } elseif ($syllabus instanceof Syllabus) {
            // get all users for the syllabus
            $syllabusUsers = $syllabus->users;
            // check if the current user belongs to this syllabus
            if (! in_array($user->id, $syllabusUsers->pluck('id')->toArray())) {
                // user does not belong to this syllabus
                session()->flash('error', 'You do not have access to this syllabus');

                return redirect()->route('home');
            }
        }

        return $next($request);
    }

    private function resolveCourse($courseParam): ?Course
    {
        if ($courseParam instanceof Course) {
            return $courseParam;
        }

        if ($courseParam) {
            return Course::find($courseParam);
        }

        return null;
    }

    private function resolveProgram($programParam): ?Program
    {
        if ($programParam instanceof Program) {
            return $programParam;
        }

        if ($programParam) {
            return Program::find($programParam);
        }

        return null;
    }

    private function resolveSyllabus($syllabusParam): ?Syllabus
    {
        if ($syllabusParam instanceof Syllabus) {
            return $syllabusParam;
        }

        if ($syllabusParam) {
            return Syllabus::find($syllabusParam);
        }

        return null;
    }
}
