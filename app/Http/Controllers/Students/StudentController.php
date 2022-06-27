<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentsRequest;
use App\Models\Notification;
use App\Repository\StudentRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\Quizze;
use App\Models\Question;
use App\Models\StudentQuiz;
use Carbon\Carbon;
class StudentController extends Controller
{

    protected $Student;

    public function __construct(StudentRepositoryInterface $Student)
    {
        $this->Student = $Student;
    }


    public function index()
    {
       return $this->Student->Get_Student();
    }


    public function create()
    {
        return $this->Student->Create_Student();
    }

    public function store(StoreStudentsRequest $request)
    {
       return $this->Student->Store_Student($request);
    }

    public function show($id){

     return $this->Student->Show_Student($id);

    }


    public function edit($id)
    {
       return $this->Student->Edit_Student($id);
    }


    public function update(StoreStudentsRequest $request)
    {
        return $this->Student->Update_Student($request);
    }


    public function destroy(Request $request)
    {
        return $this->Student->Delete_Student($request);
    }

    public function Get_classrooms($id)
    {

       return $this->Student->Get_classrooms($id);
    }

    public function Get_Sections($id)
    {
        return $this->Student->Get_Sections($id);
    }

    public function Upload_attachment(Request $request)
    {
        return $this->Student->Upload_attachment($request);
    }

    public function Download_attachment($studentsname,$filename)
    {
        return $this->Student->Download_attachment($studentsname,$filename);
    }

    public function Delete_attachment(Request $request)
    {
        return $this->Student->Delete_attachment($request);

    }

    public function today_quizzes()
    {
        $today=Carbon::now()->format('Y-m-d H:i');

        $quizes=Quizze::where('grade_id',auth()->guard('student')->user()->Grade_id)
        ->where('classroom_id',auth()->guard('student')->user()->Classroom_id)
        ->where('section_id',auth()->guard('student')->user()->section_id)
        ->where('date_of_quiz','<=',$today)
        ->get();
        if(count($quizes)){
            foreach($quizes as $quiz){
               $notifications = Notification::where('destination_id',$quiz->id)->get();
               foreach( $notifications as  $notification){
                $notification->update(['seen_at' => $today]);
               }

            }
        }
        return view('pages.Students.today_quizzes',compact('quizes'));
    }
    public function previous_quizes()
    {
        $today=now();
        $quizes=StudentQuiz::where('student_id',auth()->guard('student')->user()->id)
        ->get();
        return view('pages.Students.previous_quizes',compact('quizes'));
    }

    public function perform_exam($quiz_id)
    {
        $quiz=Quizze::find($quiz_id);

        $today=Carbon::now()->format('Y-m-d H:i');

        $check_quiz_performed=StudentQuiz::where('quizze_id',$quiz->id)
        ->where('student_id',auth()->guard('student')->user()->id)->first();
        if(! $check_quiz_performed && session()->has('quiz_test_id'))
        {
            //Check Personal eye and face Test
            session()->put('quiz_test_id',$quiz->id);
            if(session()->has('quiz_test_id'))
            {
                $end_time=Carbon::parse($quiz->date_of_quiz)
                ->addMinutes($quiz->quiz_duration);

                return view('pages.Students.perform_exam',compact('quiz','end_time'));

            }else{
                session()->put('quiz_test_id',$quiz->id);


                return redirect(url('student/eye-test/'.$quiz->id));
            }

        }
        else{
            session()->put('quiz_test_id',$quiz->id);


            return redirect(url('student/eye-test/'.$quiz->id));

        }

    }

    public function eyeTest($quiz_id)
    {
        if(session()->has('quiz_test_id')) {
            $quiz=Quizze::find(session('quiz_test_id'));
            $end_time=Carbon::parse($quiz->date_of_quiz)
            ->addMinutes($quiz->quiz_duration);

            return view('pages.Students.perform_exam',compact('quiz','end_time'));
        }
        session()->put('quiz_test_id',$quiz_id);

        return view('pages.Students.eye_test',["quiz_id"=>session()->has('quiz_test_id')]);
    }

    public function eyeTestCompleted()
    {
        $quiz = Quizze::findOrFail(session('quiz_test_id'));

        return response()->json(['status'=>true]);
    }

    public function abort($type)
    {
        StudentQuiz::create(['quizze_id'=>session('quiz_test_id'),'student_id'=>auth()->guard('student')->user()->id,'score'=>0]);
        return response()->json(['status'=>true]);

    }

    public function end_exam(Request $request)
    {
        $quiz=Quizze::find($request->quiz_id);
        $score=0;
        if($quiz)
        {
            if($request->question){
            for($i=0;$i<count($request->question);$i++)
            {
                $question_id=$request->question[$i];
                $question=Question::find($question_id);
                if($question)
                {
                    if($question->right_answer==$request->right_answer[$i])
                    {
                        $score+=$question->score;
                    }
                }
            }
        }
            //Store Quiz in Student Quezes
            $check_quiz=StudentQuiz::where('quizze_id',$quiz->id)->where('student_id',auth()->guard('student')->user()->id)->first();
            if(! $check_quiz)
            {
                StudentQuiz::create(['quizze_id'=>$quiz->id,'student_id'=>auth()->guard('student')->user()->id,'score'=>$score]);
            }
            $quiz_score=$quiz->questions->sum('score');
            $student_score="Quiz Degree: ".$score.' / ' .$quiz_score;
            return redirect(url('student/today-quizzes'))->with('student_score',$student_score);

            session()->forget('quiz_test_id');

        }
        else{
            return back();
        }
    }



}
