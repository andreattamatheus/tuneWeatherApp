<?php

use App\Jobs\DeleteUserLocationAfterDaysJob;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('cleanup job soft deletes locations older than three days and keeps recent locations', function () {
    $now = now();
    $user = User::factory()->create();
    $oldLocation = Location::factory()->create([
        'user_id' => $user->id,
        'created_at' => $now->copy()->subDays(4),
    ]);
    $recentLocation = Location::factory()->create([
        'user_id' => $user->id,
        'created_at' => $now->copy()->subDays(3),
    ]);

    (new DeleteUserLocationAfterDaysJob)->handle();

    expect($oldLocation->fresh()->trashed())->toBeTrue()
        ->and($recentLocation->fresh()->trashed())->toBeFalse();
});
