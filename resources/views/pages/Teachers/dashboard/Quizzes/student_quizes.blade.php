@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    Students Quizes
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    Students Quizes
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
                                <a href="{{route('quizzes.create')}}" class="btn btn-success btn-sm" role="button"
                                   aria-pressed="true">Add quiz</a><br><br>
                                <div class="table-responsive">
                                    <table id="datatable" class="table  table-hover table-sm table-bordered p-0"
                                           data-page-length="50"
                                           style="text-align: center">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Quiz title</th>
                                            <th>Student</th>
                                            <th>Sections</th>
                                            <th>Year</th>
                                            <th>Department</th>
                                            <th>{{trans('main_trans.date_of_quiz')}}</th>
                                            <th>{{trans('main_trans.quiz_duration')}}</th>
                                            <th>Student Degree</th>
                                            <th>Quiz Degree</th>

                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach($quizes as $quizze)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$quizze->quiz->name}}</td>
                                            <td>{{$quizze->student->name}}</td>
                                            <td>{{$quizze->quiz->grade->Name}}</td>
                                            <td>{{$quizze->quiz->classroom->Name_Class}}</td>
                                            <td>{{$quizze->quiz->section->Name_Section}}</td>
                                            <td>{{$quizze->quiz->date_of_quiz}}</td>
                                            <td>{{$quizze->quiz->quiz_duration}}</td>
                                            <td>{{$quizze->score}}</td>
                                            <td>{{$quizze->quiz->questions->sum('score')}}</td>

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
