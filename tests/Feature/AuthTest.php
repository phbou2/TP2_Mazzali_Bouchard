<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Response;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;


class AuthTest extends TestCase
{
    use DatabaseMigrations;
        
    public function testSignInThrottling()
    {
        $user = [
            "login" => "example_login2",
            "password" => "example_password2",
            "email" => "examplsadasde21@example.com",
            "last_name" => "ExampleLast",
            "first_name" => "ExampleFirst",
            "role_id" => 1 //For some reason if i put the constant here it cant find it but for the rest of the code it works
        ];

        $this->postJson('api/signup', $user);

        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('api/signin', [ 
                'login' => $user['login'],
                'password' => $user['password'], 
            ]);

            $response->assertStatus(OK); 
        }

        $response = $this->postJson('api/signin', [
            'login' => $user['login'],
            'password' => $user['password'],
        ]);

        $response->assertStatus(TOO_MANY_REQUESTS); 
    }

    public function testSignUpThrottling()
    {
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('api/signup', [
                'login' => 'test_user' . $i,
                'password' => 'password', 
                'email' => 'test_user' . $i . '@example.com', 
                'last_name' => 'Test',
                'first_name' => 'User' . $i,
                'role_id' => 1 //For some reason if i put the constant here it cant find it but for the rest of the code it works
            ]);

            $response->assertStatus(CREATED); 
        }

        
        $response = $this->postJson('api/signup', [ 
            'login' => 'test_user6',
            'password' => 'password',
            'email' => 'test_user6@example.com',
            'last_name' => 'Test',
            'first_name' => 'User6',
            'role_id' => 1 //For some reason if i put the constant here it cant find it but for the rest of the code it works
        ]);

        $response->assertStatus(TOO_MANY_REQUESTS);
    }

    public function testSignOutThrottling()
    {
        Sanctum::actingAs(User::factory()->create(), ['*']);

        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('api/signout');

            $response->assertStatus(NO_CONTENT); 
        }

        $response = $this->postJson('api/signout');

        $response->assertStatus(TOO_MANY_REQUESTS);
    }

    public function testSignIn()
    {
        $userData = [
            'login' => 'test_user',
            'password' => 'password',
            'email' => 'test_usadwadasdawer@example.com',
            'last_name' => 'Test',
            'first_name' => 'User',
            'role_id' => USER_ROLE_ID 
        ];

        $this->postJson('api/signup', $userData);

        $response = $this->postJson('api/signin', [
            'login' => $userData['login'],
            'password' => $userData['password'],
        ]);

        $response->assertStatus(OK)
            ->assertJsonStructure(['token']);
    }

    public function testSignUp()
    {
        $userData = [
            'login' => 'test_user',
            'password' => 'password',
            'email' => 'test_user@example.com',
            'last_name' => 'Test',
            'first_name' => 'User',
            'role_id' => USER_ROLE_ID 
        ];
        

        $response = $this->postJson('api/signup', $userData);

        $response->assertStatus(CREATED)
            ->assertJson(['message' => 'User registered succesfully']);
    }

    public function testSignOut()
    {
        Sanctum::actingAs(User::factory()->create(), ['*']);

        $response = $this->postJson('api/signout');

        $response->assertStatus(NO_CONTENT);
    }

    public function testSignInMissingAttribute()
    {
        $response = $this->postJson('api/signin', [
            'login' => 'example_login',
        ]);

        $response->assertStatus(INVALID_DATA);
    }

    public function testSignInInvalidCredentials()
    {
        $response = $this->postJson('api/signin', [
            'login' => 'invalid_login',
            'password' => 'invalid_password',
        ]);

        $response->assertStatus(INVALID_DATA);
    }

    public function testSignInInvalidPassword()
    {
        $user = [
            'login' => 'example_login',
            'password' => 'example_password',
            'email' => 'example@example.com',
            'last_name' => 'ExampleLast',
            'first_name' => 'ExampleFirst',
            'role_id' => USER_ROLE_ID 
        ];

        $this->postJson('api/signup', $user);

        $response = $this->postJson('api/signin', [
            'login' => $user['login'],
            'password' => 'invalid_password',
        ]);

        $response->assertStatus(INVALID_DATA);
    }

    public function testRegisterMissingCredentials()
    {
        $user = [
            'password' => 'example_password',
            'email' => 'example@example.com',
            'last_name' => 'ExampleLast',
            'first_name' => 'ExampleFirst',
            'role_id' => USER_ROLE_ID 
        ];

        $response = $this->postJson('api/signup', $user);

        $response->assertStatus(INVALID_DATA);
    }

    public function testRegisterInvalidEmailFormat()
    {
        $user = [
            'login' => 'example_login',
            'password' => 'example_password',
            'email' => 'invalid_email_format',
            'last_name' => 'ExampleLast',
            'first_name' => 'ExampleFirst',
            'role_id' => USER_ROLE_ID 
        ];

        $response = $this->postJson('api/signup', $user);

        $response->assertStatus(INVALID_DATA);
    }

    public function testRegisterExistingEmail()
    {
        $user1 = [
            'login' => 'example_login1',
            'password' => 'example_password',
            'email' => 'existing_email@example.com',
            'last_name' => 'ExampleLast',
            'first_name' => 'ExampleFirst',
            'role_id' => USER_ROLE_ID 
        ];

        $response = $this->postJson('api/signup', $user1);

        $response->assertStatus(CREATED)
            ->assertJson(['message' => 'User registered succesfully']);

        $user2 = [
            'login' => 'example_login2',
            'password' => 'example_password',
            'email' => 'existing_email@example.com',
            'last_name' => 'ExampleLast',
            'first_name' => 'ExampleFirst',
            'role_id' => USER_ROLE_ID 
        ];

        $response = $this->postJson('api/signup', $user2);

        $response->assertStatus(INVALID_DATA);
    }
}