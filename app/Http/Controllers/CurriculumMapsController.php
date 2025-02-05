<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Curriculum;
use App\CurriculumCourse;
use App\StudentOutcome;
use App\CurriculumMap;
use App\Program;
use App\CurriculumMappingStatus;
use App\LearningLevel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Gate;
use App\College;

class CurriculumMapsController extends Controller
{
    public function __construct() {
      $this->middleware('auth');
    }

    public function index() {

      //authenticate
      if(!Gate::allows('isDean') && !Gate::allows('isSAdmin') && !Gate::allows('isProf')) {
          return abort('401', 'Unauthorized');
      }
      

      if(Auth::user()->user_type_id == 's_admin') {
          if(request('college_id') == 'all' || request('college_id') == '') {
            $curricula = Curriculum::latest()->with('program')->get();      
          } else {
            $curricula = Curriculum::join('programs', 'programs.id', '=', 'curricula.program_id')
              ->join('colleges','colleges.id', '=', 'programs.college_id')
              ->where('programs.college_id', request('college_id'))
              ->select('curricula.*')
              ->latest()
              ->with('program')
              ->where('college_id', request('college_id'))
              ->get(); 
          }  
      } else {
        //validate
        if(request('college_id') == '') {
            // abort('404', 'Page not found');
            return redirect('/curriculum_mapping?college_id='. Session::get('college_id'));
        } else if (request('college_id') != Session::get('college_id')) {
            return abort('401', 'Unauthorized');
        }

        $curricula = Curriculum::join('programs', 'programs.id', '=', 'curricula.program_id')
              ->join('colleges','colleges.id', '=', 'programs.college_id')
              ->where('programs.college_id', request('college_id'))
              ->select('curricula.*')
              ->latest()
              ->with('program')
              ->get(); 
      }

      foreach ($curricula as $curriculum) {
          $curriculum->program->load('college');
      }

      $programs = Program::all();
      $colleges = College::all();

      return view('curriculum_maps.index', compact('curricula', 'programs', 'colleges'));
    }



    public function show($id) {
      //authenticate
      if(!Gate::allows('isDean') && !Gate::allows('isSAdmin') && !Gate::allows('isProf')) {
          return abort('401', 'Unauthorized');
      }


      $curriculum = Curriculum::findOrFail($id);

      $curriculum_courses = CurriculumCourse::where('curriculum_id', $curriculum->id)->where('is_active', 1)->with('course')->get();

      $curriculum_maps = CurriculumMap::join('curriculum_courses', 'curriculum_courses.id', '=', 'curriculum_maps.curriculum_course_id')
        ->join('curricula', 'curricula.id', '=', 'curriculum_courses.curriculum_id')
        ->select('curriculum_maps.*')
        ->where('curriculum_id', $id)
        ->with('learningLevel')
        ->get();

      $curriculum_mapping_status = CurriculumMappingStatus::where('curriculum_id', $curriculum->id)->first();

      $learning_levels = $curriculum->program->learningLevels;

      return view('curriculum_maps.show1')
        ->with('curriculum', $curriculum)
        ->with('curriculum_courses', $curriculum_courses)
        ->with('curriculum_maps', $curriculum_maps)
        ->with('learning_levels', $learning_levels)
        ->with('curriculum_mapping_status', $curriculum_mapping_status);
    }

    public function print_curriculum_mapping(Curriculum $curriculum) {

        $templates = $this->create_curriculum_template($curriculum);

        $curriculum_mapping_templates = $this->create_curriculum_mapping_template($templates, $curriculum);

        $so_count = $curriculum->program->studentOutcomes->count();
        // $curriculum_courses = CurriculumCourse::where('curriculum_id', $curriculum->id)->where('is_active', 1)->with('course')->get();
        
        // $curriculum_maps = CurriculumMap::join('curriculum_courses', 'curriculum_courses.id', '=', 'curriculum_maps.curriculum_course_id')
        //     ->join('curricula', 'curricula.id', '=', 'curriculum_courses.curriculum_id')
        //     ->select('curriculum_maps.*')
        //     ->where('curriculum_id', $curriculum->id)
        //     ->with('learningLevel')
        //     ->get();

        return view('curriculum_maps.print_curriculum_mapping', compact('curriculum', 'curriculum_mapping_templates', 'so_count'));
    }

    private function create_curriculum_mapping_template($templates, $curriculum) {

        foreach ($templates as $template) {
            foreach ($template['curriculum_courses'] as $curriculum_course) {
                $curriculum_maps = [];
                foreach ($curriculum->program->studentOutcomes as $student_outcome) {
                    $curriculum_map = CurriculumMap::where('is_checked', true)
                                    ->where('curriculum_course_id', $curriculum_course->id)
                                    ->where('student_outcome_id', $student_outcome->id)
                                    ->first();
                    $curriculum_maps[] = $curriculum_map;
                } 

                $curriculum_course->curriculum_maps = $curriculum_maps;  
            }
        }

        return $templates;
    }

    private function create_curriculum_template($curriculum) {
        $templates = [];

        for($year = 1; $year <= $curriculum->year_level; $year++) {
            for($sem = 1; $sem <= 3; $sem++) {
                $curriculum_courses = CurriculumCourse::where('curriculum_id', $curriculum->id)
                                        ->where('year_level', $year)
                                        ->where('semester', $sem)
                                        ->where('is_active', true)
                                        ->get();

                $templates[] = [
                    'year_sem' => $this->numIndex($year) . ' year /' . $this->numIndex($sem) . ' sem',
                    'curriculum_courses' => $curriculum_courses
                ];
            }
        }

        return $templates;
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

    // public function get_curriculum_maps($id) {
    //   //authenticate
    //   if(!Gate::allows('isDean') && !Gate::allows('isSAdmin') && !Gate::allows('isProf')) {
    //       return abort('401', 'Unauthorized');
    //   }

    //   $curriculum = Curriculum::findOrFail($id);
    // }

    public function edit($id) {
        //authenticate
        if(!Gate::allows('isDean') && !Gate::allows('isSAdmin')) {
            return abort('401', 'Unauthorized');
        }

        $cm_status = CurriculumMappingStatus::where('curriculum_id', $id)->first();
        $cm_status->status = 0;
        $cm_status->update();

        //Session::flash('message', 'You can now edit this curriculum mapping');

        //return redirect('/curriculum_mapping/' . $id);

        return $cm_status;
    }

    public function saveMaps(Request $request) {
      //authenticate
      if(!Gate::allows('isDean') && !Gate::allows('isSAdmin')) {
          return abort('401', 'Unauthorized');
      }


      DB::beginTransaction();

      try {
        foreach (request('curriculum_maps') as $curriculum_map) {
          if($curriculum_map['id'] == null) {
            //insert
            CurriculumMap::create([
              'curriculum_course_id' => $curriculum_map['curriculum_course_id'],
              'student_outcome_id' => $curriculum_map['student_outcome_id'],
              'is_checked' => $curriculum_map['is_checked'],
              'learning_level_id' => $curriculum_map['learning_level_id']
            ]);

          } else {
            
            //update
            $curriculum_map_retrieved = CurriculumMap::findOrFail($curriculum_map['id']);
            $curriculum_map_retrieved->curriculum_course_id = $curriculum_map['curriculum_course_id'];
            $curriculum_map_retrieved->student_outcome_id = $curriculum_map['student_outcome_id'];
            $curriculum_map_retrieved->is_checked = $curriculum_map['is_checked'];
            $curriculum_map_retrieved->learning_level_id = $curriculum_map['learning_level_id'];
            $curriculum_map_retrieved->update();
          }
        }

        $curriculum_mapping_status = CurriculumMappingStatus::findOrFail(request('curriculum_mapping_status')['id']);
        $curriculum_mapping_status->status = 1;
        $curriculum_mapping_status->update();

        DB::commit();
        // all good

        //Session::flash('message', 'Curriculum mapping successfully saved');
        return $curriculum_mapping_status;
      }
      catch (\Exception $e) {
          DB::rollback();
          // something went wrong
      }

      

    }
}
