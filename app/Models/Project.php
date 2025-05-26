<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
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

    public static function getProjectDurationByUser(int $userId, bool $isFreelancer = true): string
    {
        $totalDuration = self::where($isFreelancer ? 'freelancer_id' : 'client_id', $userId)
            ->get()
            ->reduce(function (\Carbon\CarbonInterval $total, $project) {
                return $total->addSeconds(self::getTotalDuration($project->id)->seconds);
            }, \Carbon\CarbonInterval::hours(0));

        return $totalDuration->cascade()->forHumans(['short' => true, 'parts' => 2]);
    }

    public static function getMonthlyDurations(?int $userId = null): array
    {
        $monthlyDurations = [];

        $userId = $userId ?: Auth::user()->id;

        $logs = ProjectLogs::whereNotNull('end_time')
            ->whereIn('project_id', self::where('client_id', $userId)->pluck('id'))
            ->get();

        foreach ($logs as $log) {
            $month = $log->start_time->format('Y-m');
            $duration = $log->duration ? \Carbon\Carbon::parse($log->duration)->secondsSinceMidnight() : 0;

            if (!isset($monthlyDurations[$month])) {
                $monthlyDurations[$month] = 0;
            }

            $monthlyDurations[$month] += $duration;
        }

        return array_map(function ($seconds) {
            return \Carbon\CarbonInterval::seconds($seconds)->cascade()->forHumans(['short' => true, 'parts' => 2]);
        }, $monthlyDurations);
    }
}
