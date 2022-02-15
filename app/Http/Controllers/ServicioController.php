<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrearServicioRequest;
use App\Models\Servicio;
use App\Tools\ApiMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServicioController extends Controller
{

    public function index(Request $request)
    {
        $respuesta = new ApiMessage($request);
        $lista = Servicio::all();
        $respuesta->setData($lista);
        return $respuesta->send();
    }


    public function store(CrearServicioRequest $request)
    {
        $servicio = Servicio::create($request->all());

        $respuesta = new ApiMessage();

        if(!$servicio)
        {
            Log::error("No se pudo crear el servicio");
            return $respuesta->setCode(409)->setMessage("Ocurrio un error")->send();
        }

        $respuesta->setMessage("Servicio generado con exito")->setData($servicio);
        Log::info("Servicio generado con exito");
        return $respuesta->send();
    }


}
