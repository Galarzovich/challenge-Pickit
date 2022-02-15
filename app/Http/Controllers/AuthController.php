<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\AltaUsuariosRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\UpdateUserRequest;
use App\Tools\ApiMessage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    public function signup(AltaUsuariosRequest $request)
    {
        $res = new ApiMessage();

        $params = $request->validated();
        $params['password'] = bcrypt($request->password);
        $user = new User($params);

        # Guardamos el usuasrio
        if (!$user->save()) {
            // error
            return $res
                ->setCode(409)
                ->setMessage("No fué posible registrar el usuario")
                ->send();
        }

        $res->message = "Usuario registrado correctamente.";
        # en data, devolvemos el id
        $res->data = [
            'id' => $user->id,
            'name'=>$user->name
        ];

        return $res->send();
    }

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Generar nuevo token de acceso",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="user",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 example={"user": "usernick", "password": "secretPassword"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Devuelve un array con datos."
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        $res = new ApiMessage();

        $login = $request->input('user');
        // Comprobar si el input coincide con el formato de E-mail
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'dni';

        // $credentials = request(['user', 'password']);
        $credentials = [
            $field => $login,
            'password' => $request->input('password')
        ];

        if (!Auth::attempt($credentials)) {
            return $res->setCode(401)->setMessage("El usuario y/o contraseña son incorrectos")->send();

        }
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse(
                $tokenResult->token->expires_at)
                ->toDateTimeString(),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/auth/logout",
     *     description="Revocar token",
     *     @OA\Response(
     *          response="200",
     *     description="OK"
     * )
     *
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' =>
            'Successfully logged out']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }


}
