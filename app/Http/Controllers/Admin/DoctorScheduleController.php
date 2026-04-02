<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    public const POLI_LIST = [
        '🩺 Spesialis Anestesiologi',
        '🩺 Spesialis Bedah',
        '🩺 Spesialis Bedah Mulut',
        '🩺 Spesialis Bedah Plastik, Rekonstruksi, dan Estetik',
        '🩺 Spesialis Forensik dan Medikolegal',
        '🩺 Spesialis Kesehatan Jiwa',
        '🩺 Spesialis Kedokteran Gigi Anak',
        '🩺 Spesialis Radiologi',
        '🩺 Spesialis Mata',
        '🩺 Spesialis Neurologi',
        '🩺 Spesialis Ortopedi dan Traumatologi',
        '🩺 Spesialis Konservasi Gigi',
        '🩺 Spesialis Telinga, Hidung, dan Tenggorok',
        '🩺 Spesialis Prostodontia',
        '🩺 Spesialis Gizi Klinis',
        '🩺 Spesialis Obstetri dan Ginekologi',
    ];

    public const DAYS = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    public function index()
    {
        $query = DoctorSchedule::query();

        if (request('q')) {
            $q = request('q');
            $query->where('doctor_name', 'like', "%$q%")
                  ->orWhere('poli', 'like', "%$q%");
        }

        if (request('poli')) {
            $query->where('poli', request('poli'));
        }

        $schedules = $query->orderBy('poli')->orderBy('doctor_name')->paginate(20);
        $poliList = self::POLI_LIST;

        return view('admin.doctor-schedules.index', compact('schedules', 'poliList'));
    }

    public function create()
    {
        $poliList = self::POLI_LIST;
        $days = self::DAYS;
        $doctors = \App\Models\User::where('is_active', true)
            ->whereHas('role', function ($query) {
                $query->where('level', '>=', 2)
                    ->where('name', '!=', 'admin');
            })
            ->orderBy('name', 'asc')
            ->get();
            
        return view('admin.doctor-schedules.create', compact('poliList', 'days', 'doctors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_name' => 'required|string|max:255',
            'poli' => 'required|string|in:' . implode(',', self::POLI_LIST),
            'day' => 'required|array|min:1',
            'day.*' => 'string|in:' . implode(',', self::DAYS),
            'start_time' => 'required',
            'end_time' => 'required',
            'hospital' => 'required|in:alta,roxwood',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        DoctorSchedule::create([
            'doctor_name' => $validated['doctor_name'],
            'poli' => $validated['poli'],
            'day' => $validated['day'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'hospital' => $validated['hospital'],
            'is_active' => $request->boolean('is_active', true),
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('admin.doctor-schedules.index')->with('success', 'Jadwal dokter berhasil ditambahkan.');
    }

    public function edit(DoctorSchedule $doctorSchedule)
    {
        $poliList = self::POLI_LIST;
        $days = self::DAYS;
        $doctors = \App\Models\User::where('is_active', true)
            ->whereHas('role', function ($query) {
                $query->where('level', '>=', 2)
                    ->where('name', '!=', 'admin');
            })
            ->orderBy('name', 'asc')
            ->get();
            
        return view('admin.doctor-schedules.edit', compact('doctorSchedule', 'poliList', 'days', 'doctors'));
    }

    public function update(Request $request, DoctorSchedule $doctorSchedule)
    {
        $validated = $request->validate([
            'doctor_name' => 'required|string|max:255',
            'poli' => 'required|string|in:' . implode(',', self::POLI_LIST),
            'day' => 'required|array|min:1',
            'day.*' => 'string|in:' . implode(',', self::DAYS),
            'start_time' => 'required',
            'end_time' => 'required',
            'hospital' => 'required|in:alta,roxwood',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $doctorSchedule->update([
            'doctor_name' => $validated['doctor_name'],
            'poli' => $validated['poli'],
            'day' => $validated['day'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'hospital' => $validated['hospital'],
            'is_active' => $request->boolean('is_active', true),
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('admin.doctor-schedules.index')->with('success', 'Jadwal dokter berhasil diperbarui.');
    }

    public function destroy(DoctorSchedule $doctorSchedule)
    {
        $doctorSchedule->delete();
        return redirect()->route('admin.doctor-schedules.index')->with('success', 'Jadwal dokter berhasil dihapus.');
    }

    public function toggleActive(DoctorSchedule $doctorSchedule)
    {
        $doctorSchedule->is_active = !$doctorSchedule->is_active;
        $doctorSchedule->save();
        return back()->with('success', 'Status jadwal diperbarui.');
    }
}
