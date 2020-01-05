<?php

namespace App\Http\Controllers\Api;

use App\Curso;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Cliente;


/**
 * @group Cliente
 *
 * APIs para el cliente
 */
class UserController extends Controller
{
    /**
     * Login
     *
     * Inicio de sesión de clientes
     * 
     * @bodyParam email string required email del usuario.
     * @bodyParam password string required contraseña del usuario.
     * 
     * @response {
     *       "status": 200,
     *       "message": "Usuario encontrado",
     *       "data": {
     *           "id": 1,
     *           "email": "imarvdt@gmail.com",
     *           "password": "$2y$10$k3rqGBNfkTBpf0ClZ/ZkGODRajtrmtvq9BlKLzbh0nQVnRj.2lv8i",
     *           "nombre": "Cesar Mejia",
     *           "apellido": "Cueva",
     *           "telefono": "987545621",
     *           "direccion": "Lince 233",
     *           "created_at": "2019-06-05 20:57:13",
     *           "updated_at": "2019-06-05 20:57:13",
     *           "deleted_at": null
     *       }
     * }
     * 
     * @response 500{
     *   "status": 500,
     *   "message": "Usuario/Contraseña incorrectos"
     * }
     */
    public function postLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|max:150',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 500,
                'message' => 'Debes de ingresar tu usuario y/o contraseña',
                'errors'  =>  $validator->errors(),
            ], 500);
        }

        $user = Cliente::where('email', '=', $request->input('email'))->first();

        if ($user) {
            if (Hash::check($request->input('password'), $user->password)) {
                return response()->json([
                    'status'  => 200,
                    'message' => 'Usuario encontrado',
                    'data'    => $user,
                ], 200);
            }
        }

        return response()->json([
            'status'  => 500,
            'message' => 'Usuario/Contraseña incorrectos',
        ], 500);

    }

    /**
     * Registro
     *
     * Registro de clientes
     * 
     * @bodyParam email string required Email del usuario.
     * @bodyParam nombre string required Nombres del cliente.
     * @bodyParam apellido string required Apellidos del cliente.
     * @bodyParam direccion string required Dirección del cliente.
     * @bodyParam telefono string required Teléfono del cliente.
     * @bodyParam password string required Contraseña del usuario.
     * 
     * @response {
     *       "status": 200,
     *       "message": "Cliente registrado",
     *       "data": {
     *           "id": 1,
     *           "email": "imarvdt@gmail.com",
     *           "password": "$2y$10$k3rqGBNfkTBpf0ClZ/ZkGODRajtrmtvq9BlKLzbh0nQVnRj.2lv8i",
     *           "nombre": "Cesar Mejia",
     *           "apellido": "Cueva",
     *           "telefono": "987545621",
     *           "direccion": "Lince 233",
     *           "created_at": "2019-06-05 20:57:13",
     *           "updated_at": "2019-06-05 20:57:13",
     *           "deleted_at": null
     *       }
     * }
     * 
     * @response 500{
     *   "status": 500,
     *   "message": "No se ha podido crear la cuenta"
     * }
     */
    public function postRegistro(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|max:150|unique:clientes',
            'nombre'     => 'required|max:50',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 500,
                'message' => 'Ha ocurrido un error inesperado',
                'errors'  => $validator->errors(),
            ], 500);
        }

        $cliente                =   new Cliente();
        $cliente->nombre        =   $request->input("nombre");
        $cliente->apellido      =   $request->input("apellido");
        $cliente->email         =   $request->input("email");
        $cliente->direccion     =   $request->input("direccion");
        $cliente->telefono      =   $request->input("telefono");
        $cliente->password      =   Hash::make($request->input("password"));

        if ($cliente->save()) {
            return response()->json([
                'status'  => 200,
                'message' => 'Cliente registrado',
                'data'    => $cliente,
            ], 200);
        }

        return response()->json([
            'status'  => 500,
            'message' => 'No se ha podido crear la cuenta',
        ], 500);

    }

    /**
     * Leer cliente
     *
     * Buscar cliente por su ID
     * 
     * @queryParam id int required ID del cliente.
     * 
     * @response {
     *       "status": 200,
     *       "message": "Cliente encontrado",
     *       "data": {
     *           "id": 1,
     *           "email": "imarvdt@gmail.com",
     *           "password": "$2y$10$k3rqGBNfkTBpf0ClZ/ZkGODRajtrmtvq9BlKLzbh0nQVnRj.2lv8i",
     *           "nombre": "Cesar Mejia",
     *           "apellido": "Cueva",
     *           "telefono": "987545621",
     *           "direccion": "Lince 233",
     *           "created_at": "2019-06-05 20:57:13",
     *           "updated_at": "2019-06-05 20:57:13",
     *           "deleted_at": null
     *       }
     * }
     * 
     * @response 500{
     *   "status": 500,
     *   "message": "Cliente no encontrado"
     * }
     * 
     */
    public function getUsuario($id)
    {
        $usuario = Cliente::find($id);
        if($usuario){
            return response()->json([
                'status'  => 200,
                'message' => 'Cliente encontrado',
                'data'    => $usuario,
            ], 200);
        }

        return response()->json([
            'status'  => 500,
            'message' => 'Cliente no encontrado',
        ], 500);

    }

    /**
     * Editar
     *
     * Edición de datos del cliente
     * 
     * @bodyParam id int required ID del cliente a editar.
     * @bodyParam nombre string required Nombres del cliente.
     * @bodyParam apellido string required Apellidos del cliente.
     * @bodyParam direccion string required Dirección del cliente.
     * @bodyParam telefono string required Teléfono del cliente.
     * @bodyParam password string required Contraseña del usuario.
     * 
     * @response {
     *       "status": 200,
     *       "message": "Cliente actualizado",
     *       "data": {
     *           "id": 1,
     *           "email": "imarvdt@gmail.com",
     *           "password": "$2y$10$k3rqGBNfkTBpf0ClZ/ZkGODRajtrmtvq9BlKLzbh0nQVnRj.2lv8i",
     *           "nombre": "Cesar Mejia",
     *           "apellido": "Cueva",
     *           "telefono": "987545621",
     *           "direccion": "Lince 233",
     *           "created_at": "2019-06-05 20:57:13",
     *           "updated_at": "2019-06-05 20:57:13",
     *           "deleted_at": null
     *       }
     * }
     * 
     * @response 500{
     *   "status": 500,
     *   "message": "Cliente no encontrado"
     * }
     * 
     */
    public function postEditar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'  => 'required|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 500,
                'message' => 'Ha ocurrido un error inesperado',
                'errors'  => $validator->errors(),
            ], 500);
        }

        $cliente        = Cliente::find($request->input("id"));
        if($cliente){
            $cliente->nombre        =   $request->input("nombre");
            $cliente->apellido      =   $request->input("apellido");
            $cliente->direccion     =   $request->input("direccion");
            $cliente->telefono      =   $request->input("telefono");
            $cliente->password      =   Hash::make($request->input("password"));
            if ($request->has("password")){
                $cliente->password = Hash::make($request->input("password"));
            }

            if ($cliente->save()) {
                return response()->json([
                    'status'  => 200,
                    'message' => 'Cliente actualizado',
                    'data'    => $cliente,
                ], 200);
            }
        }

        return response()->json([
            'status'  => 500,
            'message' => 'Cliente no encontrado',
        ], 500);
    }

}
