<template>
  <div>
    <!-- Activator -->
    <button
      v-if="isUpdate"
      type="button"
      class="btn btn-secondary btn-sm"
      data-toggle="modal"
      data-target="#courseModal"
      @click="getRandColor"
    >
      Update Course <i class="fa fa-edit"></i>
    </button>
    <button
      v-else-if="addDirectly"
      type="button"
      class="btn btn-success-b"
      data-toggle="modal"
      data-target="#courseModal"
      @click="getRandColor"
    >
      Add new Course
    </button>
    <button
      v-else
      type="button"
      class="btn btn-success-b"
      data-toggle="modal"
      data-target="#courseModal"
      @click="getRandColor"
    >
      Add New Course</i>
    </button>
    <!-- End Activator -->

    <!-- Modal -->
    <div
      class="modal fade"
      id="courseModal"
      tabindex="-1"
      role="dialog"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <form
          @submit.prevent="saveCourse"
          @keydown="form.onKeydown($event)"
          autocomplete="off"
        >
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">
                {{ modalTitle }}
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
              <!-- Field for course code  -->
              <div class="form-group">
                <label
                  for="course_code"
                  >Course Code</label
                >


                  <input
                    id="course_code"
                    type="text"
                    class="form-control"
                    name="course_code"
                    v-model="form.course_code"
                    v-uppercase
                    :class="{ 'is-invalid': form.errors.has('course_code') }"
                  />
                  <has-error :form="form" field="course_code"></has-error>
              </div>
              <!-- /end Field for course code -->

              <!-- Field for description  -->
              <div class="form-group">
                <label
                  for="description"
                  >Description</label
                >

                <div>
                  <input
                    id="description"
                    type="text"
                    class="form-control"
                    name="description"
                    v-model="form.description"
                    v-uppercase
                    :class="{ 'is-invalid': form.errors.has('description') }"
                  />
                  <has-error :form="form" field="description"></has-error>
                </div>
              </div>
              <!-- /end Field for description -->

              <!-- Field for description  -->
              <div class="form-group">
                <label
                  for="college_id"
                  >Select College</label
                >

                <div>
                  <select
                    id="college_id"
                    class="form-control"
                    name="college_id"
                    v-model="form.college_id"
                    v-uppercase
                    :class="{ 'is-invalid': form.errors.has('college_id') }"
                    :disabled="this.collegeId != 'all'"
                  >
                    <option value="" style="display: none"
                      >Select College</option
                    >
                    <option
                      v-for="college in colleges"
                      :key="college.id"
                      :value="college.id"
                      >{{ college.name }}</option
                    >
                  </select>
                  <has-error :form="form" field="college_id"></has-error>
                </div>
              </div>
              <!-- /end Field for description -->


              <div class="row">
                <div class="col-6">
                  <!-- Field for lecture_unit  -->
                  <div class="form-group">
                    <label
                      for="lec_unit"
                      >Lec Unit</label
                    >

                    <div>
                      <input
                        id="lec_unit"
                        type="number"
                        class="form-control"
                        name="lec_unit"
                        v-model="form.lec_unit"
                        v-uppercase
                        :class="{ 'is-invalid': form.errors.has('lec_unit') }"
                      />
                      <has-error :form="form" field="lec_unit"></has-error>
                    </div>
                  </div>
                  <!-- /end Field for lecture_unit -->
                </div>
                <div class="col-6">
                  <!-- Field for lab_unit  -->
                  <div class="form-group">
                    <label
                      for="lab_unit"
                      >Lab Unit</label
                    >

                    <div>
                      <input
                        id="lab_unit"
                        type="number"
                        class="form-control"
                        name="lab_unit"
                        v-model="form.lab_unit"
                        v-uppercase
                        :class="{ 'is-invalid': form.errors.has('lab_unit') }"
                      />
                      <has-error :form="form" field="lab_unit"></has-error>
                    </div>
                  </div>
                  <!-- /end Field for lab_unit -->
                </div>
              </div>


              

              

              

              <!-- Field for privacy  -->
<!--               <div class="form-group">
                <label
                  for="privacy"
                  >Privacy</label
                >

                <div>
                  <select
                    id="privacy"
                    class="form-control"
                    name="privacy"
                    v-model="form.privacy"
                    v-uppercase
                    :class="{ 'is-invalid': form.errors.has('privacy') }"
                  >
                    <option value="" style="display: none"
                      >Select Privacy</option
                    >
                    <option value="1">Public</option>
                    <option value="0">Private</option>
                  </select>
                  <has-error :form="form" field="privacy"></has-error>
                </div>
              </div> -->
              <!-- /end Field for privacy -->
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                data-dismiss="modal"
                :disabled="form.busy"
              >
                Close
              </button>
              <button
                class="btn btn-success"
                :disabled="form.busy"
                type="submit"
              >
                {{ saveTitle }}
                <div
                  v-show="form.busy"
                  class="spinner-border spinner-border-sm text-light"
                  role="status"
                >
                  <span class="sr-only">Loading...</span>
                </div>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <!-- End Modal -->
  </div>
</template>

<script>
export default {
  props: ["colleges", "isUpdate", "courseProp", "collegeId", "addDirectly"],
  data() {
    return {
      form: new Form({
        course_code: "",
        description: "",
        lec_unit: "",
        lab_unit: "",
        privacy: 1,
        college_id: "",
        color: ""
      }),
      colors: [
        "#E53935",
        "#D81B60",
        "#8E24AA",
        "#5E35B1",
        "#3949AB",
        "#1E88E5",
        "#039BE5",
        "#00ACC1",
        "#00897B",
        "#43A047",
        "#7CB342",
        "#C0CA33",
        "#FDD835",
        "#F4511E",
        "#6D4C41"
      ],
      course: null
    };
  },
  computed: {
    saveTitle() {
      return this.isUpdate ? "Update from database" : "Add to database";
    },
    modalTitle() {
      return this.isUpdate ? "Update Course" : "Add new Course";
    }
  },
  methods: {
    closeModal() {
      $("#courseModal").modal("hide");
    },
    createCourse() {
      let link = "courses";
      if (this.addDirectly) {
        link = "../courses";
      }

      this.form
        .post(link)
        .then(({ data }) => {
          if (this.addDirectly) {
            this.$emit("open-curriculum-course", data);
            this.closeModal();
            this.form.course_code = "";
            this.form.description = "";
            this.form.lec_unit = "";
            this.form.lab_unit = "";
            this.form.privacy = "";
          } else {
            window.location.href = myRootURL + "/courses/" + data.id;
          }
        })
        .catch(err => {
          console.log(err);
          toast.fire({
            type: "error",
            title: "Please enter valid data!"
          });
        });
    },
    updateCourse() {
      this.form
        .put("/pnc_soa/public/courses/" + this.course.id)
        .then(({ data }) => {
          window.location.href = myRootURL + "/courses/" + data.id;
        })
        .catch(err => {
          console.log(err);
          toast.fire({
            type: "error",
            title: "Please enter valid data!"
          });
        });
    },
    saveCourse() {
      if (this.isUpdate) {
        this.updateCourse();
      } else {
        this.createCourse();
      }
    },
    getRandColor() {
      this.form.color = this.colors[
        Math.floor(Math.random() * this.colors.length)
      ];
    }
  },
  created() {
    this.course = Object.assign({}, this.courseProp);

    this.form.course_code = this.course.course_code;
    this.form.description = this.course.description;
    this.form.lec_unit = this.course.lec_unit;
    this.form.lab_unit = this.course.lab_unit;
    this.form.privacy = this.course.is_public;
    this.form.college_id = this.course.college_id;
    this.form.color = this.course.color;

    if (this.collegeId != "all") {
      this.form.college_id = this.collegeId;
    }

    // form: new Form({
    //     course_code: "",
    //     description: "",
    //     lec_unit: "",
    //     lab_unit: "",
    //     privacy: "",
    //     college_id: "",
    //     color: ""
    //   })
  }
};
</script>
