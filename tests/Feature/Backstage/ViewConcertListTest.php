<?php

namespace Tests\Feature\Backstage;

use App\Models\Concert;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ViewConcertListTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function guests_cannot_view_a_promoters_concert_list()
    {
        $response = $this->get('/backstage/concerts');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    function promoters_can_view_a_list_of_their_concerts()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $concerts = Concert::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/backstage/concerts');

        $response->assertStatus(200);
        $this->assertTrue($response->original->getData()['concerts']->contains($concerts[0]));
        $this->assertTrue($response->original->getData()['concerts']->contains($concerts[1]));
        $this->assertTrue($response->original->getData()['concerts']->contains($concerts[2]));
    }
}
