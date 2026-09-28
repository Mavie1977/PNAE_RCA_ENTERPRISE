<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowLog extends Model
{
    protected $fillable = [
        'application_id',
        'user_id',
        'from_status',
        'to_status',
        'comment',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}