<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Models\Film;
use App\Repository\FilmRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class FilmControllerTest extends TestCase
{
    use DatabaseMigrations;

    protected $adminUser;
    protected $mockRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['is_admin' => true]);
        $this->mockRepository = $this->mock(FilmRepositoryInterface::class);
        $this->app->instance(FilmRepositoryInterface::class, $this->mockRepository);
        $this->actingAs($this->adminUser);
    }

    public function testAdminCanCreateFilm()
    {
        $this->mockRepository->shouldReceive('checkIfAdmin')->andReturn(true);
        $this->mockRepository->shouldReceive('create')->andReturn(new Film());

        $response = $this->postJson('/createfilm', [
            'title' => 'New Film',
            'description' => 'A great movie',
            'release_year' => 2021,
            'rating' => 'PG',
            'language_id' => 1
        ]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Film created successfully']);
    }

    public function testNonAdminCannotCreateFilm()
    {
        $nonAdmin = User::factory()->create(['is_admin' => false]);
        $this->actingAs($nonAdmin);
        $this->mockRepository->shouldReceive('checkIfAdmin')->andReturn(false);

        $response = $this->postJson('/createfilm', []);

        $response->assertStatus(403);
    }

    public function testCreateFilmValidationFailed()
    {
        $this->mockRepository->shouldReceive('checkIfAdmin')->andReturn(true);

        $response = $this->postJson('/createfilm', []); 

        $response->assertStatus(422);
    }

    public function testShowFilmNotFound()
    {
        $this->mockRepository->shouldReceive('getById')->with(999)->andReturn(null);

        $response = $this->getJson('/users/999');

        $response->assertStatus(404);
    }

    public function testUpdateFilmNotFound()
    {
        $this->actingAs($this->adminUser);
        $this->mockRepository->shouldReceive('checkIfAdmin')->andReturn(true);
        $this->mockRepository->shouldReceive('getById')->andReturn(null);

        $response = $this->putJson('/updatefilm/999', []);

        $response->assertStatus(404);
    }

    public function testUpdateFilmSuccess()
    {
        $film = new Film(['id' => 1, 'title' => 'Original Title']);
        $this->mockRepository->shouldReceive('checkIfAdmin')->andReturn(true);
        $this->mockRepository->shouldReceive('getById')->andReturn($film);
        $this->mockRepository->shouldReceive('update')->andReturn(true);

        $response = $this->putJson('/updatefilm/1', [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'release_year' => 2022
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Film updated successfully']);
    }

    public function testDeleteFilmSuccess()
    {
        $this->actingAs($this->adminUser);
        $film = new Film(['id' => 1]);
        $this->mockRepository->shouldReceive('checkIfAdmin')->andReturn(true);
        $this->mockRepository->shouldReceive('getById')->andReturn($film);
        $this->mockRepository->shouldReceive('delete')->once()->andReturn(true);

        $response = $this->deleteJson('/deletefilm/1');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Film deleted successfully']);
    }

    public function testDeleteFilmNotFound()
    {
        $this->actingAs($this->adminUser);
        $this->mockRepository->shouldReceive('checkIfAdmin')->andReturn(true);
        $this->mockRepository->shouldReceive('getById')->andReturn(null);

        $response = $this->deleteJson('/deletefilm/999');

        $response->assertStatus(404);
    }
    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}