<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrearPersonaRequest;
use App\Models\Persona;
use App\Tools\ApiMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PersonaController extends Controller
{

    public function index(Request $request)
    {
        $respuesta = new ApiMessage($request);
        $lista = Persona::all();
        $respuesta->setData($lista);
        return $respuesta->send();
    }


    public function store(CrearPersonaRequest $request)
    {
        $persona = Persona::create($request->all());

        $respuesta = new ApiMessage();

        if(!$persona)
        {
            Log::error("No se pudo crear persona");
            return $respuesta->setCode(409)->setMessage("Ocurrio un error")->send();
        }

        $respuesta->setMessage("Persona generada con exito")->setData($persona);
        Log::info("Persona generada con exito");
        return $respuesta->send();
    }

}
