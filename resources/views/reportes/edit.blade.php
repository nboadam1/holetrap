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
                    {!! Form::model($report,['route' => ['reports.update',$report->id],'method' => 'PUT']) !!}
                        @include('reportes.partials.form')
                    {!! Form::close()  !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection