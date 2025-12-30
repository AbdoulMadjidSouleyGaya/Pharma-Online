<?php

namespace App\Services;

use App\Models\GuardRotationSetting;
use Carbon\Carbon;

class GuardRotationService
{
    public function current(?Carbon $now = null): array
    {
        $now = $now ?: now();

        $cfg = GuardRotationSetting::query()->first();
        if (!$cfg) {
            return [
                'configured' => false,
                'message' => "Rotation non configurée.",
            ];
        }

        $startAt     = Carbon::parse($cfg->rotation_start_at);
        $cycle       = (int) $cfg->cycle_length; // 5
        $periodDays  = (int) $cfg->period_days;  // 7
        $startGroup  = (int) $cfg->start_group;  // 2

        // Si date actuelle avant démarrage => on reste sur le groupe de départ, période = [startAt, startAt+7j]
        if ($now->lt($startAt)) {
            $currentStart = $startAt->copy();
            $currentEnd   = $startAt->copy()->addDays($periodDays);
            return $this->payload($cfg, $startGroup, $currentStart, $currentEnd, 0);
        }

        $diffSeconds = $startAt->diffInSeconds($now);
        $periodSec   = $periodDays * 24 * 60 * 60;

        $elapsedPeriods = intdiv($diffSeconds, $periodSec);

        $currentStart = $startAt->copy()->addDays($elapsedPeriods * $periodDays);
        $currentEnd   = $currentStart->copy()->addDays($periodDays);

        $group = (($startGroup - 1 + $elapsedPeriods) % $cycle) + 1;

        return $this->payload($cfg, $group, $currentStart, $currentEnd, $elapsedPeriods);
    }

    public function preview(int $weeks = 8): array
    {
        $base = $this->current();
        if (($base['configured'] ?? false) === false) return [];

        $out = [];
        $start = $base['current_start_at']->copy();
        $g = (int) $base['current_group'];

        for ($i=0; $i<$weeks; $i++) {
            $s = $start->copy()->addDays($i * 7);
            $e = $s->copy()->addDays(7);

            $gi = (($g - 1 + $i) % 5) + 1;

            $out[] = [
                'group' => $gi,
                'start' => $s,
                'end'   => $e,
            ];
        }
        return $out;
    }

    private function payload($cfg, int $group, Carbon $start, Carbon $end, int $elapsed): array
    {
        $nextGroup = (($group - 1 + 1) % 5) + 1;
        $prevGroup = (($group - 1 - 1 + 5) % 5) + 1;

        return [
            'configured' => true,
            'config' => $cfg,
            'elapsed_periods' => $elapsed,
            'current_group' => $group,
            'current_start_at' => $start,
            'current_end_at' => $end,
            'next_group' => $nextGroup,
            'next_start_at' => $end->copy(),
            'next_end_at' => $end->copy()->addDays(7),
            'prev_group' => $prevGroup,
            'prev_start_at' => $start->copy()->subDays(7),
            'prev_end_at' => $start->copy(),
        ];
    }
}
