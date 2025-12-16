<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Score extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','type','score','topic','title','created_at'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'user_id');
    }
}
