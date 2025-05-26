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

    public static function getTotalDuration(int $projectId): \Carbon\CarbonInterval
    {
        $logs = ProjectLogs::where('project_id', $projectId)
            ->whereNotNull('end_time')
            ->get();

        return $logs->reduce(function (\Carbon\CarbonInterval $total, $log) {
            return $total->addSeconds($log->duration ? \Carbon\Carbon::parse($log->duration)->secondsSinceMidnight() : 0);
        }, \Carbon\CarbonInterval::hours(0));
    }

    public static function getProjectDurationByUser(int $userId): string
    {
        $totalDuration = self::where('freelancer_id', $userId)
            ->get()
            ->reduce(function (\Carbon\CarbonInterval $total, $project) {
                return $total->addSeconds(self::getTotalDuration($project->id)->seconds);
            }, \Carbon\CarbonInterval::hours(0));

        return $totalDuration->cascade()->forHumans(['short' => true, 'parts' => 2]);
    }
}
