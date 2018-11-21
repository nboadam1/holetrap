@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <strong>Reportes</strong>
                    @can('reporte.create')
                        <a href="{{ route('reports.create') }}" class="btn btn-sm btn-primary float-right">Agregar</a>
                    @endcan
                </div>

                <div class="card-body">
                    <table class="table table-striped table-hover text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Titulo</th>
                                <th>Latitud</th>
                                <th>Logitud</th>
                                <th colspan="3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportes as $report)
                            <tr>
                                <td>{{$report->id}}</td>
                                <td>{{$report->titulo}}</td>
                                <td>{{$report->latitud}}</td>
                                <td>{{$report->longitud}}</td>
                                <td width="5%;">
                                    @can('reporte.show')
                                        <span><a href="{{ route('reports.show',$report->id) }}" class="btn btn-sm btn-info pull-right">V</a></span>
                                    @endcan
                                </td>
                                <td width="5%;">
                                    @can('reporte.edit')
                                        <span><a href="{{ route('reports.edit',$report->id) }}" class="btn btn-sm btn-warning pull-right">E</a></span>
                                    @endcan
                                </td>
                                <td width="5%;">
                                    @can('reporte.destroy')
                                        {!! Form::open(['route' => ['reports.destroy',$report->id],'method' => 'DELETE' ]) !!}
                                            <button type="submit" class="btn btn-sm btn-danger pull-right">E</button>
                                        {!! Form::close() !!}
                                        
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $reportes->render() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
