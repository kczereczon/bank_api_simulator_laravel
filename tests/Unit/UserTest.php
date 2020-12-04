<?php

namespace Tests\Unit;

use App\Models\BankingAccount;
use App\Models\User;
use App\Services\BankService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCreateUserWithoutInit()
    {
        $userFake = User::factory()->make()->toArray();

        $request = $this->json('POST', '/api/user', $userFake);
        $request->assertStatus(500);
    }

    public function testInitBank()
    {
        $bankService = new BankService();
        $generalUser = $bankService->initBank(500000);

        $this->assertInstanceOf(User::class, $generalUser);
    }

    public function testCreateUserWithInit()
    {
        $bankService = new BankService();
        $generalUser = $bankService->initBank(500000);

        $userFake = User::factory()->make()->toArray();

        $request = $this->json('POST', '/api/user', $userFake);
        $request->assertStatus(200);
    }

    public function testLogin()
    {
        $userFake = User::factory()->create();

        $request = $this->json('POST', '/api/user/login', ['email' => $userFake->email]);
        $request->assertStatus(200);
    }

    public function testLoginFailed()
    {
        $request = $this->json('POST', '/api/user/login', ['email' => 'test@test.test']);
        $request->assertStatus(404);
    }

    public function testGetUserData()
    {
        $userFake = User::factory()->create();

        $request = $this->json('GET', '/api/user/' . $userFake->id);
        $request->assertStatus(200);
    }

    public function testGetUserNotExistingData()
    {
        $request = $this->json('GET', '/api/user/' . 999);
        $request->assertStatus(404);
    }
}
