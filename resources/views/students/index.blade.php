@extends('layout.app', ['active' => 'students'])

@section('title', 'Students Index')

@section('content')
<div id="app">
  <update-student-modal :student-id="student_id" v-on:refresh-students="getStudents"></update-student-modal>
  
  

  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between mb-1">
        <div>
          <h1 class="page-header"><i class="fa fa-user text-info"></i> Student Management</h1>
        </div>
        <div>
          @if(Gate::check('isProf') || Gate::check('isDean') || Gate::check('isSAdmin'))
            {{-- <a href="{{ url('/students/create') }}" class="btn btn-success-b">Add New Student</a> --}}
            <add-student-modal :colleges='@json($colleges)' 
              :programs='@json($programs)' 
              :curriculums='@json($curriculums)'
              v-on:refresh-students="getStudents"></add-student-modal>
          @endif
        </div>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="input-group mb-3" id="search-input">
            
            <input v-on:input="searchStudent" v-model="search" type="search" class="form-control" placeholder="Search student...">
            <div class="input-group-append">
              <span class="input-group-text"><i class="fa fa-search"></i></span>
            </div>
          </div>
        </div>
        <div class="col-md-8">
          <div class="d-flex justify-content-end">
            <div class="d-flex mr-4">
              <div class="mr-2"> <label class="col-form-label"> Filter By College: </label></div>
              <div>
                <select class="form-control" v-on:change="filterByCollege" v-model="college_id">
                  <option value="">All</option>
                  <option v-for="college in colleges" :key="college.id" :value="college.id">@{{ college.college_code }}</option>
                </select>
              </div>
            </div>
            <div class="d-flex">
              <div class="mr-2"><label class="col-form-label">Filter By Program: </label></div>
              <div>
                <select class="form-control" v-model="program_id" v-on:change="filterByProgram">
                  <option value="">All</option>
                  <option v-for="program in selectedPrograms" :key="program.id" :value="program.id">@{{ program.program_code }}</option>
                </select>
              </div>
            </div>
          </div>
        </div>      
      </div>
      
      <div class="table-responsive">
        <table id="students-table" class="table table-borderless">
          <thead>
            <tr>
              <th scope="col">Student ID</th>
              <th scope="col" width="20%">Full Name</th>
              <th scope="col" width="20%">Email</th>
              <th scope="col">College</th>
              <th scope="col">Program</th>
              <th scope="col">OBE</th>
              <th scope="col" class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <template v-if="tableLoading">
              <tr>
                <td colspan="7"><table-loading></table-loading></td>
              </tr>
            </template>
            <template v-else-if="students.length <= 0">
              <tr>
                <td class="text-center" colspan="7">No Record Found in Database.</td>
              </tr>
            </template>
            <template v-else>
              <tr v-for="student in students" >
                  <td>@{{ student.student_id }}</td>
                  <td>@{{ student.full_name }}</td>
                  <td>@{{ student.email }}</td>
                  <td>@{{ student.college_code }}</td>
                  <td>@{{ student.program_code }}</td>
                  <td>
                    <a :href="'students/' + student.id + '/obe_curriculum'" class="btn btn-info btn-sm">
                      <i class="fa fa-file-alt"></i> View
                    </a>
                  </td>
                  <td>
                    
                    <button title="Edit" v-on:click="openUpdateModal(student.id)" class="btn btn-success btn-sm">
                      Update <i class="fa fa-edit"></i>
                    </button>

                    <a title="View Details" class="btn btn-info btn-sm" :href=" 'students/' + student.id">
                      Info <i class="fa fa-search"></i>
                    </a>
                    
                  </td>
              </tr>
            </template>
            
              
            
          </tbody>
        </table>
      </div>
      <nav v-show="search.trim() == ''">
        <ul class="pagination justify-content-end">
          <li class="page-item" :class="{ disabled: meta.current_page == 1 }">
            <a class="page-link" href="#" v-on:click.prevent="paginate(meta.current_page - 1)">Previous</a>
          </li>
          <template v-for="num in totalPagination">
            <li class="page-item" :class="{ active : num == meta.current_page }">
              <a class="page-link" href="#" v-on:click.prevent="paginate(num)">@{{ num }}</a>
            </li>
          </template>
          <li class="page-item" :class="{ disabled: meta.current_page == meta.last_page }">
            <a class="page-link" href="#" v-on:click.prevent="paginate(meta.current_page + 1)">Next</a>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  {{-- <script>
    $(document).ready(function() {
      $('#faculty-table').DataTable();
    })
  </script> --}}
  <script>
    var vm = new Vue({
      el: '#app',
      data: {
        students: [],
        search: '',
        meta: {
          total: 0,
          per_page: 0,
          current_page: 1
        },
        links: {},
        totalPagination: 0,
        tableLoading: true,
        colleges: @json($colleges),
        programs: @json($programs),
        college_id: '',
        program_id: '',
        selectedPrograms: [],
        student_id: ''
      },
      methods: {
        getStudents(page=1) {
          this.tableLoading = true;
          ApiClient.get('/students?page=' + page + '&json=true' + '&filter_by_college=' + this.college_id + '&filter_by_program=' + this.program_id)
          .then(response => {
            this.students = response.data.data;
            this.meta = response.data.meta;
            this.links = response.data.links;
            this.totalPagination = Math.ceil(this.meta.total / this.meta.per_page);
            this.tableLoading = false;
          });
        },
        searchStudent: _.debounce(() => {
            vm.tableLoading = true;
            if(vm.search.trim() == '') {
              return vm.getStudents();
            }
            ApiClient.get('/students?q=' + vm.search + '&json=true')
            .then(response => {
              vm.students = response.data.data;
              vm.tableLoading = false;
            })
          }, 500),
        paginate(page) {
          this.getStudents(page);
        },
        filterByCollege() {
          
          this.selectedPrograms = this.programs.filter(program => {
            return program.college_id == this.college_id
          });

          if(this.college_id == '') {
            this.program_id = '';
          }

          this.getStudents();
        },
        filterByProgram() {
          for(let i = 0; i < this.programs.length; i++) {
            if(this.programs[i].id == this.program_id) {
              this.college_id = this.programs[i].college_id;
              break;
            }
          }

          this.getStudents();
        },
        openUpdateModal(student_id) {
          this.student_id = student_id;
          $('#updateStudentModal').modal('show');
        }
      },
      created() {
        setTimeout(() => {
          this.getStudents(this.meta.current_page);
        }, 1000);

        this.selectedPrograms = this.programs;
        
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