<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Padre;
use App\Models\FaltaGrave;
use App\Models\TipoFalta;

Route::get('/test', function () {
    dump('hola');
});


Route::post('/login', function(Request $request) {
    Log::info('Datos recibidos:', $request->all());
    // valido manualmente si los campos estan vacios
    if (!$request->filled('email') || !$request->filled('password')) {
        return response()->json([
            'message' => 'Debe ingresar correo y contraseña',
        ], 400);
    }

    // valido formato de email
    if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
        return response()->json([
            'message' => 'El correo no tiene un formato válido',
        ], 400);
    }
    
    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Credenciales inválidas'], 401);
    }

    return response()->json([
        'message' => 'Login exitoso',
        'user' => $user,
    ]);
});


Route::get('/cursos', function () {
    $cursos = Curso::all();
    return response()->json($cursos);
});
    

Route::get('/estudiantes', function () {
    // traer todos los estudiantes junto con su curso y padre
    $estudiantes = Estudiante::with(['curso', 'padre'])->get();

    // definir las longitudes fijas
    $longitudNombre = 8;   // caracteres reservados para el nombre
    $longitudApellido = 20; // caracteres reservados para el apellido
    $longitudCurso = 12;    // caracteres reservados para el curso

    $estudiantes->transform(function ($estudiante) use ($longitudNombre, $longitudApellido, $longitudCurso) {
        // id con ceros a la izquierda
        $estudiante->id_formateado = '' . str_pad($estudiante->id, 4, '0', STR_PAD_LEFT);

        // nombre del curso
        $cursoNombre = $estudiante->curso ? $estudiante->curso->nombre : '';

        //normalizar longitud de texto (rellenar con espacios si es más corto)
        $nombreAjustado = str_pad(substr($estudiante->nombre ?? '', 0, $longitudNombre), $longitudNombre, ' ', STR_PAD_RIGHT);
        $apellidoAjustado = str_pad(substr($estudiante->apellido ?? '', 0, $longitudApellido), $longitudApellido, ' ', STR_PAD_RIGHT);
        $cursoAjustado = str_pad(substr($cursoNombre, 0, $longitudCurso), $longitudCurso, ' ', STR_PAD_RIGHT);

        // Atributos personalizados
        $estudiante->cursoNombre = $cursoNombre;
        $estudiante->nombrePadre = $estudiante->padre
            ? ($estudiante->padre->nombre . ' ' . $estudiante->padre->apellido)
            : null;

        // Nuevo atributo combinado (alineado)
        $estudiante->formato_fijo = $nombreAjustado . $apellidoAjustado . $cursoAjustado;

        return $estudiante;
    });

    return response()->json($estudiantes);
});


Route::get('/padres', function () {
    // traigo todos los padres junto con el usuario asociado
    $padres = Padre::with('user')->get();

    return response()->json($padres);
});

Route::get('/faltas-graves', function () {
    // traigo todas las faltas graves con relaciones tipo_falta sancion y estudiante  curso
    $faltas = FaltaGrave::with([
        'tipo_falta.sancion',
        'estudiante.curso'
    ])->get();

    return response()->json($faltas);
});


Route::get('/estudiantes/{id}', function ($id) {
    preg_match('/^0*(\d+)/', $id, $matches);
    $estudianteId = $matches[1] ?? null;

    if (!$estudianteId) {
        return response()->json([
            'message' => 'Formato de ID inválido. Debe ser como "ID-1 Nombre Apellido".'
        ], 400);
    }

    $estudiante = Estudiante::with([
        'curso',
        'padre.user',
        'faltas.tipo_falta.sancion'
    ])->find($estudianteId);

    if (!$estudiante) {
        return response()->json([
            'message' => 'Estudiante no encontrado'
        ], 404);
    }

    // pongo atributos personalizados
    $estudiante->cursoNombre = $estudiante->curso ? $estudiante->curso->nombre : null;
    $estudiante->nombrePadre = $estudiante->padre
        ? ($estudiante->padre->nombre . ' ' . $estudiante->padre->apellido)
        : null;

    // ajusta cada falta con tipo_falta.nombre y sancion.descripcion
    $estudiante->faltas->transform(function($falta) {
        $falta->tipo_falta_nombre = $falta->tipo_falta->nombre ?? null;
        $falta->sancion_descripcion = $falta->tipo_falta->sancion->descripcion ?? null;
        return $falta;
    });

    return response()->json($estudiante);
});


Route::post('/sancionar', function (Request $request) {
    Log::info('Datos recibidos:', $request->all());
    // valido que lleguen los datos necesarios
    $request->validate([
        'id_estudiante' => 'required|string',
        'tipo_falta' => 'required|string'
    ]);

    preg_match('/^0*(\d+)/', $request->id_estudiante, $matches);
    $estudianteId = isset($matches[1]) ? intval($matches[1]) : null;

    if (!$estudianteId) {
        return response()->json([
            'message' => 'Formato de ID inválido. Debe ser como "ID-1 Nombre Apellido".'
        ], 400);
    }

    //verifico que el estudiante exista
    $estudiante = Estudiante::find($estudianteId);
    if (!$estudiante) {
        return response()->json([
            'message' => 'El estudiante no existe.'
        ], 404);
    }

    //busco el tipo de falta por nombre
    $tipoFalta = TipoFalta::where('nombre', $request->tipo_falta)->first();

    if (!$tipoFalta) {
        return response()->json([
            'message' => 'El tipo de falta no existe.'
        ], 404);
    }

    // creo la nueva falta grave
    $falta = FaltaGrave::create([
        'tipo_falta_id' => $tipoFalta->id,
        'estudiante_id' => $estudianteId,
        'descripcion' => 'Falta por ' . strtolower($tipoFalta->nombre),
        'estado' => 'pendiente',
        'fecha' => now(),
    ]);

    // cargo relaciones para devolver la info completa
    $falta->load([
        'tipo_falta.sancion',
        'estudiante.curso',
    ]);

    return response()->json([
        'message' => 'Sanción registrada correctamente',
        'falta' => $falta
    ], 201);
});


//eliminar falta grave por id extraido del texto
Route::delete('/faltas/{texto}', function ($texto, Request $request) {
    Log::info('Solicitud de eliminación de falta', ['texto' => $texto]);

    if (preg_match('/ID-(\d+)/', $texto, $matches)) {
        $faltaId = intval($matches[1]);
    } else {
        return response()->json([
            'message' => 'Formato de ID inválido. Debe ser como "ID-4 tipo: llegada".'
        ], 400);
    }

    $falta = FaltaGrave::find($faltaId);

    if (!$falta) {
        return response()->json([
            'message' => 'Falta no encontrada.'
        ], 404);
    }

    try {
        $falta->delete();

        return response()->json([
            'message' => 'Falta eliminada correctamente.',
            'id_eliminado' => $faltaId
        ], 200);
    } catch (\Exception $e) {
        Log::error('Error al eliminar la falta: ' . $e->getMessage());
        return response()->json([
            'message' => 'Error al intentar eliminar la falta.',
            'error' => $e->getMessage()
        ], 500);
    }
});



Route::post('/estudiantes', function (Request $request) {
    Log::info('Datos recibidos al crear estudiante:', $request->all());

    // procesar datos del padre
    // ======================================================
    $padreNombreCompleto = null;
    $padreNumero = null;

    if (!empty($request->padreDatos)) {
        // Ejemplo: "benita rios 67894532"
        $partesPadre = explode(' ', trim($request->padreDatos));
        $padreNumero = array_pop($partesPadre); // último valor = número
        $padreNombreCompleto = implode(' ', $partesPadre);
    }

    $padre = null;
    if ($padreNombreCompleto) {
        // busco si ya existe por nombre y numero
        $padre = Padre::where('nombre', 'LIKE', "%$padreNombreCompleto%")
                      ->where('numero', $padreNumero)
                      ->first();

        if (!$padre) {
            // separo nombre y apellido si hay al menos dos palabras
            $partes = explode(' ', $padreNombreCompleto);
            $padreNombre = ucfirst(array_shift($partes));
            $padreApellido = count($partes) ? implode(' ', $partes) : 'N/A';

            $padre = Padre::create([
                'nombre' => $padreNombre,
                'apellido' => $padreApellido,
                'numero' => $padreNumero ?? 'N/A',
                'user_id' => auth()->id() ?? null,
            ]);

            Log::info('Padre creado automáticamente:', ['id' => $padre->id]);
        } else {
            Log::info('Padre existente encontrado:', ['id' => $padre->id]);
        }
    }

    // procesar datos del curso
    // ======================================================
    $cursoNombre = null;
    $nombreAsesor = null;

    if (!empty($request->cursoDatos)) {
        // por ejempo 5 secun. Pedro Dominguez
        // inteto dividir en dos partes: curso y asesor
        $partes = explode('.', $request->cursoDatos, 2);
        $cursoNombre = trim($partes[0] ?? 'Sin nombre');
        $nombreAsesor = trim($partes[1] ?? 'Sin asignar');
    }

    $curso = null;
    if ($cursoNombre) {
        $curso = Curso::where('nombre', 'LIKE', "%$cursoNombre%")
                      ->first();

        if (!$curso) {
            $curso = Curso::create([
                'nombre' => $cursoNombre,
                'nombre_asesor' => $nombreAsesor ?: 'Sin asignar',
            ]);

            Log::info('Curso creado automáticamente:', ['id' => $curso->id]);
        } else {
            Log::info('Curso existente encontrado:', ['id' => $curso->id]);
        }
    }

    // valido estudiante y crear
    // ======================================================
    $validated = $request->validate([
        'nombre'    => 'required|string|max:100',
        'apellido'  => 'required|string|max:100',
        'numero'    => 'required|string|max:20',
    ]);

    try {
        $estudiante = Estudiante::create([
            'curso_id'  => $curso?->id,
            'padre_id'  => $padre?->id,
            'nombre'    => $validated['nombre'],
            'apellido'  => $validated['apellido'],
            'numero'    => $validated['numero'],
        ]);

        Log::info('Estudiante creado exitosamente:', ['id' => $estudiante->id]);

        return response()->json([
            'message' => 'Estudiante creado correctamente.',
            'data' => $estudiante,
        ], 201);

    } catch (\Exception $e) {
        Log::error('Error al crear estudiante:', ['error' => $e->getMessage()]);

        return response()->json([
            'message' => 'Ocurrió un error al crear el estudiante.',
            'error' => $e->getMessage(),
        ], 500);
    }
});