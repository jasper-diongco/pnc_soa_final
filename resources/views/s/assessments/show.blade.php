@extends('layout.app', ['active' => 'home-student', 'container_fluid' => true, 'hide_navigation' => true, 'brand' => '<i class="fa fa-edit"></i> Student Outcome A &mdash; Assessment', 'assessment' => true, 'fixed_top' => true, 'shadow' => true, 'custom_layout' => true])


@section('title', 'Assessment')

@section('content')
    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow fixed-top">
      <a class="navbar-brand" href="#"><i class="fa fa-file-alt"></i> Student Outcome {{ $student_outcome->so_code }} - Assessment</a>
    </nav>
    {{-- <div id="app" v-cloak>
        <div class="row">
            <div class="col-md-8 p-3 pl-4" style="max-height: 100vh; overflow-y: auto;">
                
            </div>
            <div class="col-md-4 p-3 pr-4" style="max-height: 100vh; overflow-y: auto;">
                                
            </div>
        </div>
    </div> --}}
    <div id="app" v-cloak>
        <report-test-question-modal v-on:reported="addReportedTestQuestion" :test_question_id="report_test_question_id" :student_id="student_id"></report-test-question-modal>
        <div class="exam-sidenav">
            <div class="card shadow">
                <div class="card-body pt-3 text-center ">
                    <h5 class="mx-0"><i class="fa fa-clock text-info"></i> Time Remaining</h5>
                    <div class="time-container text-muted">
                        @{{ getTime() }}
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-body pt-3">
                    

                    <h5 class="mx-0"><i class="fa fa-question-circle text-info"></i> Questions</h5>
                    
                    <div v-for="template in templates" :key="template.course.course_code">
                        <label class="text-muted" style="font-weight: 400; font-size: 19px"><i class="fa fa-book"></i> @{{ template.course.course_code }}</label>
                        <div class="d-flex flex-wrap">
                            <div v-on:click="selectTestQuestion(test_question.id)" v-for="test_question in template.test_questions" class="question-avatar mr-2 mb-2" :class="{ 'question-avatar-answered': test_question.is_answered, 'active_test_question': test_question.id == selected_test_question.id, 'question-avatar-marked': test_question.is_marked  }">
                                @{{ test_question.counter }}              
                            </div>
                            
                        </div>
                    </div>
                    
                    <button :disabled="submitLoading" v-on:click="confirmSubmit" class="btn btn-info btn-block my-4">
                        Submit Answers 
                        <div v-if="submitLoading" class="spinner-border spinner-border-sm text-light" role="status">
                          <span class="sr-only">Loading...</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <div class="exam-main">
            <div class="card shadow">
                <div class="card-body pt-3">
                    <div class="text-muted mb-3 d-flex justify-content-start align-items-baseline"><i class="fa fa-book text-muted mr-2"></i> @{{ selected_course.course_code }} - @{{ selected_course.description }} 
                     <span v-if="selected_test_question.test_question.type_id == 1" class="badge ml-2 badge-info">Multiple Choice</span>
                     <span v-else-if="selected_test_question.test_question.type_id == 2" class="badge ml-2 badge-success">True or False</span>
                     <span v-else-if="selected_test_question.test_question.type_id == 3">
                        <span  class="badge ml-2 badge-warning">Multiple Select</span>
                        (Select all that apply)
                     </span>
                </div>
                    <div class="test-question-body text-dark d-flex">

                        <div class="mr-2">
                            <div class="test-num">@{{ selected_test_question.counter }}</div> 
                        </div>
                        <div class="mt-1" v-html="selected_test_question.body_html">
                        </div>
                        
                        
                    </div>
                    <hr>
                    <div class="choices">
                        <div  v-for="choice in selected_test_question.answer_sheet_test_question_choices" :key="choice.id" class="mb-3" v-on:click="selectChoice(selected_test_question.id, choice.id)">
                            {{-- :class="{'choice-correct': checkIfChoiceCorrect(choice)}" --}}
                            <div class="text-dark choice" style="height: 100%;" >
                                <div class="d-flex">
                                    <div v-if="selected_test_question.test_question.type_id == 3" class="mr-2 align-self-center">
                                        <i v-if="!choice.is_selected" class="far fa-check-square" style="font-size: 25px;"></i>
                                        <i v-else class="fa fa-check-square text-success" style="font-size: 25px;"></i>
                                    </div>
                                    <div class="mr-2">
                                        <div v-if="selected_test_question.test_question.type_id == 2" class="choice-num">
                                            <i class="fa fa-check-circle" :class="{ 'text-success': choice.is_selected }"></i>
                                        </div>
                                        <div v-else class="choice-num" :class="{ 'choice-selected': choice.is_selected }">

                                            @{{ choice.letter }}
                                        </div>

                                    </div>
                                    
                                    <div v-html="choice.body_html">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        
                        <div class="d-flex justify-content-between">
                            <div>
                                <button v-if="!checkIfReported(selected_test_question.test_question_id)" v-on:click="reportTestQuestion(selected_test_question.test_question_id)" class="btn btn-sm"><i class="fa fa-exclamation-circle"></i> Report problem</button>
                            </div>
                            <div>
                                <button v-if="selected_test_question.is_marked"  v-on:click="unmarkTestQuestion" class="btn btn-primary mr-2"><i class="fa fa-bookmark"></i> Unmark</button>
                                <button v-else v-on:click="markTestQuestion" class="btn btn-info mr-2"><i class="fa fa-bookmark"></i> Mark for review</button>
                                <button :disabled="selected_test_question.counter == 1" v-on:click="prevQuestion" class="btn btn-info mr-2" ><i class="fa fa-arrow-circle-left"></i> Back </button>

                                <button :disabled="selected_test_question.counter >= answer_sheet.answer_sheet_test_questions.length" v-on:click="nextQuestion" class="btn btn-info">Next <i class="fa fa-arrow-circle-right"></i></button>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    var vm = new Vue({
        el: '#app',
        data: {
            courses: @json($courses),
            counter: 1,
            templates: [],
            answers: [],
            marks: [],
            answer_sheet: @json($answer_sheet),
            selected_test_question: '',
            selected_course: '',
            remaining_time: 0,
            interval_id: '',
            submitLoading: false,
            report_test_question_id: '',
            student_id: '',
            reported_test_questions: []
        },
        methods: {
            reportTestQuestion(test_question_id) {
                $('#reportTestQuestionModal').modal('show');
                this.report_test_question_id = test_question_id;
                this.student_id = this.answer_sheet.student_id;
            },
            addReportedTestQuestion(test_question_id) {
                this.reported_test_questions.push(test_question_id);
            },
            checkIfReported(test_question_id) {
                for(var i = 0; i < this.reported_test_questions.length; i++) {
                    if(this.reported_test_questions[i] == test_question_id) {
                        return true;
                    }
                }

                return false;
            },
            getTestQuestionByCourse(course_id) {
                return this.answer_sheet.answer_sheet_test_questions.filter(answer_sheet_test_question => {
                    return answer_sheet_test_question.course_id == course_id;
                });
            },
            selectCourse(course_id) {
                for(var i = 0; i < this.courses.length; i++) {
                    if(this.courses[i].id == course_id) {
                        this.selected_course = this.courses[i];
                        break;
                    }               
                }
            },
            convertToChar(index) {
                return String.fromCharCode(index + 65);
            },
            createTemplate() {
                var counter = 1;
                for(var i = 0; i < this.courses.length; i++) {
                    var course_test_questions = this.getTestQuestionByCourse(this.courses[i].id);
                    this.templates.push({
                        course: this.courses[i],
                        test_questions: course_test_questions
                    });
                    for(var j = 0; j < course_test_questions.length; j++) {
                        //this.templates[i].test_questions[j].counter = counter;
                        //this.templates[i].test_questions[j].is_answered = false;
                        //this.templates[i].test_questions[j].is_marked = false;
                        Vue.set(this.templates[i].test_questions[j], 'is_marked', false);
                        Vue.set(this.templates[i].test_questions[j], 'counter', counter);

                        if(this.checkIfAnswered(this.templates[i].test_questions[j].id)) {
                            Vue.set(this.templates[i].test_questions[j], 'is_answered', true);
                        } else {
                            Vue.set(this.templates[i].test_questions[j], 'is_answered', false);
                        }
                        


                        var choices = this.templates[i].test_questions[j].answer_sheet_test_question_choices;
                        for(var k = 0; k < choices.length; k++) {
                            choices[k].letter = this.convertToChar(k);
                        }

                        counter++;
                    }                        
                }
            },
            selectTestQuestion(test_question_id) {
                for(var i = 0; i < this.answer_sheet.answer_sheet_test_questions.length; i++) {
                    if(this.answer_sheet.answer_sheet_test_questions[i].id == test_question_id) {
                        this.selected_test_question = this.answer_sheet.answer_sheet_test_questions[i];
                        this.selectCourse(this.selected_test_question.course_id);
                        break;
                    }               
                }
                MathLive.renderMathInDocument();
            },
            selectChoice(test_question_id, choice_id) {
                var test_question = this.selected_test_question;
                // console.log(test_question);



                for(var i = 0; i < test_question.answer_sheet_test_question_choices.length; i++) {  if(test_question.answer_sheet_test_question_choices[i].id == choice_id) {
                        if(test_question.test_question.type_id == 3) {
                            if(test_question.answer_sheet_test_question_choices[i].is_selected == 1) {
                                test_question.answer_sheet_test_question_choices[i].is_selected = 0;
                            } else {
                                test_question.answer_sheet_test_question_choices[i].is_selected = 1;
                            }
                        } else {
                            test_question.answer_sheet_test_question_choices[i].is_selected = 1;
                        }
                        
                    } else {
                        if(test_question.test_question.type_id != 3) {
                            test_question.answer_sheet_test_question_choices[i].is_selected = 0;
                        }
                        
                    }         
                }

                ApiClient.post('/s/assessments/select_choice/' + test_question.id, {
                    answer_sheet_test_question_choices: test_question.answer_sheet_test_question_choices
                })
                .then(response => {
                    //console.log(response);
                })

                if(test_question.test_question.type_id == 3) {
                    for(var i = 0; i < test_question.answer_sheet_test_question_choices.length; i++) { 
                        if(test_question.answer_sheet_test_question_choices[i].is_selected) {
                            test_question.is_answered = true;
                            break;
                        } else {
                            test_question.is_answered = false;
                        }
                    }
                } else {
                    test_question.is_answered = true;
                }

                

            },
            nextQuestion() {
                var counter = this.selected_test_question.counter;
                for(var i = 0; i < this.templates.length; i++) {  
                    for(var j = 0; j < this.templates[i].test_questions.length; j++) {
                        if(this.templates[i].test_questions[j].counter == counter + 1) {
                            this.selected_test_question = this.templates[i].test_questions[j];
                            this.selectCourse(this.selected_test_question.course_id);
                        }
                    }       
                }


            },
            prevQuestion() {
                var counter = this.selected_test_question.counter;
                for(var i = 0; i < this.templates.length; i++) {  
                    for(var j = 0; j < this.templates[i].test_questions.length; j++) {
                        if(this.templates[i].test_questions[j].counter == counter - 1) {
                            this.selected_test_question = this.templates[i].test_questions[j];
                            this.selectCourse(this.selected_test_question.course_id);
                        }
                    }       
                }
            },
            markTestQuestion() {
                this.selected_test_question.is_marked = true;
            },
            unmarkTestQuestion() {
                this.selected_test_question.is_marked = false;
            },
            tickTime() {
                this.interval_id = setInterval(() => {
                    this.remaining_time -= 1;
                    if(this.remaining_time <= 0) {
                        //this.submitAnswers(true);
                        swal.fire({
                            type: 'success',
                            title: 'Time is over!',
                            text: 'Your answer will be submitted automatically.',
                            confirmButtonColor: '#11c26d'
                          }).
                          then(isConfirmed => {
                            this.submitAnswers(true);
                          })
                          .catch(error => {
                            alert("An Error Has Occured. Please try again.");
                            console.log(error);
                          });

                          window.clearInterval(this.interval_id);
                    }
                }, 1000);
            },
            startTime() {
                var time = moment(this.answer_sheet.created_at);
                var now = moment(Date.now());
                var used_time = now.diff(time,'seconds');
                if(used_time <= 10) {
                    used_time = 0;
                }
                if((used_time / 60) >  this.answer_sheet.time_limit) {
                    //alert("no remaining time");
                    this.submitAnswers(true);
                } else {
                    this.remaining_time = this.answer_sheet.time_limit * 60 - used_time;
                    this.tickTime();
                }
                
            },
            getTime() {
                var date = new Date(null);
                date.setSeconds(this.remaining_time); // specify value for SECONDS here
                var timeString = date.toISOString().substr(11, 8);
                return timeString;
            },
            submitAnswers(time_over) {
                var isValid = this.validateIfAllIsAnswered();

                if(time_over == true) {
                    isValid = true;
                }

                if(isValid) {
                    this.submitLoading = true;
                    ApiClient.post('/s/assessments/' + this.answer_sheet.id, {
                        answer_sheet_test_questions: this.answer_sheet.answer_sheet_test_questions
                    })
                    .then(response => {
                        swal.fire({
                            type: 'success',
                            title: 'Good Job!',
                            text: 'Your answers has been submitted.',
                            confirmButtonColor: '#11c26d'
                        })
                        .then(response => {
                            window.location.replace(myRootURL + '/s/assessments/show_score?student_id=' + this.answer_sheet.student_id + '&student_outcome_id=' + this.answer_sheet.student_outcome_id);
                        });
                    })
                    .catch(err => {
                        alert("An Error has occured. Please try submitting again");
                        this.submitLoading = false;
                    });
                } else {
                    swal.fire({
                        type: 'error',
                        title: 'Not Valid',
                        text: 'Please answer all the test questions, before submitting the form',
                        confirmButtonColor: '#11c26d'
                      });
                }
            },
            confirmSubmit() {
                swal.fire({
                    type: 'question',
                    title: 'Please confirm',
                    text: 'do you want to submit your answers?',
                    showCancelButton: true,
                    width: '400px',
                    confirmButtonColor: '#11c26d'
                  }).
                  then(isConfirmed => {
                    if(isConfirmed.value) {
                        this.submitAnswers();
                    }
                  })
                  .catch(error => {
                    alert("An Error Has Occured. Please try again.");
                    console.log(error);
                  });
            },
            validateIfAllIsAnswered() {
                var isValid = true;

                for(var i = 0; i < this.answer_sheet.answer_sheet_test_questions.length; i++) { 
                    if(!(this.answer_sheet.answer_sheet_test_questions[i].is_answered)) {
                        isValid = false;
                        break;
                    }    
                }

                return isValid;
            },
            checkIfAnswered(test_question_id) {
                var test_question;
                var is_answered = false;
                for(var i = 0; i < this.answer_sheet.answer_sheet_test_questions.length; i++) {
                    if(this.answer_sheet.answer_sheet_test_questions[i].id == test_question_id) {
                        this.selected_test_question = this.answer_sheet.answer_sheet_test_questions[i];
                        test_question = this.selected_test_question;
                        break;
                    }               
                }


                for(var i = 0; i < test_question.answer_sheet_test_question_choices.length; i++) {
                    if(test_question.answer_sheet_test_question_choices[i].is_selected) {
                        is_answered = true;
                        break;
                    }
                }

                return is_answered;   
            },
            checkIfChoiceCorrect(choice) {
                return choice.is_correct == 1;
            }
        },
        created() {
            this.createTemplate();
            this.selected_test_question = this.templates[0].test_questions[0];
            this.selected_course = this.courses[0];
            this.startTime();
            setInterval(() => {
                MathLive.renderMathInDocument();
                Prism.highlightAll();
            }, 100);
        }
    });
</script>

@endpush