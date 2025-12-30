<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\GuardGroup;
use App\Services\GuardRotationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GuardPublicController extends Controller
{
    /**
     * Page publique (lecture seule) : semaine courante + passée + à venir
     * Basée sur la rotation automatique (Groupes 1..5)
     */
    public function index(Request $request, GuardRotationService $rotation)
    {
        return $this->render($rotation, 0);
    }

    /**
     * Page publique (lecture seule) : navigation par offset
     * /pharmacies-de-garde/p/-1   => semaine passée
     * /pharmacies-de-garde/p/1    => semaine à venir
     */
    public function page(int $offset, GuardRotationService $rotation)
    {
        return $this->render($rotation, $offset);
    }

    private function render(GuardRotationService $rotation, int $offset)
    {
        $state = $rotation->current();

        if (!($state['configured'] ?? false)) {
            return view('public.guards.index', [
                'configured' => false,
                'message' => $state['message'] ?? "Rotation non configurée.",
            ]);
        }

        // Base = semaine courante (offset 0)
        $baseStart = $state['current_start_at']->copy();
        $baseEnd   = $state['current_end_at']->copy();
        $baseGroup = (int) $state['current_group'];

        // Appliquer offset (semaine passée/à venir)
        $start = $baseStart->copy()->addDays($offset * 7);
        $end   = $baseEnd->copy()->addDays($offset * 7);

        // Groupe correspondant à l'offset
        $groupId = (($baseGroup - 1 + $offset) % 5);
        if ($groupId < 0) $groupId += 5;
        $groupId = $groupId + 1;

        $group = GuardGroup::query()->with(['pharmacies' => function($q){
            $q->orderBy('created_at');
        }])->find($groupId);

        // Prev/Next offset
        $prevOffset = $offset - 1;
        $nextOffset = $offset + 1;

        // Région depuis config rotation
        $region = $state['config']->region ?? null;

        return view('public.guards.index', [
            'configured' => true,
            'region' => $region,
            'offset' => $offset,

            'period_start' => $start,
            'period_end'   => $end,

            'group' => $group,
            'pharmacies' => $group ? $group->pharmacies : collect(),

            'prevOffset' => $prevOffset,
            'nextOffset' => $nextOffset,
        ]);
    }
}
