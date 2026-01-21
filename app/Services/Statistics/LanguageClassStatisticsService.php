<?php

namespace App\Services\Statistics;

use App\Models\LanguageClass;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LanguageClassStatisticsService
{
    /**
     * Dashboard overview
     * * @return array{
     *     total_classes: int,
     *     total_professors: int,
     *     total_students: int
     * }
     */
    public function overview(?string $from = null, ?string $to = null) : array
    {
        $query = LanguageClass::query()
            ->when($from, fn($q) => $q->whereDate('schedule_time', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('schedule_time', '<=', $to));

        return [
            'total_classes' => (clone $query)->count(),

            'total_professors' => (clone $query)
                ->distinct('professor_id')
                ->count('professor_id'),

            'total_students' => (clone $query)
                ->withCount('students')
                ->get()
                ->sum('students_count'),
        ];
    }

    /**
     * Classes grouped by period (daily, weekly, monthly)
     */
    public function classesByPeriod(array $filters = [], string $period = 'daily'): Collection
    {
        $query = LanguageClass::query();

        // Filter po status časova
        if (($filters['status'] ?? null) !== null) {
            $query->where('status', $filters['status']);
        }

        // Filter po profesoru
        if (($filters['professor_id'] ?? null) !== null) {
            $query->where('professor_id', $filters['professor_id']);
        }

        // Filter po studentu (pivot tabela)
        if (($filters['student_id'] ?? null) !== null) {
            $query->whereHas('students', fn($q) => $q->where('user_id', $filters['student_id']));
        }

        // Filter po periodu (from/to)
        if (($filters['from'] ?? null) !== null) {
            $query->where('schedule_time', '>=', Carbon::parse($filters['from']));
        }
        if (($filters['to'] ?? null) !== null) {
            $query->where('schedule_time', '<=', Carbon::parse($filters['to']));
        }

        // Dohvati filtrirane časove
        $classes = $query->get();

        // Grupisanje po periodu
        return $classes->groupBy(fn($class) => match ($period) {
            'weekly' => Carbon::parse($class->schedule_time)->format('Y-W'),
            'monthly' => Carbon::parse($class->schedule_time)->format('Y-m'),
            default => Carbon::parse($class->schedule_time)->format('Y-m-d'),
        })
            ->map(fn($items, $key) => [
                'period' => $key,
                'total' => $items->count(),
            ])
            ->values();
    }

    /**
     * Statistics per professor
     */
    public function professorStatistics(array $filters = []): Collection
    {
        $query = User::query()->where('role', 'professor');

        if (($filters['professor_id'] ?? null) !== null) {
            $query->where('id', $filters['professor_id']);
        }

        $professors = $query->withCount('taughtClasses')->get();

        return $professors->map(function ($professor) use ($filters) {
            $classes = $professor->taughtClasses;

            if (($filters['status'] ?? null) !== null) {
                $classes = $classes->where('status', $filters['status']);
            }

            if (($filters['from'] ?? null) !== null) {
                $from = Carbon::parse($filters['from']);
                $classes = $classes->filter(fn($c) => Carbon::parse($c->schedule_time) >= $from);
            }
            if (($filters['to'] ?? null) !== null) {
                $to = Carbon::parse($filters['to']);
                $classes = $classes->filter(fn($c) => Carbon::parse($c->schedule_time) <= $to);
            }

            return [
                'professor_id' => $professor->id,
                'name' => $professor->name,
                'total_classes' => $classes->count(),
                'total_students' => $classes->sum(fn($c) => $c->students()->count()),
            ];
        });
    }

    public function studentStatistics(array $filters = []): Collection
    {
        $query = User::query()->where('role', 'student');

        if (($filters['student_id'] ?? null) !== null) {
            $query->where('id', $filters['student_id']);
        }

        $students = $query->with('enrolledClasses')->get();

        return $students->map(function ($student) use ($filters) {
            $classes = $student->enrolledClasses;

            if (($filters['status'] ?? null) !== null) {
                $classes = $classes->where('status', $filters['status']);
            }

            if (($filters['from'] ?? null) !== null) {
                $from = Carbon::parse($filters['from']);
                $classes = $classes->filter(fn($c) => Carbon::parse($c->schedule_time) >= $from);
            }
            if (($filters['to'] ?? null) !== null) {
                $to = Carbon::parse($filters['to']);
                $classes = $classes->filter(fn($c) => Carbon::parse($c->schedule_time) <= $to);
            }

            return [
                'student_id' => $student->id,
                'name' => $student->name,
                'total_classes' => $classes->count(),
                'total_professors' => $classes->pluck('professor_id')->unique()->count(),
            ];
        });
    }
}
