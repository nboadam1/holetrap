@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <strong>Usuarios</strong>
                </div>

                <div class="card-body">
                    <table class="table table-striped table-hover text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th colspan="3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{$user->id}}</td>
                                <td>{{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                <td width="5%;">
                                    @can('users.show')
                                        <span><a href="{{ route('users.show',$user->id) }}" class="btn btn-sm btn-info pull-right">V</a></span>
                                    @endcan
                                </td>
                                <td width="5%;">
                                    @can('users.edit')
                                        <span><a href="{{ route('users.edit',$user->id) }}" class="btn btn-sm btn-warning pull-right">E</a></span>
                                    @endcan
                                </td>
                                <td width="5%;">
                                    @can('users.destroy')
                                        {!! Form::open(['route' => ['users.destroy',$user->id],'method' => 'DELETE' ]) !!}
                                            <button type="submit" class="btn btn-sm btn-danger pull-right">E</button>
                                        {!! Form::close() !!}
                                        
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $users->render() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
