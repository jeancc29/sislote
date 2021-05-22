<?php
// vendor/bin/phpunit --filter test_indexAdd_loan
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportesTest extends TestCase
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

    public function test_jugadas_reportes()
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
            "servidor" => "valentin",
            "status" => 1,
            // "tipo" => \App\Classes\Helper::stdClassToArray($tipo),
        ];
        $jwt = \App\Classes\Helper::jwtEncoder($data);
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        $response = $this->post(route('reporte.jugadas'), [
            "datos" => $jwt
        ]);
        $array = \App\Classes\Helper::stdClassToArray($response->getData());
        $json = json_encode($array);
        echo $json;

        $response->assertStatus(200);
        // $response->assertSessionHasErrors('email');
    }
    public function test_ventas_reportes()
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
            "fecha" => "2021-04-18 00:00",
            "fechaFinal" => "2021-04-19 23:59:59",
            "servidor" => "valentin",
            "idBanca" => 18,
            "status" => 1,
            "idUsuario" => 1,
            // "tipo" => \App\Classes\Helper::stdClassToArray($tipo),
        ];
        $jwt = \App\Classes\Helper::jwtEncoder($data);
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        $response = $this->post(route('reporte.ventas'), [
            "datos" => $jwt
        ]);
        $array = \App\Classes\Helper::stdClassToArray($response->getData());
        $json = json_encode($array);
        echo $json;

        $response->assertStatus(201);
        // $response->assertSessionHasErrors('email');
    }

    public function test_ventas_reportes()
    {
        $this->withoutExceptionHandling();
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        
        $data =  [
            "retornarLoterias" => false,
            "fechaDesde" => "2021-04-18 00:00",
            "fechaHasta" => "2021-04-19 23:59:59",
            "idBanca" => 18,
            "status" => 1,
            "idUsuario" => 1,
            // "tipo" => \App\Classes\Helper::stdClassToArray($tipo),
        ];
        // $jwt = \App\Classes\Helper::jwtEncoder($data);
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        $response = $this->post(route('historicoApi'), [
            "datos" => $jwt
        ]);
        $array = \App\Classes\Helper::stdClassToArray($response->getData());
        $json = json_encode($array);
        echo $json;

        $response->assertStatus(201);
        // $response->assertSessionHasErrors('email');
    }
}
