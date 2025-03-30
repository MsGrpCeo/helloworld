<?php

namespace App\DevModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'date',
        'qid',
        'did',
        'tid',
        'dur',
        'score',
        'exam_type',
        'u_answer'
    ];
}
