<template>
    <div>
        <!-- create -->
        <image-modal
            :ref-id="ref_id"
            v-on:objects-added="refreshObjects"
        ></image-modal>

        <!-- update -->
        <image-modal
            :id="img_obj_id"
            :is-update="true"
            :ref-id="ref_id"
            v-on:objects-added="refreshObjects"
        ></image-modal>

        <!-- create -->
        <code-modal
            :ref-id="ref_id"
            v-on:objects-added="refreshObjects"
        ></code-modal>

        <!-- update -->
        <code-modal
            :id="code_obj_id"
            :is-update="true"
            :ref-id="ref_id"
            v-on:objects-added="refreshObjects"
        ></code-modal>

        <!-- create -->
        <math-modal
            :ref-id="ref_id"
            v-on:objects-added="refreshObjects"
        ></math-modal>

        <!-- update -->
        <math-modal
            :id="math_obj_id"
            :ref-id="ref_id"
            v-on:objects-added="refreshObjects"
            :is-update="true"
        ></math-modal>

        <!-- Button trigger modal -->
        <!--         <button
            type="button"
            class="btn btn-primary"
            data-toggle="modal"
            data-target="#reviseTestQuestionModal"
        >
            Launch demo modal
        </button> -->

        <!-- Modal -->
        <div
            class="modal fade"
            id="reviseTestQuestionModal"
            tabindex="-1"
            role="dialog"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        >
            <div
                class="modal-dialog modal-xl modal-dialog-scrollable"
                role="document"
            >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Modal title
                        </h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <!-- main -->
                                <div class="form-group">
                                    <label class="text-dark">Title</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        placeholder="Enter title..."
                                        v-model="title"
                                        data-vv-name="title"
                                        v-validate="{
                                            required: true,
                                            regex: /^[A-Za-z\s\-0-9_.,()\'?!]+$/
                                        }"
                                        :class="{
                                            'is-invalid': errors.has('title')
                                        }"
                                    />
                                    <div class="invalid-feedback">
                                        {{ errors.first("title") }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="text-dark"
                                        >Test question body</label
                                    >
                                    <div class="text-danger">
                                        {{ errors.first("body") }}
                                    </div>

                                    <textarea
                                        name="body"
                                        class="form-control"
                                        id="q-body"
                                        cols="30"
                                        rows="10"
                                        v-model="editorData"
                                        data-vv-name="body"
                                        v-validate="'required|max:1000'"
                                        :class="{
                                            'is-invalid': errors.has('body')
                                        }"
                                    ></textarea>
                                    <div class="invalid-feedback">
                                        {{ errors.first("body") }}
                                    </div>
                                </div>

                                <hr class="mt-5" />

                                <h5 class="text-dark">
                                    Choices
                                    <button
                                        v-on:click="addChoice"
                                        :disabled="this.choices.length >= 5"
                                        class="btn btn-sm btn-success"
                                    >
                                        add
                                        <i
                                            class="fa fa-plus"
                                            style="font-size: 10px"
                                        ></i>
                                    </button>
                                </h5>

                                <ul
                                    class="nav nav-tabs"
                                    id="myTab"
                                    role="tablist"
                                >
                                    <template
                                        v-for="(choice, index) in choices"
                                    >
                                        <li
                                            v-if="choice.is_active"
                                            :key="index"
                                            class="nav-item "
                                        >
                                            <a
                                                class="nav-link text-dark"
                                                :class="{
                                                    active: index == 0,
                                                    'border-bottom-danger': errors.has(
                                                        'choice ' + (index + 1)
                                                    )
                                                }"
                                                id="home-tab"
                                                data-toggle="tab"
                                                :href="'#c' + (index + 1)"
                                                >Choice
                                                {{ numToLetter(index + 1) }}

                                                <checked-icon
                                                    v-if="choice.is_correct"
                                                ></checked-icon>
                                                <i
                                                    v-else
                                                    class="fa fa-check"
                                                ></i>
                                                <button
                                                    v-if="choices.length > 3"
                                                    v-on:click="
                                                        removeChoice(index)
                                                    "
                                                    data-toggle="tooltip"
                                                    data-placement="top"
                                                    title="Remove"
                                                    class="btn btn-sm btn-light"
                                                >
                                                    <i
                                                        class="fa fa-minus text-danger"
                                                    ></i></button
                                            ></a>
                                        </li>
                                    </template>
                                </ul>
                                <div class="tab-content p-0" id="myTabContent">
                                    <template
                                        v-for="(choice, index) in choices"
                                    >
                                        <div
                                            v-if="choice.is_active"
                                            :key="index"
                                            class="tab-pane fade show p-0"
                                            :class="{ active: index == 0 }"
                                            :id="'c' + (index + 1)"
                                            role="tabpanel"
                                        >
                                            <textarea
                                                name="body"
                                                class="form-control"
                                                id="q-body"
                                                cols="30"
                                                rows="5"
                                                v-model="choice.editorData"
                                                :data-vv-name="
                                                    'choice ' + (index + 1)
                                                "
                                                v-validate="'required|max:500'"
                                                :class="{
                                                    'is-invalid': errors.has(
                                                        'choice ' + (index + 1)
                                                    )
                                                }"
                                            ></textarea>
                                            <div class="invalid-feedback">
                                                {{
                                                    errors.first(
                                                        "choice " + (index + 1)
                                                    )
                                                }}
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <div class="form-group mt-3">
                                    <label class="text-dark"
                                        >Select Correct Answer</label
                                    >
                                    <select
                                        class="form-control"
                                        v-model="correct_answer"
                                        v-on:change="selectCorrectAnswer"
                                        data-vv-name="correct answer"
                                        v-validate="'required'"
                                        :class="{
                                            'is-invalid': errors.has(
                                                'correct answer'
                                            )
                                        }"
                                    >
                                        <option value="" class="d-none"
                                            >Select Correct Answer</option
                                        >
                                        <option
                                            v-for="(choice, index) in choices"
                                            :value="index"
                                            >Choice
                                            {{ numToLetter(index + 1) }}</option
                                        >
                                    </select>
                                    <div class="invalid-feedback">
                                        {{ errors.first("correct answer") }}
                                    </div>
                                </div>
                                <div class="form-group mt-1">
                                    <label class="text-dark"
                                        >Select Level of Difficulty
                                        <i
                                            class="fa fa-circle"
                                            :class="{
                                                'text-success':
                                                    level_of_difficulty == 1,
                                                'text-warning':
                                                    level_of_difficulty == 2,
                                                'text-danger':
                                                    level_of_difficulty == 3
                                            }"
                                        ></i
                                    ></label>
                                    <select
                                        class="form-control"
                                        v-model="level_of_difficulty"
                                        data-vv-name="level"
                                        v-validate="'required'"
                                        :class="{
                                            'is-invalid': errors.has('level')
                                        }"
                                    >
                                        <option value="" class="d-none"
                                            >Select Option</option
                                        >
                                        <option value="1">Easy</option>
                                        <option value="2">Average</option>
                                        <option value="3">Difficult</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        {{ errors.first("level") }}
                                    </div>
                                </div>
                                <!-- ./main -->
                            </div>
                            <div class="col-md-4">
                                <div
                                    class="d-flex justify-content-between align-items-baseline"
                                >
                                    <h5
                                        class="text-dark d-flex align-items-center"
                                    >
                                        <div class="mr-2">
                                            Objects
                                        </div>

                                        <div class="dropdown dropright">
                                            <button
                                                class="btn btn-primary btn-sm dropdown-toggle"
                                                type="button"
                                                id="dropdownMenuButton"
                                                data-toggle="dropdown"
                                                aria-haspopup="true"
                                                aria-expanded="false"
                                            >
                                                <i
                                                    class="fa fa-plus-circle"
                                                    style="font-size: 10px"
                                                ></i>
                                                add
                                            </button>
                                            <div
                                                class="dropdown-menu"
                                                aria-labelledby="dropdownMenuButton"
                                            >
                                                <a
                                                    class="dropdown-item"
                                                    href="#"
                                                    data-toggle="modal"
                                                    data-target="#imageModal"
                                                    ><i class="fa fa-image"></i>
                                                    Image</a
                                                >
                                                <a
                                                    class="dropdown-item"
                                                    href="#"
                                                    data-toggle="modal"
                                                    data-target="#codeModal"
                                                    ><i class="fa fa-code"></i>
                                                    Code</a
                                                >
                                                <a
                                                    class="dropdown-item"
                                                    href="#"
                                                    data-toggle="modal"
                                                    data-target="#mathModal"
                                                    ><i
                                                        class="fa fa-superscript"
                                                    ></i>
                                                    Math</a
                                                >
                                            </div>
                                        </div>
                                    </h5>
                                </div>

                                <ul class="list-group">
                                    <template v-if="objectsLoading">
                                        <li class="list-group-item">
                                            <table-loading></table-loading>
                                        </li>
                                    </template>
                                    <template v-else-if="objectsEmpty">
                                        <li class="list-group-item">
                                            No object found.
                                        </li>
                                    </template>
                                    <template v-else>
                                        <!-- image -->
                                        <li
                                            v-for="image in image_objects"
                                            :key="image.path"
                                            class="list-group-item d-flex justify-content-between"
                                        >
                                            <div>
                                                <i
                                                    class="fa fa-image text-success"
                                                ></i>
                                                <span class="text-primary"
                                                    >[[#img{{
                                                        image.id
                                                    }}]]</span
                                                >
                                                &mdash;
                                                {{ image.description }}
                                            </div>

                                            <div>
                                                <button
                                                    v-on:click="
                                                        openImageModalUpdate(
                                                            image.id
                                                        )
                                                    "
                                                    class="btn btn-sm btn-primary"
                                                >
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </li>

                                        <!-- code -->
                                        <li
                                            v-for="code in code_objects"
                                            :key="code.id"
                                            class="list-group-item d-flex justify-content-between"
                                        >
                                            <div>
                                                <i
                                                    class="fa fa-code text-success"
                                                ></i>
                                                <span class="text-primary"
                                                    >[[#code{{
                                                        code.id
                                                    }}]]</span
                                                >
                                                &mdash;
                                                {{ code.description }}
                                            </div>

                                            <div>
                                                <button
                                                    v-on:click="
                                                        openCodeModalUpdate(
                                                            code.id
                                                        )
                                                    "
                                                    class="btn btn-sm btn-primary"
                                                >
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </li>

                                        <!-- math -->
                                        <li
                                            v-for="math in math_objects"
                                            :key="math.id + '' + math.formula"
                                            class="list-group-item d-flex justify-content-between"
                                        >
                                            <div>
                                                <i
                                                    class="fa fa-code text-success"
                                                ></i>
                                                <span class="text-primary"
                                                    >[[#math{{
                                                        math.id
                                                    }}]]</span
                                                >
                                                &mdash; $${{ math.formula }}$$
                                            </div>

                                            <div>
                                                <button
                                                    v-on:click="
                                                        openMathModalUpdate(
                                                            math.id
                                                        )
                                                    "
                                                    class="btn btn-sm btn-primary"
                                                >
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal"
                            :disabled="btnLoading"
                        >
                            Close
                        </button>
                        <button
                            :disabled="btnLoading"
                            type="button"
                            class="btn btn-success"
                            @click="saveTestQuestion"
                        >
                            Save changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<!--             @foreach ($test_question->choices as $index => $choice)
                {
                    id: {{ $choice->id }},
                    is_correct: {{ $choice->is_correct }},
                    editor: ClassicEditor,
                    editorData: `{!! $choice->body !!}`,
                    is_active: {{ $choice->is_active }},
                    editorConfig: {
                    }
                },
            @endforeach -->
<script>
export default {
    props: ["test_question_id", "item_analysis_detail_id"],
    data() {
        return {
            // test_question_id: 70,
            question: "",
            choices: [],
            choices_deactivated: [],
            editor: ClassicEditor,
            editorData: ``,
            editorConfig: {
                // The configuration of the editor.
            },
            title: "",
            correct_answer: "",
            level_of_difficulty: "",
            course_id: "",
            student_outcome_id: "",
            btnLoading: false,
            ref_id: "",
            math_obj_id: "",
            code_obj_id: "",
            img_obj_id: "",
            image_objects: [],
            code_objects: [],
            math_objects: [],
            objectsLoading: true
        };
    },
    watch: {
        test_question_id() {
            this.getTestQuestion();
        }
    },
    computed: {
        objectsEmpty() {
            return (
                this.image_objects.length == 0 &&
                this.code_objects.length == 0 &&
                this.math_objects.length == 0
            );
        },
        choicesCount() {
            let count = 0;

            for (let i = 0; i < this.choices.length; i++) {
                if (this.choices[i].is_active) {
                    count++;
                }
            }

            return count;
        }
    },
    methods: {
        getTestQuestion() {
            ApiClient.get(
                "/test_questions/get_test_question/" + this.test_question_id
            ).then(response => {
                this.choices = response.data.choices;
                this.title = response.data.title;
                this.editorData = response.data.body;
                this.level_of_difficulty = response.data.difficulty_level_id;
                this.course_id = response.data.course_id;
                this.ref_id = response.data.ref_id;
                this.student_outcome_id = response.data.student_outcome_id;
                this.getCorrectAnswer();

                for (var i = 0; i < this.choices.length; i++) {
                    this.choices[i].editorData = this.choices[i].body;
                }
                this.getImageObjects();
                this.getCodeObjects();
                this.getMathObjects();
            });
        },
        addChoice() {
            this.choices.push({
                id: null,
                is_correct: false,
                editor: ClassicEditor,
                editorData: "",
                is_active: true,
                editorConfig: {
                    // The configuration of the editor.
                }
            });
        },
        removeChoice(index) {
            swal.fire({
                title: "Do you want to remove this choice?",
                text: "Please confirm",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#1cc88a",
                cancelButtonColor: "#858796",
                confirmButtonText: "Yes",
                width: "400px"
            }).then(result => {
                if (result.value) {
                    this.errors.clear();
                    // for(let i = 0; i < this.choices.length; i++) {
                    //     this.choices[i].is_correct = false;
                    // }
                    if (this.choices[index].id == null) {
                        this.choices.splice(index, 1);
                    } else {
                        this.choices[index].is_active = false;
                        this.choices[index].is_correct = false;
                        let deac_choice = this.choices.splice(index, 1);

                        this.choices_deactivated.push(deac_choice[0]);
                    }

                    this.correct_answer = "";
                    this.getCorrectAnswer();
                }
            });
        },
        selectCorrectAnswer() {
            for (let i = 0; i < this.choices.length; i++) {
                this.choices[i].is_correct = false;
            }

            this.choices[this.correct_answer].is_correct = true;
        },
        getCorrectAnswer() {
            for (let i = 0; i < this.choices.length; i++) {
                if (this.choices[i].is_correct) {
                    this.correct_answer = i;
                    break;
                }
            }
        },
        saveTestQuestion() {
            this.btnLoading = true;
            this.$validator
                .validateAll()
                .then(isValid => {
                    if (isValid) {
                        ApiClient.put(
                            "/test_questions/" +
                                this.test_question_id +
                                "?is_revised=true&item_analysis_detail_id=" +
                                this.item_analysis_detail_id,
                            {
                                title: this.title,
                                question_body: this.editorData,
                                level_of_difficulty: this.level_of_difficulty,
                                course_id: this.course_id,
                                student_outcome_id: this.student_outcome_id,
                                choices: this.choices,
                                choices_deactivated: this.choices_deactivated,
                                ref_id: this.ref_id
                            }
                        )
                            .then(response => {
                                this.btnLoading = false;
                                // window.location.replace(
                                //     myRootURL +
                                //         "/test_questions/" +
                                //         this.test_question_id +
                                //         "?student_outcome_id=" +
                                //         this.student_outcome_id +
                                //         "&course_id=" +
                                //         this.course_id +
                                //         "&program_id=" +
                                //         this.program_id
                                // );
                                this.$emit("update-revise-status", response);
                                toast.fire({
                                    type: "success",
                                    title: "Test Question successfully revise"
                                });

                                $("#reviseTestQuestionModal").modal("hide");
                            })
                            .catch(err => {
                                console.log(err);
                                this.btnLoading = false;
                            });
                    } else {
                        toast.fire({
                            title: "Please enter valid data!",
                            type: "error"
                        });
                        this.btnLoading = false;
                    }
                })
                .catch(err => {
                    console.log(err);
                    this.btnLoading = false;
                });
        },
        refreshObjects() {
            this.getImageObjects();
            this.getCodeObjects();
            this.getMathObjects();
            this.math_obj_id = "";
            this.img_obj_id = "";
            this.code_obj_id = "";
            // $("#reviseTestQuestionModal").modal("hide");

            // setTimeout(() => {
            //     $("#reviseTestQuestionModal").modal("show");
            // }, 300);
        },
        openImageModalUpdate(image_id) {
            this.img_obj_id = image_id;
            $("#imageModalUpdate").modal("show");
        },
        openCodeModalUpdate(code_id) {
            this.code_obj_id = code_id;
            $("#codeModalUpdate").modal("show");
        },
        openMathModalUpdate(math_id) {
            this.math_obj_id = math_id;
            $("#mathModalUpdate").modal("show");
        },
        getImageObjects() {
            this.objectsLoading = true;
            ApiClient.get("/image_objects?ref_id=" + this.ref_id).then(
                response => {
                    this.objectsLoading = false;
                    this.image_objects = response.data;
                }
            );
        },
        getCodeObjects() {
            this.objectsLoading = true;
            ApiClient.get("/code_objects?ref_id=" + this.ref_id).then(
                response => {
                    this.objectsLoading = false;
                    this.code_objects = response.data;
                }
            );
        },
        getMathObjects() {
            this.objectsLoading = true;
            ApiClient.get("/math_objects?ref_id=" + this.ref_id).then(
                response => {
                    this.objectsLoading = false;
                    this.math_objects = response.data;
                    //MathLive.renderMathInDocument();
                    setInterval(() => {
                        this.renderMath();
                    }, 100);
                }
            );
        },
        renderMath() {
            MathLive.renderMathInDocument();
        },
        generateRef() {
            // Math.random should be unique because of its seeding algorithm.
            // Convert it to base 36 (numbers + letters), and grab the first 9 characters
            // after the decimal.
            return (
                "_" +
                Math.random()
                    .toString(36)
                    .substr(2, 9)
            );
        },
        numToLetter(num) {
            num = num + 64;

            return String.fromCharCode(num);
        }
    },
    created() {
        // this.getTestQuestion();

        setTimeout(() => {
            this.getImageObjects();
            this.getCodeObjects();
            this.getMathObjects();
        }, 1000);
    }
};
</script>
