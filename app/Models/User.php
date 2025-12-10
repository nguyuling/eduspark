<?php

namespace App\Models;

// Make sure you import HasFactory
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * We must add all the new registration fields here so they can be written to the database.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // --- NEW FIELDS ---
        'role',
        'district',
        'school_code',
        'phone',
        'user_id', // CRITICAL: Must be fillable for the controller to set it
        // --- END NEW FIELDS ---
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        // Ensure 'role' is cast correctly if you use it in other logic
        'role' => 'string', 
    ];
    
    // --- Relationships ---
    
    // Relationship for Quizzes created by this User (Teacher)
    public function createdQuizzes()
    {
        // Quizzes have a 'teacher_id' column that refers to the User's 'id'
        return $this->hasMany(Quiz::class, 'teacher_id');
    }

    // Relationship for Quiz Attempts by this User (Student)
    public function quizAttempts()
    {
        // QuizAttempts have a 'student_id' column that refers to the User's 'id'
        return $this->hasMany(QuizAttempt::class, 'student_id');
    }
}