<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = [
        'idMoneda',
        'consorcio',
        'imprimirNombreConsorcio',
        'idTipoFormatoTicket',
        'imprimirNombreBanca',
        'cancelarTicketWhatsapp',
        'pagarTicketEnCualquierBanca',
    ];

    public static function customFirst($servidor){
        $data = \DB::connection($servidor)->select("
            SELECT
            settings.*,
            (
                SELECT
                IF(
                    t.id IS NULL,
                    NULL,
                    JSON_OBJECT('id', t.id, 'descripcion', t.descripcion)
                )
            ) tipoFormatoTicket
            FROM settings
            LEFT JOIN types t on t.id = settings.idTipoFormatoTicket
            LIMIT 1
        ");

        return count($data) > 0 ? $data[0] : null;
    }

    public static function puedeCancelarTicketsPorWhatsapp($servidor){
        $ajuste = Settings::on($servidor)->first();
        if($ajuste == null)
            return false;

        if($ajuste->cancelarTicketWhatsapp == true)
            return true;
        
        return false;
    }

    public static function puedePagarTicketEnCualquierBanca($servidor){
        $ajuste = Settings::on($servidor)->first();
        if($ajuste == null)
            return false;

        if($ajuste->pagarTicketEnCualquierBanca == true)
            return true;
        
        return false;
    }
}
