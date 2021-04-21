<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettingTest extends TestCase
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

    public function test_index_settings()
    {
        $this->withoutExceptionHandling();
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        
        $data =  [
            "loteria" => ["id" => 15],
            // "sorteo" => ["id" => 2],
            // "loteria" => null,
            // "sorteo" => null,
            "retornarSorteos" => false,
            "retornarLoterias" => false,
            "fechaInicial" => "2021-03-25 00:00",
            "fechaFinal" => "2021-04-25 23:59:59",
            "idUsuario" => 1,
            "servidor" => "valentin",
            "status" => 1,
            // "tipo" => \App\Classes\Helper::stdClassToArray($tipo),
        ];
        $jwt = \App\Classes\Helper::jwtEncoder($data);
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        $response = $this->post(route('settings.index'), [
            "datos" => $jwt
        ]);
        $array = \App\Classes\Helper::stdClassToArray($response->getData());
        $json = json_encode($array);
        echo $json;

        $response->assertStatus(200);
        // $response->assertSessionHasErrors('email');
    }

    public function test_store_settings()
    {
        $this->withoutExceptionHandling();
        $tipo = \App\Types::on("valentin")->whereRenglon("ticket")->first();
        
        $data =  [
            "usuario" => ["id" => 1],
            // "sorteo" => ["id" => 2],
            // "loteria" => null,
            // "sorteo" => null,
            "ajustes" => ["id" => null, 
                "consorcio" => "Consorcio Jean", 
                "imprimirNombreConsorcio" => 1,
                "tipoFormatoTicket" => ["id" => $tipo->id, "descripcion" => $tipo->descripcion]
            ],
            "servidor" => "valentin",
            "status" => 1,
            // "tipo" => \App\Classes\Helper::stdClassToArray($tipo),
        ];
        $jwt = \App\Classes\Helper::jwtEncoder($data);
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        $response = $this->post(route('settings.store'), [
            "datos" => $jwt
        ]);
        $array = \App\Classes\Helper::stdClassToArray($response->getData());
        $json = json_encode($array);
        echo $json;

        $response->assertStatus(200);
        // $response->assertSessionHasErrors('email');
    }
}
