<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentQuiz extends Model
{
    use HasFactory;

    protected $fillable=[
        'quizze_id',
        'student_id',
        'score',
        'created_at',
        'updated_at',
    ];
    public function quiz()
    {
        return $this->belongsTo(Quizze::class,'quizze_id','id');
    }
    public function student()
    {
        return $this->belongsTo('App\Models\Student', 'student_id');
    }
}


