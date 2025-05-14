<?php

namespace App\reservas\Asociaciones\Services;

use Illuminate\Support\Facades\DB;
use Exception;

use App\reservas\Asociaciones\Models\Asociacion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AsociacionesService
{
    /**
     * Obtener todas las asociaciones paginadas
     */
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return Asociacion::with('municipalidad')->paginate($perPage);
    }

    /**
     * Obtener una asociación por su ID
     */
    public function getById(int $id): ?Asociacion
    {
        return Asociacion::with('municipalidad')->find($id);
    }

    /**
     * Obtener una asociación con sus emprendedores
     */
    public function getWithEmprendedores(int $id): ?Asociacion
    {
        return Asociacion::with('emprendedores')->find($id);
    }

    /**
     * Crear una nueva asociación
     */
    public function create(array $data): Asociacion
    {
        try {
            DB::beginTransaction();

            $asociacion = new Asociacion();
            $asociacion->fill($data);
            
            if (!$asociacion->save()) {
                throw new Exception('Error al guardar el registro en la base de datos');
            }
            
            DB::commit();
            return $asociacion;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Actualizar una asociación existente
     */
    public function update(int $id, array $data): ?Asociacion
    {
        try {
            DB::beginTransaction();
            
            $asociacion = Asociacion::find($id);
            
            if (!$asociacion) {
                DB::rollBack();
                return null;
            }
            
            $asociacion->fill($data);
            
            if (!$asociacion->save()) {
                throw new Exception('Error al actualizar el registro en la base de datos');
            }
            
            DB::commit();
            return $asociacion;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Eliminar una asociación
     */
    public function delete(int $id): bool
    {
        $asociacion = $this->getById($id);

        if (!$asociacion) {
            return false;
        }

        return $asociacion->delete();
    }

    /**
     * Obtener asociaciones por municipalidad
     */
    public function getByMunicipalidad(int $municipalidadId): Collection
    {
        return Asociacion::where('municipalidad_id', $municipalidadId)->get();
    }
}