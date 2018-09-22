<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// use DB;
// use Storage;
use Hash;
use Validator;

use App\User;

class APIController extends Controller {

  public function getLogin(Request $request) {

    $validator = Validator::make($request->all(), [
      'email' => 'required',
      'pass' => 'required',
    ]);

    if ($validator->fails()) {
      $res['error'] = $validator->messages()->first();
      $res['debug_info'] = $validator->messages();
      return response()->json($res, 406);
    }

    $login = $request->email;

    // OJO! Esta consulta ya ignora los usuarios que tienen rellenado el campo 'deleted_at' si queremos verlos todos hay que añadir ->withTrashed() a la consulta
    if (strpos($login, '@') !== false) {
      $user = User::where('email', $login)->first();
    } else {
      $user = User::where('clave', $login)->first();
    }

    if ($user) {

      // Comprobar que el pass que nos pasan coincida con los datos del usuario que pretenden loguear
      if (Hash::check($request->pass, $user->password)) {

        // Si el usuario no tiene api_token se lo generamos
        if (!$user->api_token) {
          $user->generateToken();
        }

        $status = 200;
        $data['user'] = $user;
        $data['msj'] = "Logueado con éxito, ¡Bienvenido!";

        // Aqui cargamos los datos comunes con los que ya tenemos en $data
        $res = $this->get_datos_comunes($data);

      } else { $status = 403; $res['error'] = 'Credenciales inválidas'; }
    } else { $status = 401; $res['error'] = 'No existe / Borrado / Credenciales inválidas'; }

    return response()->json($res, $status);
  }

  public function getUser(Request $request) {

    // OJO! Se ignoran los usuarios que tienen rellenado el campo 'deleted_at'
    // Si esta borrado devuelve "error": "Unauthenticated."
    $data['user'] = $request->user();
    $data['msj'] = "¡Bienvenido de nuevo!";

    // Aqui cargamos los datos comunes con los que ya tenemos en $data
    $res = $this->get_datos_comunes($data);

    return response()->json($res, 200);
  }

  // Construimos un array con los datos comunes para unificar getLogin y getUser
  private function get_datos_comunes($res) {

    $res['common_data'] = 'test';
    $res['common_data2'] = 'test2';

    return $res;
  }

}
