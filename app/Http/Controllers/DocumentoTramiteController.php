<?php

namespace App\Http\Controllers;

use App\Models\TramiteDocumento;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentoTramiteController extends Controller
{
    /**
     * Visualiza el archivo PDF directamente en el navegador (inline).
     */
    public function show(TramiteDocumento $documento): StreamedResponse
    {
        Gate::authorize('view', $documento->tramite);

        $disk = Storage::disk('local');

        if (! $disk->exists($documento->archivo_path)) {
            abort(404, 'El archivo físico no fue localizado en el servidor de almacenamiento.');
        }

        $mimeType = $disk->mimeType($documento->archivo_path) ?? 'application/pdf';

        return $disk->response(
            $documento->archivo_path,
            $documento->nombre_original,
            [
                'Content-Type'        => $mimeType,
                'Content-Disposition' => 'inline; filename="' . addslashes($documento->nombre_original) . '"',
            ]
        );
    }

    /**
     * Fuerza la descarga directa del archivo con su nombre original.
     */
    public function download(TramiteDocumento $documento): StreamedResponse
    {
        Gate::authorize('view', $documento->tramite);

        $disk = Storage::disk('local');

        if (! $disk->exists($documento->archivo_path)) {
            abort(404, 'El archivo físico no fue localizado en el servidor de almacenamiento.');
        }

        return $disk->download(
            $documento->archivo_path,
            $documento->nombre_original
        );
    }
}
