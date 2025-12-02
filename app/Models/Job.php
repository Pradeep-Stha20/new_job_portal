<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category_id',
        'job_type_id',
        'user_id',
        'vacancy',
        'salary_min',
        'salary_max',
        'salary_negotiable',
        'location',
        'description',
        'benefits',
        'responsibility',
        'qualifications',
        'keywords',
        'experience',
        'company_name',
        'company_location',
        'company_website',
        'fit_score',
        'fraud_score',
        'status'
    ];

    protected $casts = [
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'salary_negotiable' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function jobType() {
        return $this->belongsTo(JobType::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function applications() {
        return $this->hasMany(JobApplication::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
