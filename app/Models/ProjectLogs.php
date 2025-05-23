<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectLogs extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectsLogsFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'start_time',
        'end_time',
        'description',
        'duration',
        'tag'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration' => 'string',
        'tag' => 'string',
    ];


    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
