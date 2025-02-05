<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Program;
use App\Curriculum;
use App\College;
use App\CurriculumCourse;
use App\CourseRequisite;
use App\Course;
use App\Http\Resources\CurriculumResource;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\CurriculumMappingStatus;
use App\CurriculumMap;
use Gate;

class CurriculaController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //authenticate
        if(!Gate::allows('isDean') && !Gate::allows('isSAdmin') && !Gate::allows('isProf')) {
            return abort('401', 'Unauthorized');
        }


        // if(Auth::user()->user_type_id == 's_admin') {
        //     $colleges = College::all();
        //     if(request('college_id') == '') {
        //         return view('curricula.list')->with('colleges', $colleges);
        //     }
            
        // } else {
        //     //validate
        //     if(request('college_id') == '') {
        //         // abort('404', 'Page not found');
        //         return redirect('/curricula?college_id='. Session::get('college_id'));
        //     } else if (request('college_id') != Session::get('college_id')) {
        //         return abort('401', 'Unauthorized');
        //     }
        // }

        if(Auth::user()->user_type_id == 's_admin') {
            $programs = Program::all();

            if(request('program_id') == '') {
                $curricula = Curriculum::select('curricula.*')
                                ->join('programs', 'programs.id', '=', 'curricula.program_id')
                                ->join('colleges', 'colleges.id', '=', 'programs.college_id')
                                ->latest()
                                ->with('program')
                                ->get();
            } else {
                $curricula = Curriculum::select('curricula.*')
                                ->join('programs', 'programs.id', '=', 'curricula.program_id')
                                ->join('colleges', 'colleges.id', '=', 'programs.college_id')
                                ->latest()
                                ->where('program_id', request('program_id'))
                                ->with('program')
                                ->get();
            }
        } else {
            $programs = Program::where('college_id', Session::get('college_id'))->get();
            $curricula = Curriculum::select('curricula.*')
                                ->join('programs', 'programs.id', '=', 'curricula.program_id')
                                ->join('colleges', 'colleges.id', '=', 'programs.college_id')
                                ->where('colleges.id', Session::get('college_id'))
                                ->latest()
                                ->with('program')
                                ->get();

        }
        
        /*$curricula = [];

        $list = DB::select('SELECT ref_id, max(curricula.id) as id from ((curricula INNER JOIN programs ON programs.id = curricula.program_id) INNER JOIN colleges ON colleges.id = programs.college_id) WHERE programs.college_id = :college_id GROUP BY ref_id', ['college_id' => request('college_id')]);

        foreach ($list as $item) {
            $curricula[] = Curriculum::find($item->id);
        }*/

        //$college = College::findOrFail(request('college_id'));
        $colleges = College::all();


        return view('curricula.index')
            ->with('programs', $programs)
            ->with('colleges', $colleges)
            ->with('curricula', $curricula);
    }

    public function print_curriculum(Curriculum $curriculum) {

        $templates = [];

        for($year = 1; $year <= $curriculum->year_level; $year++) {
            for($sem = 1; $sem <= 3; $sem++) {
                $curriculum_courses = CurriculumCourse::where('curriculum_id', $curriculum->id)
                                        ->where('year_level', $year)
                                        ->where('semester', $sem)
                                        ->where('is_active', true)
                                        ->get();
                $total_lec_units = 0;
                $total_lab_units = 0;

                foreach ($curriculum_courses as $curriculum_course) {
                    $course_requisites = $curriculum_course->courseRequisites;
                    $course_requisites_str = 'None';

                    if(count($course_requisites) > 0) {
                        $course_requisites_str = '';
                        foreach ($course_requisites as $course_requisite) {
                            $course_requisites_str .= $course_requisite->preReq()->course->course_code;
                            $course_requisites_str .= ';';
                        }
                    }

                    $total_lec_units += $curriculum_course->course->lec_unit;
                    $total_lab_units += $curriculum_course->course->lab_unit;
                    $curriculum_course->course_requisites_str = $course_requisites_str;
                }

                $templates[] = [
                    'year_sem' => $this->numIndex($year) . ' year /' . $this->numIndex($sem) . ' sem',
                    'curriculum_courses' => $curriculum_courses,
                    'total_lec_units' => $total_lec_units,
                    'total_lab_units' => $total_lab_units
                ];
            }
        }

        return view('curricula.print_curriculum', compact('curriculum', 'templates'));
    }

    private function numIndex($num) {
        if($num == 1) {
            return "1st";
        } else if ($num == 2) {
            return "2nd";
        } else if ($num == 3) {
            return  "3rd";
        } else if ($num >= 4) {
            return $num . "th";
        } else {
            return $num;
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Gate::allows('isDean') && !Gate::allows('isSAdmin')) {
            return abort('401', 'Unauthorized');
        }

        $request->validate([
            'program_id' => 'required',
            'name' => 'required|max:255|regex:/^[\pL\s\-0-9_]+$/u',
            'description' => 'nullable|max:255|regex:/^[\pL\s\-0-9_]+$/u',
            'year' => 'required|digits:4',
            'year_level' => 'required'
        ]);

        $curriculum = Curriculum::create([
            'program_id' => request('program_id'),
            'name' => strtoupper(request('name')),
            'description' => request('description'),
            'year' => request('year'),
            'user_id' => Auth::user()->id,
            'year_level' => request('year_level'),
            'revision_no' => 1
        ]);



        $curriculum->ref_id = $curriculum->id;

        $curriculum->save();

        CurriculumMappingStatus::create([
            'curriculum_id' => $curriculum->id,
            'status' => false
        ]);

        Session::flash('message', 'Curriculum successfully added to database');

        return $curriculum;

    }

    public function saveCurriculum(Request $request, $id) {
        if(!Gate::allows('isDean') && !Gate::allows('isSAdmin')) {
            return abort('401', 'Unauthorized');
        }

        $curriculum = Curriculum::findOrFail($id);
        $curriculum->is_saved = true;
        $curriculum->update();

        Session::flash('message', 'Curriculum successfully saved!');

        return redirect('/curricula/' . $curriculum->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $courses_id = [];
        $curriculum = Curriculum::findOrFail($id);
        $colleges = College::all();
        $curriculum_courses = CurriculumCourse::where("curriculum_id", $curriculum->id)->get();

        foreach ($curriculum_courses as $curriculum_course) {
            $courses_id[] = $curriculum_course->course_id;
        }

        $courses = Course::select("courses.*")
                        ->whereNotIn('id', $courses_id)
                        ->latest()
                        ->get();

        if($request->ajax() && request('json') == 'yes') {
            return new CurriculumResource($curriculum);
        }
        

        return view('curricula.show')
            ->with('curriculum', $curriculum)
            ->with('courses', $courses)
            ->with('colleges', $colleges);
    }


    public function edit($id) {
        $curriculum = Curriculum::findOrFail($id);
        $curriculum->is_saved = false;
        $curriculum->update();

        Session::flash('message', 'You can edit this curriculum now.');

        return redirect('/curricula/' . $curriculum->id);
    }

    public function revise(Request $request, $id) {
        if(!Gate::allows('isDean') && !Gate::allows('isSAdmin')) {
            return abort('401', 'Unauthorized');
        }

        $request->validate([
            'program_id' => 'required',
            'name' => 'required|max:255|regex:/^[\pL\s\-0-9_]+$/u',
            'description' => 'nullable|max:255|regex:/^[\pL\s\-0-9_]+$/u',
            'year' => 'required|digits:4',
            'year_level' => 'required'
        ]);

        $curriculum = Curriculum::findOrFail($id);


        $curriculum->load('curriculumCourses');

        $newCurriculum = $curriculum->replicate();
        $newCurriculum->name = request('name');
        $newCurriculum->description = request('description');
        $newCurriculum->year = request('year');
        $newCurriculum->year_level = request('year_level');
        $newCurriculum->revision_no += 1;
        $newCurriculum->is_saved = false;
        $newCurriculum->push();

        foreach($curriculum->getRelations() as $relation => $items){
            foreach($items as $item){
                $cloneItem = clone $item;
                unset($item->id);
                $newItem = $newCurriculum->{$relation}()->create($item->toArray());
                //try lang
                foreach ($cloneItem->courseRequisites as $requisite) {
                    $course_id = CurriculumCourse::find($requisite->pre_req_id)->course->id;
                    //find curriculum course in the curriculum
                    $curriculum_course = CurriculumCourse::where('course_id', $course_id)
                    ->where('curriculum_id', $newCurriculum->id)
                    ->first();

                    //then add the requisite
                    // $course_requisite = new CourseRequisite();
                    // $course_requisite->curriculum_course_id = $newItem->id;
                    // $course_requisite->type = 2;
                    // $course_requisite->pre_req_id = $curriculum_course->id;
                    // $course_requisite->create();
                    CourseRequisite::create([
                        'curriculum_course_id' => $newItem->id,
                        'type' => 2,
                        'pre_req_id' => $curriculum_course->id
                    ]);

                    

                }

                //clone the curriculum mapping
                $curriculum_maps = CurriculumMap::where('curriculum_course_id', $cloneItem->id)->where('is_checked', true)->get();

                foreach ($curriculum_maps as $curriculum_map) {
                    $new_curriculum_map = new CurriculumMap();
                    $new_curriculum_map->student_outcome_id = $curriculum_map->student_outcome_id;
                    $new_curriculum_map->curriculum_course_id = $newItem->id;
                    $new_curriculum_map->is_checked = $curriculum_map->is_checked;
                    $new_curriculum_map->learning_level_id = $curriculum_map->learning_level_id;
                    $new_curriculum_map->save();
                }



            }
        }

        //clone the curriculum mapping
        // foreach ($curriculum->curriculumCourses as $curriculum_course) {
            
        // }

        CurriculumMappingStatus::create([
            'curriculum_id' => $newCurriculum->id,
            'status' => false
        ]);

        Session::flash('message', 'Curriculum successfully revised.');

        return $newCurriculum;
    }

    public function deactivatedCourses($id) {
        $curriculum_courses =  CurriculumCourse::where('curriculum_id', $id)
            ->where('is_active', 0)
            ->paginate(10);

        return view('curricula.deactivated_courses')->with('curriculum_courses', $curriculum_courses);
    }
}
