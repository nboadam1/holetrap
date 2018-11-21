<?php

use Illuminate\Database\Seeder;
use Caffeinated\Shinobi\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	//Usuarios
        Permission::create([
        	'name' => 'Navegar usuarios',
        	'slug' => 'users.index',
        	'description' => 'Lista y navega todos los usuarios del sistema.'
        ]);
        Permission::create([
        	'name' => 'Ver detalle usuario',
        	'slug' => 'users.show',
        	'description' => 'Ver detalle usuario del sistema.'
        ]);
        Permission::create([
        	'name' => 'Edicion de usuario',
        	'slug' => 'users.edit',
        	'description' => 'Editar usuario del sistema.'
        ]);
        Permission::create([
        	'name' => 'Eliminar usuario',
        	'slug' => 'users.destroy',
        	'description' => 'Eliminar usuario del sistema.'
        ]);

        //Roles
        Permission::create([
        	'name' => 'Navegar roles',
        	'slug' => 'roles.index',
        	'description' => 'Lista y navega todos los roles del sistema.'
        ]);
        Permission::create([
        	'name' => 'Ver detalle rol',
        	'slug' => 'roles.show',
        	'description' => 'Ver detalle rol del sistema.'
        ]);
        Permission::create([
        	'name' => 'Creacion de roles',
        	'slug' => 'roles.create',
        	'description' => 'Editar roles del sistema.'
        ]);
        Permission::create([
        	'name' => 'Edicion de roles',
        	'slug' => 'roles.edit',
        	'description' => 'Editar reporte del sistema.'
        ]);
        Permission::create([
        	'name' => 'Eliminar roles',
        	'slug' => 'roles.destroy',
        	'description' => 'Eliminar roles del sistema.'
        ]);

        //Reporte mapa
        Permission::create([
        	'name' => 'Navegar reportes',
        	'slug' => 'reporte.index',
        	'description' => 'Lista y navega todos los reportes del sistema.'
        ]);
        Permission::create([
        	'name' => 'Ver detalle reporte',
        	'slug' => 'reporte.show',
        	'description' => 'Ver detalle repote del sistema.'
        ]);
        Permission::create([
        	'name' => 'Creacion de reportes',
        	'slug' => 'reporte.create',
        	'description' => 'Editar reporte del sistema.'
        ]);
        Permission::create([
        	'name' => 'Edicion de reportes',
        	'slug' => 'reporte.edit',
        	'description' => 'Editar reportes del sistema.'
        ]);
        Permission::create([
        	'name' => 'Eliminar reportes',
        	'slug' => 'reporte.destroy',
        	'description' => 'Eliminar reportes del sistema.'
        ]);

    }
}
