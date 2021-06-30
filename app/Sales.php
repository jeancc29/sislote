<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $fillable = [
        'id', 'idUsuario', 'idBanca', 'total', 'descuentoMonto', 'descuentoPorcentaje', 'hayDescuento', 'subTotal', 'idLoteria', 'idTicket', 'compartido'
    ];


    public function usuario()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Users', 'id', 'idUsuario');
    }

    public function banca()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Branches', 'id', 'idBanca');
    }

    public function ticket()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Tickets', 'id', 'idTicket');
    }

    public function cancelacion()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Cancellations', 'id', 'idTicket');
    }

    public static function customFirst($servidor, $id){
        $data = \DB::connection($servidor)->select("
            SELECT
                s.id,
                s.compartido,
                s.idUsuario,
                s.idBanca,
                s.total,
                s.subTotal,
                s.descuentoMonto,
                s.hayDescuento,
                s.idTicket,
                s.created_at,
                s.updated_at,
                s.status,
                (SELECT JSON_OBJECT(
                    'id', b.id,
                    'codigo', b.codigo,
                    'descripcion', b.descripcion,
                    'piepagina1', b.piepagina1,
                    'piepagina2', b.piepagina2,
                    'piepagina3', b.piepagina3,
                    'piepagina4', b.piepagina4,
                    'imprimirCodigoQr', b.imprimirCodigoQr
                )) banca,
                (SELECT JSON_OBJECT(
                    'id', u.id,
                    'usuario', u.usuario,
                    'nombres', u.nombres,
                    'apellidos', u.apellidos
                )) usuario,
                (SELECT JSON_OBJECT(
                    'id', t.id,
                    'codigoBarra', t.codigoBarra,
                    'uuid', t.uuid
                )) ticket
            FROM sales s
            INNER JOIN branches b on b.id = s.idBanca
            INNER JOIN users u on u.id = s.idUsuario
            INNER JOIN tickets t on t.id = s.idTicket
            WHERE s.id = $id
        ");
        
        return count($data) > 0 ? $data[0] : null;
    }
}
