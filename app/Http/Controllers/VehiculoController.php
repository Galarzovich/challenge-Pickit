<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrearVehiculoRequest;
use App\Http\Requests\EditarVehiculoRequest;
use App\Models\DetalleVenta;
use App\Models\Persona;
use App\Models\Servicio;
use App\Models\Vehiculo;
use App\Models\Venta;
use App\Tools\ApiMessage;
use BenSampo\Enum\Enum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VehiculoController extends Controller
{

    public function index(Request $request)
    {
        $respuesta = new ApiMessage($request);
        $lista = Vehiculo::all();
        $respuesta->setData($lista);
        return $respuesta->send();
    }


    public function store(CrearVehiculoRequest $request)
    {
        $respuesta = new ApiMessage();
        $params = [
            'marca' => $request->marca,
            'modelo' => $request->modelo,
            'año' => $request->año,
            'patente' => $request->patente,
            'color' => strtoupper($request->color),
            'persona_id' => $request->persona_id,
        ];
        //$persona = Persona::where('id',$params[$])
        $vehiculo = new Vehiculo($params);
        $vehiculo->save();

        if(!$vehiculo)
        {
            Log::error("Ocurrio un error en la carga del vehiculo");
            return $respuesta->setCode(409)->setMessage("Ocurrio un error")->send();
        }

        $respuesta->setMessage("Vehiculo generado con exito")->setData($vehiculo);
        Log::info("Vehiculo generado con exito");
        return $respuesta->send();
    }


    public function update(EditarVehiculoRequest $request, $id)
    {
        $vehiculo = Vehiculo::find($id);

        $respuesta = new ApiMessage();
        if(!$vehiculo)
        {
            return $respuesta->setCode(404)->setMessage("No se encuentra el vehiculo")->send();
        }

        $datos= $request->validated();
        $vehiculo->fill($datos);

        if($vehiculo->save())
        {
            $respuesta->setMessage("Vehiculo actualizado con exito")->setData($vehiculo);
            Log::info("Vehiculo actualizado con exito");
            return $respuesta->send();
        }
    }


    public function destroy($id)
    {
        $vehiculo = Vehiculo::find($id);

        $respuesta = new ApiMessage();

        if(!$vehiculo)
        {
            return $respuesta->setCode(404)->setMessage("No se encuentra el vehiculo")->send();
        }

        try {
            if ($vehiculo->delete()) {
                $respuesta->setMessage("Vehiculo eliminado con exito");
                Log::info("Vehiculo eliminado con exito");
                return $respuesta->send();
            }
        } catch (\Exception $e) {
            return $respuesta->setCode(400)->setMessage("El vehiculo no puede ser eliminada debido a que esta siendo utilizado. Por favor, revise sus ventas.")->send();
        }
    }

    public function getServicios($id)
    {
        $sql = "select s.* from servicio s
                    inner join detalleventa vd ON vd.servicio_id = s.id
                    inner join venta v ON v.id = vd.venta_id
                    WHERE v.vehiculo_id = '$id' ";

        $result = DB::connection('mysql')->select(DB::raw($sql));

        return $result;

    }
}
