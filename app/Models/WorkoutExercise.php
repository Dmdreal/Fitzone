<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutExercise extends Model
{
    protected $fillable = [
        'workout_plan_id',
        'exercise_name',
        'sets',
        'reps',
        'instructions',
        'trainer_notes',
        'sort_order',
    ];

    public function workoutPlan()
    {
        return $this->belongsTo(WorkoutPlan::class);
    }
}
