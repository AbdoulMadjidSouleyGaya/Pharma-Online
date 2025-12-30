<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuardSchedule;
use App\Models\GuardPharmacy;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class AdminGuardPharmacyController extends Controller
{
    public function store(Request $request, GuardSchedule $schedule): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required','string','max:150'],
            'district' => ['nullable','string','max:150'],
            'address'  => ['nullable','string','max:255'],
            'phone'    => ['nullable','string','max:60'],
        ]);
        $data['guard_schedule_id'] = $schedule->id;
        GuardPharmacy::create($data);

        return back()->with('status','Pharmacie ajoutée.');
    }

    public function edit(GuardSchedule $schedule, GuardPharmacy $pharmacy)
    {
        return view('admin.guards.edit_pharmacy', compact('schedule','pharmacy'));
    }

    public function update(Request $request, GuardSchedule $schedule, GuardPharmacy $pharmacy): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required','string','max:150'],
            'district' => ['nullable','string','max:150'],
            'address'  => ['nullable','string','max:255'],
            'phone'    => ['nullable','string','max:60'],
        ]);
        $pharmacy->update($data);

        return redirect()->route('admin.guards.show', $schedule)->with('status','Pharmacie mise à jour.');
    }

    public function destroy(GuardSchedule $schedule, GuardPharmacy $pharmacy): RedirectResponse
    {
        $pharmacy->delete();
        return back()->with('status','Pharmacie supprimée.');
    }
}
