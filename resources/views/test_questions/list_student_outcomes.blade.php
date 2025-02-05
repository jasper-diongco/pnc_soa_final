@extends('layout.app', ['active' => 'test_questions'])

@section('title', 'Test Questions')

@section('content')

<a href="{{ url('/test_questions/list_program') }}" class="text-success"><i class="fa fa-arrow-left"></i> Back</a>

<div class="d-flex justify-content-between mb-3">
  <div>
    <h1 class="page-header mt-3">Test Questions &mdash; List of Student Outcomes</h1>
  </div>
</div>

@if(count($program->studentOutcomes) > 0) 
  <div class="list-group">
    
    @foreach($program->studentOutcomes as $student_outcome)
      {{-- <a href="{{ url('/test_questions?program_id=' . $program->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
      <div>
        {{ $program->program_code . ' - ' . $program->description }} 
        <br>
        <small class="text-muted">{{ $program->college->name }}</small>
      </div>
        <i class="fa fa-chevron-right"></i>
      </a> --}}
      
      <div class="card mb-2">
        <!-- Card Header - Accordion -->
        <a href="#so_{{$student_outcome->id}}" class="d-block card-header py-3 so_collapse" data-toggle="collapse" role="button">
          <h6 class="m-0 font-weight-bold so_collapse">{{ $student_outcome->so_code }} &mdash; {{ $student_outcome->description }}</h6>
        </a>
        <div class="pl-2 p-1"><label>Test Questions:</label></div> 
        <!-- Card Content - Collapse -->
        <div class="collapse show" id="so_{{$student_outcome->id}}">
          <div class="">
            <div class="list-group">
              @if($student_outcome->curriculumMaps->count() > 0)
                @foreach ($student_outcome->curriculumMaps as $curriculum_map)
                  <a href="{{ url('/test_questions?student_outcome_id=' . $student_outcome->id . '&course_id=' . $curriculum_map->curriculumCourse->course->id . '&program_id=' . $student_outcome->program_id) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex align-items-center justify-content-between">
                      <div class="d-flex align-items-center">
                        <div class="avatar mr-2" style="background: {{ $curriculum_map->curriculumCourse->course->color  }};">
                          <i class="fa fa-book"></i>
                        </div>
                        <div class="mr-3">
                          {{ $curriculum_map->curriculumCourse->course->course_code }} - {{ $curriculum_map->curriculumCourse->course->description }}
                        </div>
                        <div>
                          <span class="badge {{ $curriculum_map->testQuestionCount() > 0? 'badge-success' : 'badge-dark'  }}">{{ $curriculum_map->testQuestionCount() }} questions</span>
                        </div>
                      </div>

                      <div><i class="fa fa-chevron-right"></i></div>
                    </div>
                    
                  </a>
                @endforeach
              @else
                <div class="list-group-item list-group-item-action">
                  No course mapped.
                </div>
              @endif

            </div>
          </div>
        </div>
      </div>
    @endforeach  
  </div>
@else
  <div class="text-center bg-white p-3">No Student Outcome Found in Database.</div>
@endif

{{-- <div class="my-3 d-flex justify-content-end">
  {{ $programs->appends(request()->input())->links() }}
</div> --}}

  
@endsection