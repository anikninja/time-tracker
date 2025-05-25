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

    public static function startTracking(int $project_id): void
    {
        self::create([
            'project_id' => $project_id,
            'start_time' => now(),
            'end_time' => null,
        ]);
    }

    public static function stopTracking(int $project_id): void
    {
        $log = self::where('project_id', $project_id)
            ->whereNull('end_time')
            ->first();

        if ($log) {
            $log->update([
                'end_time' => now(),
                'duration' => $log->start_time->diff(now())->format('%H:%I:%S'),
            ]);
        }
    }

    public static function getDuration($project_id)
    {
        return self::where('project_id', $project_id)
            ->whereNotNull('end_time')
            ->get()
            ->reduce(function ($total, $log) {
                $durationInSeconds = \Carbon\Carbon::parse($log->duration)->secondsSinceMidnight();
                return $total->addSeconds($durationInSeconds);
            }, \Carbon\CarbonInterval::hours(0))
            ->cascade()
            ->forHumans(['short' => true, 'parts' => 2]);
    }

    public static function shouldHideTracker($project_id)
    {
        return self::where('project_id', $project_id)
            ->whereNull('end_time')
            ->exists();
    }

    public static function isTracking(int $project_id): bool
    {
        return self::where('project_id', $project_id)
            ->whereNull('end_time')
            ->exists();
    }

    public static function getLiveDuration(int $project_id): ?string
    {
        $log = self::where('project_id', $project_id)
            ->whereNull('end_time')
            ->first();

        if ($log) {
            $duration = $log->start_time->diff(now());
            return $duration->format('%H:%I:%S');
        }

        return null;
    }
}
