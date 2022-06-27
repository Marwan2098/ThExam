@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    {{trans('main_trans.list_students')}}
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    {{trans('main_trans.today_quizes')}}
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <div class="card card-statistics h-100">
                <div class="card-body">
                    <div class="col-xl-12 mb-30">
                        <div class="card card-statistics h-100">
                            <div class="card-body">
                                @if (session('student_score'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('student_score') }}
                                </div>
                                @endif
                                <div class="table-responsive">
                                    <table id="datatable" class="table  table-hover table-sm table-bordered p-0"
                                           data-page-length="50"
                                           style="text-align: center">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Quiz title</th>
                                                <th>Doctor</th>
                                                <th>Sections</th>
                                                <th>Year</th>
                                                <th>Department</th>
                                                <th>{{trans('main_trans.date_of_quiz')}}</th>
                                                <th>{{trans('main_trans.quiz_duration')}}</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($quizes as $quizze)
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{$quizze->name}}</td>
                                                <td>{{$quizze->teacher->Name}}</td>
                                                <td>{{$quizze->grade->Name}}</td>
                                                <td>{{$quizze->classroom->Name_Class}}</td>
                                                <td>{{$quizze->section->Name_Section}}</td>
                                                <td>{{$quizze->date_of_quiz}}</td>
                                                <td>{{$quizze->quiz_duration}}</td>
                                                <td>
                                                    @php
                                                $check_quiz_performed=App\Models\StudentQuiz::where('quizze_id',$quizze->id)
                                                ->where('student_id',auth()->guard('student')->user()->id)->first();
                                                    @endphp
                                                    @if(! $check_quiz_performed)
                                                    <a href="{{url('/student/perform-exam/'.$quizze->id)}}"
                                                       class="btn btn-info btn-sm" role="button" aria-pressed="true"><i
                                                            class="fa fa-open"></i>Start Quiz</a>
                                                        @else
                                                        <a class="btn btn-danger text-white btn-sm" role="button" aria-pressed="true"><i
                                                                 class="fa fa-open"></i>Quiz Finished</a>
                                                        @endif
                                                </td>
                                            </tr>

                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
@endsection
@section('js')
    @toastr_js
    @toastr_render
@endsection
