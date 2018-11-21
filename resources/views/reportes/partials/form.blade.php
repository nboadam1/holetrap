<div class="form-group">
	{{ Form::label('titulo','Nombre del reporte') }}
	{{ Form::text('titulo',null,['class' => 'form-control']) }}
</div>
<div class="form-group">
	{{ Form::label('descripcion','Descripción') }}
	{{ Form::text('descripcion',null,['class' => 'form-control']) }}
</div>
<div class="form-group">
	{{ Form::label('latitud','Latitud') }}
	{{ Form::text('latitud',null,['class' => 'form-control']) }}
</div>
<div class="form-group">
	{{ Form::label('longitud','Longitud') }}
	{{ Form::text('longitud',null,['class' => 'form-control']) }}
</div>
<div class="form-group">
	{{ Form::submit('Guardar',['class' => 'btn btn-primary btn-sm']) }}
</div>