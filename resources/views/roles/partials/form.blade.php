<div class="form-group">
	{{ Form::label('name','Nombre del rol') }}
	{{ Form::text('name',null,['class' => 'form-control']) }}
</div>
<div class="form-group">
	{{ Form::label('slug','Url amigable') }}
	{{ Form::text('slug',null,['class' => 'form-control']) }}
</div>
<div class="form-group">
	{{ Form::label('description','Descripción') }}
	{{ Form::textarea('description',null,['class' => 'form-control']) }}
</div>
<hr>
<h3>Permiso especial</h3>
<div class="form-group">
	<label for="">{{ Form::radio('special','all-access') }} Acceso total</label>
	<label for="">{{ Form::radio('special','no-access') }} Ningun acceso</label>
</div>
<h3>Lista de roles</h3>
<div class="form-group">
	<ul class="list-unstyle">
		@foreach($permissions as $permission)
			<li>
				<label for="">
					{{ Form::checkbox('permissions[]',$permission->id,null) }}
					{{ $permission->name }}
					<em>({{ $permission->description ?: 'N/A' }})</em>
				</label>
			</li>
		@endforeach
	</ul>
</div>
<div class="form-group">
	{{ Form::submit('Guardar',['class' => 'btn btn-primary btn-sm']) }}
</div>