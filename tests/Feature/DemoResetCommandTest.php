<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class DemoResetCommandTest extends TestCase
{
    public function test_demo_reset_aborts_when_demo_mode_disabled(): void
    {
        config(['app.demo_mode' => false]);
        $exitCode = Artisan::call('demo:reset');
        $this->assertEquals(1, $exitCode);
    }

    public function test_demo_reset_runs_when_demo_mode_enabled(): void
    {
        config(['app.demo_mode' => true]);
        $exitCode = Artisan::call('demo:reset');
        $this->assertEquals(0, $exitCode);
        $this->assertDatabaseHas('asset_categories', ['name' => 'Industrial Sewing Machine']);
        $this->assertDatabaseHas('users', ['email' => 'worker@demo.local']);
    }
}
