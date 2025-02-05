@extends('layout.app', ['active' => 'students'])

@section('title', $student->user->getFullName())


@section('content')

    <a href="{{ url('/students') }}" class="text-success"><i class="fa fa-arrow-left"></i> Back</a>
    <h1 class="page-header mt-3">Student information</h1>
    <div id="app">
    <update-student-modal student-id="{{ $student->id }}" :refresh-update="true"></update-student-modal>
            <div class="card">
                <div class="card-body pt-4">
                    
                    <div class="d-flex justify-content-between align-items-baseline mb-4">
                      <div class="d-flex align-items-baseline">
                        {{-- <div>
                            <img src="{{ asset('img/user.svg') }}" alt="user-icon" style="width: 40px" class=" mr-3">
                        </div> --}}
                        <div>
                            <h4><i class="fa fa-user text-info"></i> {{ $student->user->getFullName() }}</h4>
                        </div>
                      </div>
                      <div>
                        <a href="{{ url('/students/' . $student->id . '/obe_curriculum') }}" class="btn btn-sm btn-info mr-2"><i class="fa fa-file-alt"></i> View OBE Curriculum</a>
                        <button data-toggle="modal" data-target="#updateStudentModal" class="btn btn-sm btn-secondary"><i class="fa fa-edit"></i> Update Information</button>
                      </div>
                    </div>
                        
                    <div class="accordion" id="accordionExample">
                      <div class="card">
                        <div class="card-header" id="headingOne">
                          <h2 class="mb-0">
                            <button class="btn btn-link text-success" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                              <b>Basic Information</b>
                            </button>
                          </h2>
                        </div>
                
                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                          <div class="card-body">

                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <label>Student ID: </label>
                                    <span>{{ $student->student_id }}</span>
                                </li>
                                <li class="list-group-item">
                                    <label>Last Name: </label>
                                    <span>{{ $student->user->last_name }}</span>
                                </li>
                                <li class="list-group-item">
                                    <label>First Name: </label>
                                    <span>{{ $student->user->first_name }}</span>
                                </li>
                                <li class="list-group-item">
                                    <label>Middle Name: </label>
                                    <span>{{ $student->user->middle_name }}</span>
                                </li>
                                <li class="list-group-item">
                                    <label>Sex: </label>
                                    <span>{{ $student->user->sex == 'M' ? 'Male' : 'Female' }}</span>
                                </li>
                                <li class="list-group-item">
                                    <label>Date of Birth: </label>
                                    <span>@{{ date_of_birth | date  }}</span>
                                </li>
                                
                            </ul>
                          </div>
                        </div>
                      </div>
                      <div class="card">
                        <div class="card-header d-flex justify-content-between" id="headingTwo">
                          <div>
                              <h2 class="mb-0">
                                <button class="text-success btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                  <b>Academic Information</b>
                                </button>
                              </h2>
                          </div>

                          
                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                          <div class="card-body">
                            <div class="mb-3 d-flex justify-content-end">
                              <a href="{{ url('/students/' . $student->id . '/edit_academic') }}" class="btn btn-success btn-sm">Update <i class="fa fa-edit"></i></a>
                            </div>
                            <ul class="list-group list-group-flush">
                                
                                <li class="list-group-item">
                                    <label>College: </label>
                                    <span>{{ $student->program->college->name }}</span>
                                </li>
                                <li class="list-group-item">
                                    <label>Program: </label>
                                    <span>{{ $student->program->description }}</span>
                                </li>
                                <li class="list-group-item">
                                    <label>Curriculum: </label>
                                    <span>{{ $student->curriculum->name }} &mdash; {{ $student->curriculum->year }} &dmash; revision no. {{ $student->curriculum->revision_no }}.0</span>
                                </li>
                                
                            </ul>
                            
                          </div>
                        </div>
                      </div>
                      <div class="card">
                        <div class="card-header" id="headingThree">
                          <h2 class="mb-0">
                            <button class="text-success btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                              <b>Account Information</b>
                            </button>
                          </h2>
                        </div>
                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                          <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <label>Email: </label>
                                    <span>{{ $student->user->email }}</span>
                                </li>
                                <li class="list-group-item">
                                    <label>User Type: </label>
                                    <span>{{ $student->user->userType->description }}</span>
                                </li>
                                <li class="list-group-item">
                                    <label>Date Registered: </label>
                                    <span>@{{ created_at | date }}</span>
                                </li>
                                
                            </ul>
                          </div>
                        </div>
                      </div>

                      <div class="card">
                        <div class="card-header" id="headingThree">
                          <h2 class="mb-0">
                            <button class="text-success btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseThree">
                              <b>Extra Information</b>
                            </button>
                          </h2>
                        </div>
                        <div id="collapseFour" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                          <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <label>Address: </label>
                                    <span>{{ $student->user->address ?? 'Not defined' }}</span>
                                </li>
                                <li class="list-group-item">
                                    <label>Contact Number: </label>
                                    <span>{{ $student->user->contact_no ?? 'Not defined' }}</span>
                                </li>
                                
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>
            </div>
        
    </div>
@endsection

@push('scripts')
    <script>
        new Vue({
            el: '#app',
            data: {
                date_of_birth: '{{ $student->user->date_of_birth }}',
                created_at: '{{ $student->user->created_at }}'
            },
            filters: {
                date(value) {
                    return moment(value).format("MMM D, YYYY");
                }
            }
        });
    </script>

    @if(Session::has('message'))
        <script>
          toast.fire({
            type: 'success',
            title: '{{ Session::get('message') }}'
          })
        </script>
      @endif
@endpush

{{-- @push('scripts')
    <script>
        new Vue({
            el: '#app',
            data: {
                form: new Form({
                    student_id: '',
                    last_name: '',
                    first_name: '',
                    middle_name: '',
                    sex: '',
                    date_of_birth: '',
                    college: '',
                    program: '',
                    curriculum: '',
                    email: '',
                    password: 'DefaultPass123'
                }),
                colleges: @json($colleges),
                programs: @json($programs),
                curriculums: @json($curriculums)
            },
            methods: {
                selectProgramsByCollege(college_id) {
                    return this.programs.filter(program => {
                        return program.college_id == college_id;
                    });
                },
                selectCurriculumsByProgram(program_id) {
                    return this.curriculums.filter(curriculum => {
                        return curriculum.program_id == program_id;
                    });
                },
                addStudent() {
                    this.form.post('../students')
                        .then(response => {
                            window.location.replace(myRootURL + '/students');
                        })
                        .catch(err => {
                            console.log(err);
                        });
                }
            }
        })
    </script>
@endpush --}}