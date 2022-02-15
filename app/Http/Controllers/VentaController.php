<?php

namespace App\Http\Controllers;

use App\Enums\TipoServicio;
use App\Http\Requests\CrearVentaRequest;
use App\Models\DetalleVenta;
use App\Models\Servicio;
use App\Models\Vehiculo;
use App\Models\Venta;
use App\Tools\ApiMessage;
use BenSampo\Enum\Enum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VentaController extends Controller
{

    public function index(Request $request)
    {
        $respuesta = new ApiMessage($request);
        $lista = Venta::all();
        $respuesta->setData($lista);
        return $respuesta->send();
    }


    public function store(CrearVentaRequest $request)
    {
        $datos = $request->validated();
        $user = Auth::user();

        $venta_info = [
            'vehiculo_id' => $request->vehiculo_id,
            'monto' => 0,
            'user_id' => $user->id
        ];

        $respuesta = new ApiMessage();

        $vehiculo = Vehiculo::find($venta_info['vehiculo_id']);
        if(!$vehiculo)
        {
            return $respuesta->setCode(404)->setMessage("No se encuentra vehiculo")->send();//arreglar los codigos de error
        }

        try {
            DB::beginTransaction();
            $venta = Venta::create($venta_info);
            if(!$venta)
            {
                return $respuesta->setCode(409)->setMessage("Ocurrio un error")->send();
            }
            foreach ($datos['detalle'] as $detalle) {

                try {
                    $this->_guardarDetalle($detalle, $venta->id,$vehiculo->color);
                }
                catch (\Exception $e) {
                    return $respuesta->setCode(400)->setMessage($e->getMessage())->send();
                }
            }
            DB::commit();

        }
        catch (\Exception $e)
        {
            DB::rollBack();
            $respuesta->setCode(400)->setMessage("Ocurrio un error".$e->getMessage())->send();
        }

        $venta->refresh();
        $ventaDetalle = Venta::find($venta->id);

        $respuesta->setMessage("Venta generada con exito")->setData($ventaDetalle);
        Log::info("Venta generada con exito");
        return $respuesta->send();
    }


    public function show($id)
    {
        $ventas = Venta::find($id);

        $respuesta = new ApiMessage();

        if(!$ventas)
        {
            return $respuesta->setCode(404)->setMessage("No se encuentra la venta")->send();
        }

        return $ventas;
    }

    public function _guardarDetalle($params, $idventa,$vehiculo)
    {

        $detallesVentas = new DetalleVenta($params);
        $respuesta = new ApiMessage();

        $venta = Venta::find($idventa);
        $servicio = Servicio::where('tipo', $params['servicio_id'])->first();



        try {
            if(!$servicio)
            {
                throw new \Exception('Id de servicio erroneo');
            }

            if (strcmp($vehiculo, "GRIS") == 0 && $params['servicio_id'] == TipoServicio::PINTURA) {
                throw new \Exception('El servicio no esta disponible para autos de color Gris');
            }

            $total = $venta->monto + $servicio->precio;

            $detallesVentas->venta_id = $idventa;
            $detallesVentas->saveOrFail();

            $venta->monto = $total;
            $venta->saveOrFail();


            return true;
        } catch (\PDOException $e) {
            DB::rollBack();
            Log::Error('No fue posible registrar el detalle de la venta: ' . $idventa . '. Error: ' . $e->getMessage());
            throw new \Exception('No fue posible registrar el detalle de la venta: ' . $idventa . '. Error: ' . $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::Error('No se pudo completar la venta. '. $e->getMessage());
            throw new \Exception('No se pudo completar la venta. '. $e->getMessage());
        }

    }
}
