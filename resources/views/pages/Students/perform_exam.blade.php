@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
Perform Exam
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
Perform Exam
@stop
<!-- breadcrumb -->
@endsection
@section('content')

<input class="red_url" value="{{ url('/student/today-quizzes') }}" type="hidden">
<input class="quiz_time" value="{{$end_time}}" type="hidden">


    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <div class="card card-statistics h-100">
                <div class="card-body">

                    @if(session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>{{ session()->get('error') }}</strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="alert alert-info">
                        <strong>Timer: </strong>
                        <div id="timer" style="font-size: 20px"></div>
                    </div>

                    <div id="question-wrapper">

                    <div class="col-xs-12"  >
                        <div class="col-md-12">
                            <br>
                            <span style="color: red">Question Numbers:{{$quiz->questions->count()}}</span>
                            <span style="color: red">Quiz Time:{{$quiz->quiz_duration}} hour(s)</span>
                            <form action="{{ url('/student/end-exam') }}" method="post" autocomplete="off" id="form-test">
                                @csrf
                                <input type="hidden" name="quiz_id" value="{{$quiz->id}}">
                                @foreach ($quiz->questions as $question )

                                <input type="hidden" value="{{$question->id}}" name="question[]">
                                <div class="form-row">

                                    <div class="col">

                                        <label for="title">Question</label>
                                        <input type="text" readonly value="{{$question->title}}" id="input-name"
                                               class="form-control form-control-alternative" autofocus>
                                    </div>
                                </div>
                                <br>

                                <div class="form-row">
                                    <div class="col">
                                        <label for="title">Answer</label>
                                        <select name="right_answer[]"  class="form-control form-control select2" style="padding:3px">
                                            @php $answers = explode(PHP_EOL, $question->answers); @endphp
                                            <option value="">Please Select</option>
                                            @foreach ($answers as $answer)
                                            <option value="{{$answer}}">{{$answer}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <br>
                                @endforeach

                                <button class="btn btn-success btn-sm nextBtn btn-lg pull-right" onclick="finish()" type="submit">End Exam</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
@endsection
@section("js")
<script src="{{ asset('js/face/webgazer.js') }}" type="text/javascript"></script>

<script>
    $("#question-wrapper").hide();

    jQuery.fn.center = function () {
        this.css("position","absolute");
        this.css("top", "20px");
        this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) +
            $(window).scrollLeft()) + "px");
        return this;
    }

    let isFinish = false;
    function finish() {
        isFinish = true;
        $("#form-test").submit();
    }
    $(function() {

        $(window).blur(function() {
            if(isFinish === false) {
                $.get("{{url('/api/abort-test/{error}')}}");
                alert("Another Browser Tab Opened,Quiz Will Be Closed !");
                red_url=$('.red_url').val();
                location.href = red_url;
            }
        });
    })

    let timeout = null;
    let xPred = null;
    let yPred = null;
    let eyeCatch = false;
    let eyeLost = false;
    let preDiskual = false;

    var targetObj = {};
    var targetProxy = new Proxy(targetObj, {
        set: function (target, key, value) {
            if(key === 'eyeLost' && value === true) {
                faceNotDetected();
            }
            console.log(`${key} set to ${value}`);
            target[key] = value;
            return true;
        }
    });

    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    async function faceNotDetected()
    {
        webgazer.pause();
        isFinish = true;
        $("#question-wrapper").hide();
        // await alert("Wajah tidak terdeteksi!");
        await $.get("{{ url('/api/abort-test/{error}') }}");
        alert("Quiz Will Be Closed Because you leaved camera !");
                red_url=$('.red_url').val();
                location.href = red_url;
        await sleep(1500);
    }

    async function main() {
        await webgazer.setGazeListener(function(data, elapsedTime) {
            if (data == null) {
                return;
            }
            xPred = data.x; //these x coordinates are relative to the viewport
            yPred = data.y; //these y coordinates are relative to the viewport
            eyeCatch = true;
        }).begin();


        let check = setInterval(async ()=> {
            $("#webgazerVideoContainer").center();

            if(eyeCatch) {

                let inside = $("#webgazerFaceFeedbackBox").attr('style');
                if(inside && inside.includes("solid green") && xPred && yPred) {
                    console.log("Face inside");
                    $("#alert-waiting-webcam").hide();
                    $("#question-wrapper").show();
                    clearTimeout(timeout);
                    preDiskual = false;
                } else {
                    $("#question-wrapper").hide();
                    $("#alert-waiting-webcam").html("Please focus on the form, in 3 seconds the system will be locked...").show();
                    if(preDiskual === false) {
                        preDiskual = true;
                        timeout = setTimeout(function() {
                            targetProxy.eyeLost = true;
                            clearInterval(check);

                        }, 3000);
                    }
                }
            }
        }
        ,500);

        // let check2 = setInterval(async ()=>{
        //     $("#webgazerVideoContainer").center();
        // }, 100);
    }

    main();


    // Count down timer
    // Set the countdown end time
    // let countDownDate = new Date(new Date().setHours(new Date().getHours() + 1));
    // alert(countDownDate);
    let countDownDate=new Date($('.quiz_time').val()).getTime();
    // alert(countDownDate);
    // Update countdown every 1 second
    var x = setInterval(function() {

        // To get today's date and time
        // var now = $('.datenow').val();
        var now = new Date().getTime();

        // Find the distance between now and the countdown date
        var distance = countDownDate - now;

        // Calculation of time for days, hours, minutes and seconds
        // var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Output result in element with id="demo"
        document.getElementById("timer").innerHTML =hours + "h "
            + minutes + "m " + seconds + "s ";

        // When the countdown is over, write some text
        if (distance < 0) {
            $("#form-test").submit();
            clearInterval(x);
            // isFinish = true;
            // $.get("{{url('/api/abort-test/{timeout}')}}");

            // alert("Time Out Quiz Will Be Closed !");
                // red_url=$('.red_url').val();
                // location.href = red_url;
            document.getElementById("timer").innerHTML = "EXPIRED";
        }
    }, 1000);

    function disableF5(e) { if ((e.which || e.keyCode) == 116 || (e.which || e.keyCode) == 82) e.preventDefault(); };

    $(document).ready(function(){
        $(document).on("keydown", disableF5);
    });
</script>
@endsection
@section('js')
    @toastr_js
    @toastr_render
@endsection
