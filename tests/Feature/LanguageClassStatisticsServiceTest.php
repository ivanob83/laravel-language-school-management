<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LanguageClass;
use App\Services\Statistics\LanguageClassStatisticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LanguageClassStatisticsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LanguageClassStatisticsService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(LanguageClassStatisticsService::class);
    }

    #[Test]
    public function it_returns_overview_statistics()
    {
        $professor1 = User::factory()->professor()->create();
        $professor2 = User::factory()->professor()->create();

        $student1 = User::factory()->student()->create();
        $student2 = User::factory()->student()->create();

        $class1 = LanguageClass::factory()->create([
            'professor_id' => $professor1->id,
            'schedule_time' => now(),
        ]);

        $class2 = LanguageClass::factory()->create([
            'professor_id' => $professor2->id,
            'schedule_time' => now(),
        ]);

        $class1->students()->attach([$student1->id, $student2->id]);
        $class2->students()->attach([$student1->id]);

        $stats = $this->service->overview();

        $this->assertEquals(2, $stats['total_classes']);
        $this->assertEquals(2, $stats['total_professors']);
        $this->assertEquals(3, $stats['total_students']);
    }

    #[Test]
    public function it_groups_classes_by_day()
    {
        LanguageClass::factory()->create(['schedule_time' => now()]);
        LanguageClass::factory()->create(['schedule_time' => now()]);
        LanguageClass::factory()->create(['schedule_time' => now()->subDay()]);

        $stats = $this->service->classesByPeriod('daily');

        $this->assertCount(2, $stats);
        $this->assertEquals(2, $stats->firstWhere('period', now()->format('Y-m-d'))['total']);
    }

    #[Test]
    public function it_returns_statistics_per_professor()
    {
        $professor = User::factory()->professor()->create();
        $student = User::factory()->student()->create();



        $class = LanguageClass::factory()
            ->forProfessor($professor)
            ->create();

        $class->students()->attach($student->id);

        $stats = $this->service->professorStatistics();

        $this->assertCount(1, $stats);

        $this->assertEquals([
            'professor_id' => $professor->id,
            'name' => $professor->name,
            'total_classes' => 1,
            'total_students' => 1,
        ], $stats->first());
    }

    #[Test]
    public function it_returns_statistics_per_student()
    {
        $professor = User::factory()->professor()->create();

        $student = User::factory()->student()->create();

        LanguageClass::factory()
            ->forProfessor($professor)
            ->forStudent($student)
            ->create();

        LanguageClass::factory()
            ->forProfessor($professor)
            ->forStudent($student)
            ->create();

        $stats = $this->service->studentStatistics();

        $this->assertCount(1, $stats);

        $stat = $stats[0];

        $this->assertEquals($student->id, $stat['student_id']);
        $this->assertEquals($student->name, $stat['name']);
        $this->assertEquals(2, $stat['total_classes']);
        $this->assertEquals(1, $stat['total_professors']);
    }
}
