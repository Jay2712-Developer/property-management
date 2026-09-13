<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserHashidAndRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_generates_and_decodes_hashid(): void
    {
        $user = User::factory()->create([
            'email' => 'agent@tisha-property.test',
        ]);

        $this->assertNotEmpty($user->hashid);
        $this->assertIsString($user->hashid);

        $decodedId = User::decodeHashid($user->hashid);
        $this->assertEquals($user->id, $decodedId);

        // Test route model binding resolution
        $resolved = (new User)->resolveRouteBinding($user->hashid);
        $this->assertNotNull($resolved);
        $this->assertEquals($user->id, $resolved->id);
    }

    public function test_user_can_assign_spatie_role(): void
    {
        $role = Role::create(['name' => 'Agent', 'guard_name' => 'web']);
        $user = User::factory()->create();

        $user->assignRole($role);

        $this->assertTrue($user->hasRole('Agent'));
    }
}
