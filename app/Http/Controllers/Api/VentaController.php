<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Carrito;
use Illuminate\Support\Facades\Validator;
use App\Tarjeta;
use App\Direccion;
use App\VentaDetalle;
use App\Venta;

/**
 * @group Ventas
 *
 * API para ventas
 */
class VentaController extends Controller
{

    /**
     * Agregar
     *
     * Crear nueva venta
     *
     * @bodyParam cliente_id int required Id del cliente
     * @bodyParam tarjeta_id int required Id de la tarjeta
     * @bodyParam total decimal required Precio total de venta realizada
     */
    public function postAgregar(Request $request){
        $validator = Validator::make($request->all(),[
            "cliente_id" => "required",
        ]);

        if($validator->fails()){
            return response()->json([
                "status" => 500,
                "message" => "Ha ocurrido un error de validación",
                "errors" => $validator->errors(),
            ], 500);
        }


        $venta = new Venta();
        $venta->cliente_id = $request->input("cliente_id");
        $venta->tarjeta_id = $request->input("tarjeta_id");
        $tarjeta = Tarjeta::find($request->input("tarjeta_id"));
        $venta->tarjeta = $tarjeta->numero_final;
        $direccion = Direccion::find($request->input("direccion_id"));
        $venta->direccion = $direccion->direccion;

        if($venta->save()){

            $carrito = Carrito::where("cliente_id",$request->input("cliente_id"))->get();
            foreach($carrito as $c){
                $ventadetalle = new VentaDetalle();
                $ventadetalle->venta_id = $venta->id;
                $ventadetalle->producto_id = $c->producto_id;
                $ventadetalle->cantidad = $c->cantidad;
                $ventadetalle->precio = $c->precio;
                $ventadetalle->save();

                $c->delete();
            }

            return response()->json([
                "status" => 200,
                "message" => "Venta finalizada satisfactoriamente",
            ],200);

        }

        return response()->json([
            "status" => 500,
            "message" => "Ha ocurrido un error interno",
        ], 500);
    }

    /**
     * Modificar estado
     *
     * Modificar estado de una venta
     *
     * @bodyParam id int required Id de la venta
     * @bodyParam estado string required Nombre del estado (Comprado,Enviado,Entregado)
     */
    public function postModificarEstado(Request $request){
        $validator = Validator::make($request->all(),[
            "id" => "required",
        ]);

        if($validator->fails()){
            return response()->json([
                "status" => 500,
                "message" => "Ha ocurrido un error de validación",
                "errors" => $validator->errors(),
            ], 500);
        }


        $venta = Venta::find($request->input("id"));
        $venta->estado = $request->input("estado");

        if($venta->save()){

            return response()->json([
                "status" => 200,
                "message" => "Venta modificada satisfactoriamente",
            ],200);

        }

        return response()->json([
            "status" => 500,
            "message" => "Ha ocurrido un error interno",
        ], 500);
    }

    /**
     * Listar
     *
     * Listar las ventas de un cliente
     *
     * @queryParam cliente_id int required Id del cliente
     */
    public function getListar(Request $request){
        $ventas = Venta::where("cliente_id",$request->input("cliente_id"))->get();
        return response()->json([
            "status" => 200,
            "message" => "Lista",
            "data" => $ventas,
        ],200);
    }

    /**
     * Detalle
     *
     * Detalle de una venta
     *
     * @queryParam id int required Id de la venta
     */
    public function getDetalle($id){
        $venta = Venta::where("id",$id)->with("detalle")->first();
        if($venta){
            return response()->json([
                "status" => 200,
                "message" => "Venta encontrada",
                "data" => $venta,
            ],200);
        }

        return response()->json([
            "status" => 500,
            "message" => "Venta no encontrada",
        ],500);
    }

}
