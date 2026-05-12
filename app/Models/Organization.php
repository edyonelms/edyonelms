<?php

namespace App\Models;

use App\Models\Admin\SchoolInfo;
use App\Models\Student\Section;
use App\Models\Student\StudentDetail;
use App\Models\Student\Subject;
use App\Models\Teacher\TeacherDetail;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'email',
        'mobile_number',
        'state',
        'education_board',
        'school_code',
        'affiliation_no',
        'udise_number',
        'serial_number',
        'status',
        'logo',
        'address',
        'bank_name',
        'bank_account_no',
        'bank_ifsc',
        'bank_branch',
        'bank_holder_name',
    ];

    protected $casts = ['status' => 'boolean'];

    protected $table = 'organizations';

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function schoolInfo()
    {
        return $this->hasOne(SchoolInfo::class, 'organization_id');
    }
    public function students()
    {
        return $this->hasMany(StudentDetail::class, 'organization_id');
    }
    public function teachers()
    {
        return $this->hasMany(TeacherDetail::class, 'organization_id');
    }
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }
}
