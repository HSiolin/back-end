<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    public function acessar(Request $request)
    {
        $user = UserProfile::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->senha, $user->senha)) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário ou senha inválidos'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'email' => $user->email
        ]);
    }

    public function registrar(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'dt_nascimento' => 'required|date',
                'senha' => 'required|min:6'
            ]);

            $idade = Carbon::parse($request->dt_nascimento)->age;

            if ($idade < 18) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário deve ser maior de 18 anos'
                ], 400);
            }

            if (UserProfile::where('email', $request->email)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'E-mail já cadastrado'
                ], 400);
            }

            $user = new UserProfile();
            $user->email = $request->email;
            $user->dt_nascimento = $request->dt_nascimento;
            $user->senha = Hash::make($request->senha);
            $saved = $user->save();

            if (!$saved || !$user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar usuário no banco de dados'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Usuário registrado com sucesso'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar usuário: ' . $e->getMessage()
            ], 500);
        }
    }
    public function listagemUsuarios()
    {
        $usuarios = UserProfile::select('id', 'email', 'dt_nascimento')->get();

        return response()->json([
            'success' => true,
            'data' => $usuarios
        ]);
    }
}
