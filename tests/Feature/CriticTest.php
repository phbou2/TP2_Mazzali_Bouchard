<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Critic;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class CriticTest extends TestCase
{
    use DatabaseMigrations;
    
    protected $user;
    
    public function testValidationFailure()
    {
        $response = $this->postJson('/createCritics', [
            'film_id' => null,
            'note' => 6,
            'comment' => ''
        ]);

        $response->assertStatus(INVALID_DATA);
    }

    public function testPreventDuplicateCritics()
    {
        Critic::create([
            'film_id' => 1,
            'user_id' => $this->user->id,
            'note' => 4,
            'comment' => 'Great movie!'
        ]);

        $response = $this->postJson('/createCritics', [
            'film_id' => 1,
            'note' => 5,
            'comment' => 'Amazing!'
        ]);

        $response->assertStatus(FORBIDDEN);
        $response->assertJson(['error' => 'Vous avez déjà critiqué ce film']);
    }

    public function testCreateCriticSuccess()
    {
        $response = $this->postJson('/createCritics', [
            'film_id' => 2,
            'note' => 3,
            'comment' => 'Decent movie'
        ]);

        $response->assertStatus(CREATED);
        $response->assertJsonStructure([
            'film_id',
            'user_id',
            'note',
            'comment'
        ]);
        $this->assertDatabaseHas('critics', ['film_id' => 2, 'user_id' => $this->user->id]);
    }

    public function testCreateCriticWithoutAuthentication()
    {
        Sanctum::actingAs(null);
        $response = $this->postJson('/createCritics', [
            'film_id' => 2,
            'note' => 3,
            'comment' => 'Interesting perspective'
        ]);

        $response->assertStatus(UNAUTHORIZED);
    }

}