<template>
  <div>
    <!-- Activator -->
    <!-- <button
        type="button"
        class="btn btn-success btn-success btn-round"
        data-toggle="modal"
        data-target="#curriculumModal"
      >
        Add New Curriculum <i class="fa fa-plus"></i>
      </button> -->
    <div class="dropdown" v-if="!isRevise">
      <button
        class="btn btn-success-b"
        type="button"
        id="dropdownMenuButton"
        data-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
      >
        Add New Curriculum <i class="fa fa-caret-down"></i>
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a
          v-for="program in programs"
          :key="program.id"
          @click="selectProgram(program)"
          data-toggle="modal"
          data-target="#curriculumModal"
          class="dropdown-item d-flex justify-content-between"
          href="#"
        >
          <div>
            <i class="fa fa-chevron-right text-primary"></i>
            {{ program.program_code }}
          </div>
          <div>
            <i class="fa fa-plus text-success"></i>
          </div>
        </a>
        <!--           <a 
            data-toggle="modal"
            data-target="#curriculumModal" 
            class="dropdown-item d-flex justify-content-between" 
            href="#">
            <div>
            <i class="fa fa-chevron-right text-primary"></i> BSCS
            </div> 
            <div>
            <i class="fa fa-plus text-success"></i>
            </div>
          </a> -->
      </div>
    </div>
    <!-- End Activator -->

    <!-- Modal -->

    <!-- Modal -->
    <div
      class="modal fade"
      id="curriculumModal"
      tabindex="-1"
      role="dialog"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <form
          @submit.prevent="saveCurriculum"
          @keydown="form.onKeydown($event)"
          autocomplete="off"
        >
          <!-- <form action="" autocomplete="off"></form> -->
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
              <!-- Field for name  -->
              <div class="form-group">
                <label for="name">Name</label>

                <div>
                  <input
                    id="name"
                    type="text"
                    class="form-control"
                    name="name"
                    v-model="form.name"
                    v-uppercase
                    :class="{ 'is-invalid': form.errors.has('name') }"
                    :readonly="isRevise"
                  />
                  <has-error :form="form" field="name"></has-error>
                </div>
              </div>
              <!-- /end Field for name -->

              <!-- Field for description  -->
              <div class="form-group">
                <label for="description">Description (optional)</label>

                <div>
                  <textarea
                    id="description"
                    type="text"
                    class="form-control"
                    name="description"
                    v-model="form.description"
                    :class="{ 'is-invalid': form.errors.has('description') }"
                  ></textarea>
                  <has-error :form="form" field="description"></has-error>
                </div>
              </div>
              <!-- /end Field for description -->

              <!-- Field for year  -->
              <div class="form-group">
                <label for="year">Year</label>

                <div>
                  <select
                    id="year"
                    class="form-control"
                    name="year"
                    v-model="form.year"
                    :class="{ 'is-invalid': form.errors.has('year') }"
                  >
                    <option :value="yearNow" selected>{{ yearNow }}</option>
                    <option :value="yearNow + 1">{{ yearNow + 1 }}</option>
                  </select>
                  <has-error :form="form" field="year"></has-error>
                </div>
              </div>
              <!-- /end Field for year -->

              <!-- Field for year level  -->
              <div class="form-group">
                <label for="year">Year Level</label>

                <div>
                  <select
                    id="year_level"
                    class="form-control"
                    name="year_level"
                    v-model="form.year_level"
                    :class="{ 'is-invalid': form.errors.has('year_level') }"
                  >
                    <option :value="4" selected>4</option>
                    <option :value="5 + 1">5</option>
                  </select>
                  <has-error :form="form" field="year_level"></has-error>
                </div>
              </div>
              <!-- /end Field for year level -->
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

    <!-- End Modal -->
  </div>
</template>

<script>
export default {
  props: ["programs", "isRevise", "reviseProgram", "curriculum", "curricula"],
  data() {
    return {
      form: new Form({
        id: "",
        program_id: "",
        name: "",
        description: "",
        year: new Date().getFullYear(),
        year_level: "4"
      }),
      selectedProgram: {},
      yearNow: ""
    };
  },
  computed: {
    modalTitle() {
      return this.isRevise ? "Revise Curriculum" : "Add new Curriculum";
    },
    saveTitle() {
      return this.isRevise ? "Revise" : "Add to database";
    }
  },
  methods: {
    selectProgram(program) {
      this.form.program_id = program.id;
      this.selectedProgram = program;
    },
    createCurriculum() {
      this.form
        .post("curricula")
        .then(({ data }) => {
          window.location.href = myRootURL + "/curricula/" + data.id;
        })
        .catch(err => {
          console.log(err);
          toast.fire({
            type: "error",
            title: "Please Enter valid data!"
          });
        });
    },
    reviseCurriculum() {
      this.form
        .post("../curricula/" + this.form.id + "/revise")
        .then(({ data }) => {
          window.location.replace(myRootURL + "/curricula/" + data.id);
        })
        .catch(err => {
          console.log(err);
          toast.fire({
            type: "error",
            title: "Please Enter valid data!"
          });
        });
    },
    saveCurriculum() {
      if (this.isRevise) {
        this.reviseCurriculum();
      } else {
        this.createCurriculum();
      }
    }
  },
  created() {
    this.yearNow = new Date().getFullYear();
    if (this.isRevise) {
      this.selectedProgram = this.reviseProgram;
      this.form.id = this.curriculum.id;
      this.form.program_id = this.curriculum.program_id;
      this.form.name = this.curriculum.name;
      this.form.year = this.curriculum.year;
      this.form.year_level = this.curriculum.year_level;
    }
  }
};
</script>
