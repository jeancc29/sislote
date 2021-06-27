<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salesdetails extends Model
{
    protected $fillable = [
        'idLoteria', 'idLoteriaSuperpale', 'idSorteo', 'premio', 'monto', 'jugada', 'status', 'idVenta', 'comision', 'pagado', 'idStock'
    ];

    public function sorteo()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Draws', 'id', 'idSorteo');
    }

    public static function loterias()
    {
        //Modelo, foreign key, local key
        return $this->hasMany('App\Lotteries', 'id', 'idLoteria');
    }

    public static function customAll($servidor, $idVenta){
        return \DB::connection($servidor)->select("
            SELECT
                s.id,
                s.idVenta,
                s.idLoteria,
                s.idSorteo,
                d.descripcion sorteoDescripcion,
                s.jugada,
                s.monto,
                s.premio,
                s.comision,
                s.idStock,
                s.idLoteriaSuperpale,
                s.created_at,
                s.updated_at,
                s.status,
                (SELECT JSON_OBJECT(
                    'id', l.id,
                    'abreviatura', l.abreviatura,
                    'descripcion', l.descripcion
                )) loteria,
                (SELECT JSON_OBJECT(
                    'id', d.id,
                    'descripcion', d.descripcion
                )) sorteo,
                (SELECT IF(
                    ls.id IS NULL,
                    NULL,
                    JSON_OBJECT(
                        'id', ls.id,
                        'abreviatura', ls.abreviatura,
                        'descripcion', ls.descripcion
                    )
                )
                ) loteriaSuperpale
            FROM salesdetails s
            INNER JOIN lotteries l on l.id = s.idLoteria
            INNER JOIN draws d on d.id = s.idSorteo
            LEFT JOIN lotteries ls on ls.id = s.idLoteriaSuperpale
            WHERE s.idVenta = $idVenta
        ");
        
    }
}
