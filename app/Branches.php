<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branches extends Model
{
    protected $fillable = [
        'descripcion', 
        'ip', 
        'codigo', 
        'idUsuario',
        'idMoneda',
        'idGrupo',
        'dueno',
        'localidad',
        'balanceDesactivacion',
        'limiteVenta',
        'descontar',
        'deCada',
        'minutosCancelarTicket',
        'piepagina1',
        'piepagina2',
        'piepagina3',
        'piepagina4',
        'status',
        'imprimirCodigoQr'
    ];


    public function dias()
    {
        return $this->belongsToMany('App\Days', 'branches_days', 'idBanca', 'idDia')->withPivot('horaApertura','horaCierre');
    }

    public function loterias()
    {
        return $this->belongsToMany('App\Lotteries', 'branches_lotteries', 'idBanca', 'idLoteria');
    }

  

    public function usuario()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Users', 'id', 'idUsuario');
    }

    public function moneda()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Coins', 'id', 'idMoneda');
    }
    // public function pagosCombinaciones()
    // {
    //     //Modelo, foreign key, foreign key, local key, local key
    //     return $this->hasManyThrough('App\Payscombinations', 'App\Lotteries', 'id', 'id', 'idUsuario');
    // }

    public function pagosCombinaciones()
    {
        //Modelo, foreign key, foreign key, local key, local key
        return $this->hasMany('App\Payscombinations', 'idBanca');
    }

    public function gastos()
    {
        //Modelo, foreign key, foreign key, local key, local key
        return $this->hasMany('App\Automaticexpenses', 'idBanca');
    }

    public function comisiones()
    {
        //Modelo, foreign key, foreign key, local key, local key
        return $this->hasMany('App\Commissions', 'idBanca');
    }

    public function loteriaExiste($idLoteria){
        if($this->loterias()->wherePivot('idLoteria', $idLoteria)->first() != null)
            return true;
        else
            return false;
    }

    public function loteriaExisteYTienePagoCombinaciones($idLoteria, $idSorteo){
        if($this->loterias()->wherePivot('idLoteria', $idLoteria)->first() != null){
            $d = $this->pagosCombinaciones()->where('idLoteria', $idLoteria)->first();
            if($d != null){
                if($idSorteo == 1){
                    if((int)$d['primera'] == 0)
                        return false;
                    if((int)$d['segunda'] == 0)
                        return false;
                    if((int)$d['tercera'] == 0)
                        return false;
                }
                else if($idSorteo == 2){
                    if((int)$d['primeraSegunda'] == 0)
                        return false;
                    if((int)$d['primeraTercera'] == 0)
                        return false;
                    if((int)$d['segundaTercera'] == 0)
                        return false;
                }
                else if($idSorteo == 3){
                    if((int)$d['tresNumeros'] == 0)
                        return false;
                    if((int)$d['dosNumeros'] == 0)
                        return false;
                }
                else if($idSorteo == 4){
                    if((int)$d['primerPago'] == 0)
                        return false;
                }
            }else
                return false;
        }
        else
            return false;

        return true;
    }    

    public function cerrada(){
        $cerrado = false;
        $fecha = getdate();
        $hora = explode(':',$this->dias()->whereWday($fecha['wday'])->first()->pivot->horaCierre);
        if((int)$fecha['hours'] > (int)$hora[0])
            $cerrado = true;
        else if((int)$hora[0] == (int)$fecha['hours']){
            //Validamos si los minutos actuales son mayores que los minutos horaCierre  
            if((int)$fecha['minutes'] > (int)$hora[1])
                $cerrado = true;
        }

        return $cerrado;
    }


    public function abierta(){
        $abierta = false;
        $fecha = getdate();
        $hora = explode(':',$this->dias()->whereWday($fecha['wday'])->first()->pivot->horaApertura);
        if((int)$fecha['hours'] > (int)$hora[0])
            $abierta = true;
        else if((int)$hora[0] == (int)$fecha['hours']){
            //Validamos si los minutos actuales son mayores que los minutos horaCierre  
            if((int)$fecha['minutes'] > (int)$hora[1])
                $abierta = true;
        }

        return $abierta;
    }

    public function limiteVenta($servidor, $monto_a_vender){
        $abierta = false;
        $fecha = getdate();
        
       $ventas = Sales::on($servidor)->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
            ->where('idBanca', $this->id)
            ->whereNotIn('status', [0,5])->sum('total');

        $ventas += $monto_a_vender;

        return ($ventas > $this->limiteVenta);
    }
   
    public static function customAll($servidor){
        return \DB::connection($servidor)->select("
            select 
                b.id,
                b.descripcion,
                b.codigo,
                b.dueno,
                b.status,
                JSON_OBJECT('id', u.id, 'usuario', u.usuario, 'nombres', u.nombres) usuario,
                JSON_OBJECT('id', c.id, 'descripcion', c.descripcion, 'abreviatura', c.abreviatura, 'color', c.color) monedaObject
            FROM branches b 
            INNER JOIN users u ON u.id = b.idUsuario
            INNER JOIN coins c ON c.id = b.idMoneda
            WHERE b.status != 2
        ");
    }


    public static function customFirst($servidor, $id){
        $data = \DB::connection($servidor)->select("
            select 
                b.id,
                b.descripcion,
                b.codigo,
                b.dueno,
                b.localidad,
                b.status,
                JSON_OBJECT('id', u.id, 'usuario', u.usuario, 'nombres', u.nombres) usuario,
                JSON_OBJECT('id', g.id, 'descripcion', g.descripcion) grupo,
                JSON_OBJECT('id', c.id, 'descripcion', c.descripcion, 'abreviatura', c.abreviatura, 'color', c.color) monedaObject,
                b.limiteVenta,
                b.balanceDesactivacion,
                b.descontar,
                b.deCada,
                b.minutosCancelarTicket,
                b.piepagina1,
                b.piepagina2,
                b.piepagina3,
                b.piepagina4,
                (
                    SELECT
                        JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'id', c.id,
                                'idLoteria', c.idLoteria,
                                'directo', c.directo,
                                'pale', c.pale,
                                'tripleta', c.tripleta,
                                'superPale', c.superPale,
                                'pick3Straight', c.pick3Straight,
                                'pick3Box', c.pick3Box,
                                'pick4Straight', c.pick4Straight,
                                'pick4Box', c.pick4Box
                            )
                    )
                    FROM commissions c 
                    INNER JOIN lotteries l ON l.id = c.idLoteria
                    WHERE c.idBanca = b.id
                ) AS comisiones,
                (
                    SELECT
                        JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'id', p.id,
                                'idLoteria', p.idLoteria,
                                'primera', p.primera,
                                'segunda', p.segunda,
                                'tercera', p.tercera,
                                'primeraSegunda', p.primeraSegunda,
                                'primeraTercera', p.primeraTercera,
                                'segundaTercera', p.segundaTercera,
                                'tresNumeros', p.tresNumeros,
                                'dosNumeros', p.dosNumeros,
                                'primerPago', p.primerPago,
                                'pick3TodosEnSecuencia', p.pick3TodosEnSecuencia,
                                'pick33Way', p.pick33Way,
                                'pick36Way', p.pick36Way,
                                'pick4TodosEnSecuencia', p.pick4TodosEnSecuencia,
                                'pick44Way', p.pick44Way,
                                'pick46Way', p.pick46Way,
                                'pick412Way', p.pick412Way,
                                'pick424Way', p.pick424Way
                            )
                    )
                    FROM payscombinations p
                    INNER JOIN lotteries l ON l.id = p.idLoteria
                    WHERE p.idBanca = b.id
                ) AS pagosCombinaciones,
                (
                    SELECT
                        JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'id', l.id,
                                'descripcion', l.descripcion,
                                'abreviatura', l.abreviatura
                            )
                    )
                    FROM branches_lotteries bl
                    INNER JOIN lotteries l ON l.id = bl.idLoteria
                    WHERE bl.idBanca = b.id
                ) AS loterias,
                (
                    SELECT
                        JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'id', a.id,
                                'descripcion', a.descripcion,
                                'monto', a.monto,
                                'created_at', a.created_at,
                                'frecuencia', (SELECT JSON_OBJECT('id', f.id, 'descripcion', f.descripcion)),
                                'dia', (SELECT JSON_OBJECT('id', d.id, 'descripcion', d.descripcion))
                            )
                    )
                    FROM Automaticexpenses a
                    INNER JOIN frecuencies f ON f.id = a.idFrecuencia
                    LEFT JOIN days d ON d.id = a.idDia
                    WHERE a.idBanca = b.id
                ) AS gastos,
                (
                    SELECT
                        JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'id', d.id,
                                'descripcion', d.descripcion,
                                'wday', d.wday,
                                'created_at', d.created_at,
                                'horaApertura', bd.horaApertura,
                                'horaCierre', bd.horaCierre
                            )
                    )
                    FROM days d
                    INNER JOIN branches_days bd ON bd.idDia = d.id
                    WHERE bd.idBanca = b.id
                ) AS dias,
                (select sum(sales.total) from sales where date(created_at) = date(now()) and status not in(0, 5) and sales.idBanca = b.id) as ventasDelDia
            FROM branches b 
            INNER JOIN users u ON u.id = b.idUsuario
            INNER JOIN coins c ON c.id = b.idMoneda
            LEFT JOIN $servidor.groups g ON g.id = b.idGrupo
            WHERE b.status != 2 AND b.id = $id
        ");

        return count($data) > 0 ? $data[0] : null;
    }

    public static function getFirstBanca($usuario){
        return Branches::on($usuario->getConnectionName())->where("status", "!=", 2)->first();
    }

    public static function getFirstBancaOfHisGroup($usuario){
        $grupo = $usuario->group;
        if($grupo == null)
            return null;

        $banca = Branches::on($usuario->getConnectionName())->where("idGrupo", $grupo->id)->where("status", "!=", 2)->first();
        if($banca == null)
            abort(404, "No hay bancas registradas en su grupo");

        return $banca;
    }

    public static function getBancasOfHisGroupOrAll($usuario){
        $grupo = $usuario->group;
        if($grupo == null){
            $data = Branches::on($usuario->getConnectionName())->where("status", "!=", 2)->get();
            if(count($data) == 0)
                abort(404, "No hay bancas registradas");
            
            return $data;
        }

        $data = Branches::on($usuario->getConnectionName())->where("idGrupo", $grupo->id)->where("status", "!=", 2)->get();
        if(count($data) == 0)
            abort(404, "No hay bancas registradas en su grupo");

        return $data;
    }

    public static function search($servidor, $data){
        return \DB::connection($servidor)->select("
            SELECT
                b.id,
                b.descripcion,
                b.codigo,
                b.dueno,
                b.status,
                JSON_OBJECT('id', u.id, 'usuario', u.usuario, 'nombres', u.nombres) usuario,
                JSON_OBJECT('id', c.id, 'descripcion', c.descripcion, 'abreviatura', c.abreviatura, 'color', c.color) monedaObject
            FROM branches b
            INNER JOIN users u ON u.id = b.idUsuario
            INNER JOIN coins c ON c.id = b.idMoneda
            WHERE 
                b.status != 2
                AND (b.descripcion LIKE '%{$data}%' OR b.codigo LIKE '%{$data}%')
        ");
    }

}
