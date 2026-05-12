<?php

namespace App\Models\Teacher;

use App\Models\Student\Standard;
use App\Models\Student\Subject;
use App\Models\Teacher\TeacherDetail;
use Illuminate\Database\Eloquent\Model;

class TeacherSubject extends Model
{
    protected $fillable = [
        'teacher_detail_id',
        'subject_id',
        'section_id',
        'organization_id'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(TeacherDetail::class, 'teacher_detail_id');
    }
}
