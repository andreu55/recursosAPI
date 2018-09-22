<?php

namespace App\Http\Controllers;

use Storage;
use Validator;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;

// use App\Libraries\ReconocimientoClient;
// use App\Libraries\CatastroClient;
use App\Http\Controllers\clases_catastro\ReferenciaCatastro;


class CatastroController extends Controller {

  /**
  * Create a new controller instance.
  *
  * @return void
  */
  public function __construct() {
    $this->middleware('auth');
  }

  public function direccionDesdeCatastro(Request $request) {

    $validator = Validator::make($request->all(), [
      'rc' => 'required',
    ]);

    if ($validator->fails()) {
      $res['error'] = $validator->messages()->first();
      $res['debug_info'] = $validator->messages();
      return response()->json($res, 406);
    }

    $endPoint = 'http://ovc.catastro.meh.es/ovcservweb/OVCSWLocalizacionRC/OVCCallejero.asmx/';

    $client = new Client([
      'base_uri' => $endPoint,
      'headers' => [
        'Content-Type' => 'text/xml; charset=utf-8',
      ]
    ]);

    try {

      $response = $client->get('Consulta_DNPRC?Provincia=&Municipio=&RC='.$request->rc);

    } catch (ClientException $e) {

      // $response = $e->getResponse()->getBody()->getContents();

      return response()->json([
        'state' => 'ko',
        'title' => 'Error',
        'text' => 'Catastro no disponible.'
      ]);
    }

    // Sacamos el estado de la respuesta tanto si ha ido bien como si no (200, 401, 406...)
    $status_code = $response->getStatusCode();

    if ($status_code == 200) {
      $body = $response->getBody()->getContents();

      $res = new \SimpleXMLElement($body);

      print_r($res); exit();

      if (!isset($res->control->cudnp)) { // número de devoluciones
        return response()->json([
          'state' => 'ko',
          'title' => 'Error',
          'text' => 'No hemos obtenido ninguna coincidencia',
        ]);
      }

      $tipoDocumento = 'none';
      $coincidencias = [];

      if ($res->control->cudnp == 1) {

        $coincidencias[] = new ReferenciaCatastro($res->bico, true);

      } else {
        foreach ($res->lrcdnp->rcdnp as $coincidencia) {
          $coincidencias[] = new ReferenciaCatastro($coincidencia);
        }
      }
      return response()->json([
        'state' => 200,
        'tipoDocumento' => $tipoDocumento,
        'salida' => $coincidencias
      ]);
    }

  }

}
