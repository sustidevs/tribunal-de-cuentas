<?php

namespace App\Http\Controllers\Api;

use App\Models\Area;
use App\Models\User;
use App\Models\Persona;
use App\Models\SubArea;
use App\Models\TipoUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::index();
        return response()->json($users,200);
    }
    
    public function create()
    {
        //$data = Collect([]);
        //$data->push(["tipo_users" => TipoUser::get_tipo_users(),"areas" => Area::all_areas()]);
        $data = ["tipo_users" =>TipoUser::get_tipo_users(),"areas" => Area::all_areas()];
        return response()->json($data,200);
    }

    public function store(Request $request)
    {
        /*
        $request = new Request;
        $request->dni = '12345677';
        $request->cuil = '2123456779';
        $request->area_id = '1';
        $request->tipo_area = 'Area';
        $request->nombre = 'Juan';
        $request->apellido = 'Apellido';
        $request->telefono = '3794111111';
        $request->email = 'user@ejemplo.com';
        $request->direccion = 'Una Calle 123';
        $request->password = 'password';
        $request->comfirm_password = 'password';
        $request->tipo_user = '1';
        */
        
        $persona = New Persona();
        $persona->dni = $request->dni;
        $persona->nombre = $request->nombre;
        $persona->apellido = $request->apellido;
        $persona->telefono = $request->telefono;
        $persona->email = $request->email;
        $persona->direccion = $request->direccion;
    
        if ($persona->save()){
            $user = New User();
            if ($request->tipo_area == "Area") 
            {
                $area = Area::findOrFail($request->area_id);
                $area_type = Area::class;
            } else 
            {
                $area = SubArea::findOrFail($request->area_id);
                $area_type = SubArea::class;
            }
            
            $user->area_id = $area->id;
            $user->area_type = $area_type;
            $user->persona_id = $persona->id;
            $user->tipo_user_id = $request->tipo_user;
            $user->cuil = $request->cuil;
            $user->password = Hash::make($request->password);
            //$user->assignRole(Role::find($request->role_id)->name);
            
            if($user->save()){
                return response()->json($user->datos_user(),200);
            }
        }
        return response()->json([],200);
    }

    /**
     * Retorna los datos del usuario a mostrar para su edición.
     *
     */
    public function edit(Request $request)
    {
            /*
            $request = new Request;
            $request->id = '23';
            */
            $user = User::findOrFail($request->id);
        
            $data = ["tipo_users" => TipoUser::get_tipo_users(),
                     "areas" => Area::all_areas(),
                     "user" => [$user->datos_user()]];
        return response()->json($data,200);
    }

    public function update(Request $request)
    {
        /*
        $request = new Request;
        $request->id = '23';
        $request->dni = '12345677';
        $request->cuil = '2123456779';
        $request->area_id = '1';
        $request->tipo_area = 'Area';
        $request->tipo_user_id = '2';
        $request->nombre = 'Juan';
        $request->apellido = 'Fernandez';
        $request->telefono = '3794111111';
        $request->email = 'user@ejemplo.com';
        $request->direccion = 'Una Calle 123';
        */
        $user = User::findOrFail($request->id);
        $persona = Persona::findOrFail($user->persona->id);

        
        $persona->dni = $request->dni;
        $persona->nombre = $request->nombre;
        $persona->apellido = $request->apellido;
        $persona->telefono = $request->telefono;
        $persona->email = $request->email;
        $persona->direccion = $request->direccion;
        
        $persona->update();
        
        if ($request->tipo_area == "Area") 
        {
            $area = Area::findOrFail($request->area_id);
            $area_type = Area::class;
        } else 
        {
            $area = SubArea::findOrFail($request->area_id);
            $area_type = SubArea::class;
        }
        
        $user->area_id = $area->id;
        $user->area_type = $area_type;
        $user->tipo_user_id = $request->tipo_user_id;
        $user->cuil = $request->cuil;
        if ($request->password != null) {
            $user->password = Hash::make($request->password);
        }
        
        if($user->update()){
            return response()->json($user->datos_user(),200);
        }

        return response()->json([],200);
    }

    /**
     * Da de baja a un usuario.
     *
     */
    public function delete(Request $request)
    {
        /*    
        $request = new Request;
        $request->id = '23';
        */
        $user = User::findOrFail($request->id);
        $user->delete();

        return response()->json($user->datos_user(),200);
    }

    /**
     * Da de alta un usuario que fue dado de baja.
     *
     */
    public function restore(Request $request)
    {
        /*
        $request = new Request;
        $request->id = '23';
        */
        $user = User::withTrashed()->findOrFail($request->id);;    
        $user->restore();

        return response()->json($user->datos_user(),200);
    }
}