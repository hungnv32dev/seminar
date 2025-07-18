<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_created_workshops_relationship()
    {
        $user = User::factory()->create();
        $workshop = Workshop::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->createdWorkshops);
        $this->assertTrue($user->createdWorkshops->contains($workshop));
        $this->assertEquals(1, $user->createdWorkshops->count());
    }

    /** @test */
    public function it_has_organized_workshops_relationship()
    {
        $user = User::factory()->create();
        $workshop = Workshop::factory()->create();
        
        $user->organizedWorkshops()->attach($workshop->id);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->organizedWorkshops);
        $this->assertTrue($user->organizedWorkshops->contains($workshop));
        $this->assertEquals(1, $user->organizedWorkshops->count());
    }

    /** @test */
    public function it_can_organize_multiple_workshops()
    {
        $user = User::factory()->create();
        $workshops = Workshop::factory()->count(3)->create();
        
        $user->organizedWorkshops()->attach($workshops->pluck('id'));

        $this->assertEquals(3, $user->organizedWorkshops->count());
        foreach ($workshops as $workshop) {
            $this->assertTrue($user->organizedWorkshops->contains($workshop));
        }
    }

    /** @test */
    public function it_can_create_multiple_workshops()
    {
        $user = User::factory()->create();
        $workshops = Workshop::factory()->count(3)->create(['created_by' => $user->id]);

        $this->assertEquals(3, $user->createdWorkshops->count());
        foreach ($workshops as $workshop) {
            $this->assertTrue($user->createdWorkshops->contains($workshop));
        }
    }

    /** @test */
    public function active_scope_returns_only_active_users()
    {
        User::factory()->create(['is_active' => true]);
        User::factory()->create(['is_active' => false]);
        User::factory()->create(['is_active' => true]);

        $activeUsers = User::active()->get();

        $this->assertEquals(2, $activeUsers->count());
        foreach ($activeUsers as $user) {
            $this->assertTrue($user->is_active);
        }
    }

    /** @test */
    public function inactive_scope_returns_only_inactive_users()
    {
        User::factory()->create(['is_active' => true]);
        User::factory()->create(['is_active' => false]);
        User::factory()->create(['is_active' => false]);

        $inactiveUsers = User::inactive()->get();

        $this->assertEquals(2, $inactiveUsers->count());
        foreach ($inactiveUsers as $user) {
            $this->assertFalse($user->is_active);
        }
    }

    /** @test */
    public function it_casts_is_active_to_boolean()
    {
        $user = User::factory()->create(['is_active' => 1]);

        $this->assertIsBool($user->is_active);
        $this->assertTrue($user->is_active);

        $user = User::factory()->create(['is_active' => 0]);
        $this->assertIsBool($user->is_active);
        $this->assertFalse($user->is_active);
    }
}