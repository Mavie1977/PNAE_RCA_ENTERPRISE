<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Prefecture extends Model { protected $fillable=['name']; public function communes(){return $this->hasMany(Commune::class);} }
