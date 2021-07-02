<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function test_users_search()
    {
        $this->withoutExceptionHandling();
        $tipo = \App\Types::on("valentin")->whereRenglon("ticket")->first();
        
        $data =  [
            "search" => "je",
            "servidor" => "valentin",
        ];
        $jwt = \App\Classes\Helper::jwtEncoder($data);
        $response = $this->post(route('users.search'), [
            "datos" => $jwt
        ]);
        $array = \App\Classes\Helper::stdClassToArray($response->getData());
        $json = json_encode($array);
        echo $json;

        $response->assertStatus(200);
        // $response->assertSessionHasErrors('email');
    }
}
