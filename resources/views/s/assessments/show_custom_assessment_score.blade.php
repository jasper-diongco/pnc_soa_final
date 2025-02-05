@extends('layout.app', ['active' => 'assessments'])

@section('title', 'Assessment Score')

@section('content')

<div id="app">
    <div class="card">
        <div class="card-body pt-4">
            <a href="{{ url('s/home') }}" class="text-success"><i class="fa fa-arrow-left"></i> Back</a>
            <div class="mt-3">
                <h1 class="page-header">Assessment Score</h1>
                <div class="w-md-31 mx-auto">
                    <img src="{{ asset('svg/undraw_segmentation_uioo1.svg') }}" alt="done checking" class="w-100">
                </div>
                <h2 class="mt-3 text-info text-center" style="font-size: 25px">{{ $student_outcome->description }}</h2>
                <div class="mt-4 d-flex flex-column align-items-center">
                    <div style="color:#a7a7a7;">YOUR SCORE</div>
                    <div style="font-size: 40px;font-weight: 600; {{ $custom_recorded_assessment->checkIfPassed() ? '' : 'border-color:#b5b5b5;' }}" class="{{ $custom_recorded_assessment->checkIfPassed() ? 'text-success' : 'text-danger' }}">{{ $custom_recorded_assessment->computeScore() }}%</div>
                    
                    <div class="alert mt-4 mb-4" style="background: #f1f1f1">

                     <h5><strong>Performance Criteria:</strong> {{ $custom_recorded_assessment->getPerformanceCriteriaText() }}<br>
                      <br>
                      Score: <strong>{{ $custom_recorded_assessment->computeScore() }}% ({{ $custom_recorded_assessment->getScoreLabel() }})</strong>  &mdash; {{ $custom_recorded_assessment->getScoreDescription() }}</h5>
                    </div>

                    @if ($custom_recorded_assessment->checkIfPassed())
                        <div class="text-center mb-3" style="font-size: 20px; color: #858886">
                            <strong>Congratulations, {{ $student->user->first_name . ' ' .$student->user->last_name  }} </strong>
                            <div>You passed the assessment for Student Outcome {{ $student_outcome->so_code }}</div> 
                        </div>
                    @else
                        <div class="text-center mb-3" style="font-size: 20px; color: #858886">
                            <strong>Sorry, {{ $student->user->first_name . ' ' .$student->user->last_name  }} </strong>
                            <div>You didn't passed the assessment for Student Outcome {{ $student_outcome->so_code }}</div> 
                        </div>
                    @endif


                </div>
                
{{--                 <h5 class="text-info mt-3">Details</h5>
                <ul class="list-group mt-3 list-student-outcomes">
                  <li class="list-group-item"><i class="fa fa-list"></i> {{ $assessment->assessmentDetails->count() }} total items </li>

                  <li class="list-group-item"><i class="fa fa-check-circle text-success"></i> {{ $assessment->getCorrectAnswers()->count() }} correct answers </li>
                  <li class="list-group-item"><i class="fa fa-times-circle text-danger"></i> {{ $assessment->getIncorrectAnswers()->count() }} incorrect answers</li>
                  <li class="list-group-item"><i class="fa fa-check-circle"></i> {{ $answer_sheet->passing_grade }}% passing grade </li>
                  <li class="list-group-item"><i class="fa fa-clock"></i> {{ $assessment->getDuration() }}</li>
                  <li class="list-group-item"><i class="fa fa-check-circle text-success"></i> {{ $assessment->getAnsweredTestQuestions()->count() }} test questions answered</li>
                  <li class="list-group-item"><i class="fa fa-times-circle"></i> {{ $assessment->getUnansweredTestQuestions()->count() }} unanswered test question</li>
                  
                </ul> --}}
                
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{url('s/home') }}" class="btn btn-info">OK, Back to home</a>
                </div>
            </div>
            
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>

    </script>
@endpush