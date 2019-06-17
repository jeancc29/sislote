@extends('header')

@section('content')


<div class="row justify-content-center" ng-init="prueba('{{ session('idUsuario')}}')">
    <div class="col-6 text-center">
            <h1>Hola prueba http</h1>
        <button ng-click="obtener()" class="btn btn-primary">
        Obtener
        </button>
    </div>
</div>

@endsection