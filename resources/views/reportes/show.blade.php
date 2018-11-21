@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                   Reporte
                </div>

                <div class="card-body">
                    <p><strong>Titulo: </strong> {{$report->titulo}}</p>
                    <p><strong>Descripcion: </strong> {{$report->descripcion}}</p>
                    <p><strong>Latitud: </strong> {{$report->latitud}}</p>
                    <p><strong>Longitud: </strong> {{$report->longitud}}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection