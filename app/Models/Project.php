<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'deadline',
        'client_id',
        'freelancer_id',
        
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];


    public function clientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    public function freelancerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function projectLogs(): HasMany
    {
        return $this->hasMany(ProjectLogs::class);
    }


}
