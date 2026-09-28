<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Department extends Model { protected $fillable=['ministry_id','name','description','active']; protected $casts=['active'=>'boolean']; public function ministry(){return $this->belongsTo(Ministry::class);} }
