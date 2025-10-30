@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row mb-3">
            <div class="col">
                <a href="{{ url()->previous() ?: route('programWizard.step1', $program->program_id) }}" class="btn btn-link p-0">&larr; Back</a>
            </div>
        </div>

        <form method="POST" action="{{ route('programs.duplicate.store', $program->program_id) }}">
            @csrf
            <div class="card shadow-sm">
                <div class="card-header">
                    <h1 class="h5 mb-0">Duplicate {{ $program->program }}</h1>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="program">Program Name</label>
                        <input id="program" type="text" name="program" class="form-control @error('program') is-invalid @enderror" value="{{ old('program', $program->program . ' - Copy') }}" required autofocus>
                        @error('program')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <p class="mb-3">Choose how to bring each course into the new program. Cloning creates a brand-new copy of the course; linking keeps the original course attached; requesting defers cloning until owners approve.</p>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="thead-light">
                                <tr>
                                    <th>Course</th>
                                    <th style="width: 220px;">Action</th>
                                    <th style="width: 150px;">Your Access</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $permissionLabels = [
                                        1 => 'Owner',
                                        2 => 'Editor',
                                        3 => 'Viewer',
                                    ];
                                @endphp
                                @forelse ($courseData as $entry)
                                    @php
                                        $course = $entry['course'];
                                        $courseProgram = $entry['courseProgram'];
                                        $defaultAction = $entry['default_action'];
                                        $permission = $permissionLabels[$entry['permission'] ?? 0] ?? 'No access';
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $course->course_code }} {{ $course->course_num }}</strong><br>
                                            <span class="text-muted">{{ $course->course_title }}</span>
                                        </td>
                                        <td>
                                            <select name="course_actions[{{ $courseProgram->course_id }}][action]" class="form-control form-control-sm">
                                                <option value="clone" {{ $defaultAction === 'clone' ? 'selected' : '' }} {{ $entry['can_clone'] ? '' : 'disabled' }}>Clone new copy</option>
                                                <option value="link" {{ $defaultAction === 'link' ? 'selected' : '' }}>Link original</option>
                                                <option value="request" {{ $defaultAction === 'request' ? 'selected' : '' }}>Request clone later</option>
                                                <option value="skip" {{ $defaultAction === 'skip' ? 'selected' : '' }}>Skip for now</option>
                                            </select>
                                            @if (! $entry['can_clone'])
                                                <small class="text-muted d-block mt-1">You can’t clone this course directly.</small>
                                            @endif
                                        </td>
                                        <td>{{ $permission }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">This program has no courses yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ url()->previous() ?: route('programWizard.step1', $program->program_id) }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success">Duplicate Program</button>
                </div>
            </div>
        </form>
    </div>
@endsection
