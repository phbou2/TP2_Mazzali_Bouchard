<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    private $user;


    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::create([
            "login" => "example_login2",
            "password" => bcrypt('example_password2'),
            "email" => "examplsadasde21@example.com",
            "last_name" => "ExampleLast",
            "first_name" => "ExampleFirst",
            "role" => 1
        ]);
    }

    public function testShowSuccess()
    {
        $this->actingAs($this->user)
             ->getJson('/user/' . $this->user->id)
             ->assertStatus(OK)
             ->assertJson(['data' => $this->user->toArray()]);
    }

    public function testUserNotFound()
    {
        $nonExistingUserId = 999;
        $this->actingAs($this->user)
             ->getJson('/user/' . $nonExistingUserId)
             ->assertStatus(NOT_FOUND)
             ->assertJson(['error' => 'User not found']);
    }

    public function testServerErrorOnShow()
    {
        User::shouldReceive('find')->andThrow(new \Exception('Server Error'));
        $this->actingAs($this->user)
             ->getJson('/user/' . $this->user->id)
             ->assertStatus(SERVER_ERROR)
             ->assertJson(['error' => 'Server Error']);
    }

    public function testUnauthorizedAccessToShow()
    {
        $unauthorizedUser = User::create([
            "login" => "another_login",
            "password" => bcrypt('another_password'),
            "email" => "another@example.com",
            "last_name" => "AnotherLast",
            "first_name" => "AnotherFirst",
            "role" => 2  
        ]);
        $this->actingAs($unauthorizedUser)
             ->getJson('/user/' . $this->user->id) 
             ->assertStatus(FORBIDDEN);
    }

    public function testSuccessfulPasswordUpdate()
    {
        $passwordData = [
            'password' => 'example_password2',
            'new_password' => 'newSecurePassword123',
            'confirm_password' => 'newSecurePassword123'
        ];

        $this->actingAs($this->user)
             ->putJson('/updateUsers/' . $this->user->id, $passwordData) 
             ->assertStatus(CREATED) 
             ->assertJson(['message' => 'Password changed']);
        $this->assertTrue(Hash::check($passwordData['new_password'], $this->user->fresh()->password));
    }

    public function testPasswordUpdateFailsWhenPasswordsDoNotMatch()
    {
        $passwordData = [
            'password' => 'example_password2',
            'new_password' => 'newSecurePassword123',
            'confirm_password' => 'differentPassword'
        ];

        $this->actingAs($this->user)
             ->putJson('/updateUsers/' . $this->user->id, $passwordData) 
             ->assertStatus(INVALID_DATA)
             ->assertJsonValidationErrors(['confirm_password']);
    }

    public function testPasswordUpdateFailsWithIncorrectCurrentPassword()
    {
        $passwordData = [
            'password' => 'wrongPassword',
            'new_password' => 'newSecurePassword123',
            'confirm_password' => 'newSecurePassword123'
        ];

        $this->actingAs($this->user)
             ->putJson('/updateUsers/' . $this->user->id, $passwordData) 
             ->assertStatus(FORBIDDEN)
             ->assertJson(['error' => 'Current password is incorrect']);
    }
}