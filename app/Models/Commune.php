<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Commune extends Model { protected $fillable=['prefecture_id','name']; public function prefecture(){return $this->belongsTo(Prefecture::class);} }
