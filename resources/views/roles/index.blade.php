@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <strong>Roles</strong>
                    @can('rolee.create')
                        <a href="{{ route('roles.create') }}" class="btn btn-sm btn-primary float-right">Agregar</a>
                    @endcan
                </div>

                <div class="card-body">
                    <table class="table table-striped table-hover text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Rol</th>
                                <th colspan="3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                            <tr>
                                <td>{{$role->id}}</td>
                                <td>{{$role->name}}</td>
                                <td width="5%;">
                                    @can('roles.show')
                                        <span><a href="{{ route('roles.show',$role->id) }}" class="btn btn-sm btn-info pull-right">V</a></span>
                                    @endcan
                                </td>
                                <td width="5%;">
                                    @can('roles.edit')
                                        <span><a href="{{ route('roles.edit',$role->id) }}" class="btn btn-sm btn-warning pull-right">E</a></span>
                                    @endcan
                                </td>
                                <td width="5%;">
                                    @can('roles.destroy')
                                        {!! Form::open(['route' => ['roles.destroy',$role->id],'method' => 'DELETE' ]) !!}
                                            <button type="submit" class="btn btn-sm btn-danger pull-right">E</button>
                                        {!! Form::close() !!}
                                        
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $roles->render() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
