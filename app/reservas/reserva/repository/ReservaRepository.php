<?php
namespace App\reservas\reserva\Repository;

use App\Reservas\Reserva\Models\Reserva;
use App\Reservas\Reserva\Models\ReservaServicio;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ReservaRepository
{
    protected $model;

    public function __construct(Reserva $reserva)
    {
        $this->model = $reserva;
    }

    /**
     * Obtener todas las reservas
     */
    public function getAll(): Collection
    {
        return $this->model->with(['usuario', 'servicios.servicio', 'servicios.emprendedor'])->get();
    }

    /**
     * Obtener reservas paginadas
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['usuario', 'servicios.servicio', 'servicios.emprendedor'])->paginate($perPage);
    }

    /**
     * Encontrar reserva por ID
     */
    public function findById(int $id): ?Reserva
    {
        return $this->model->with(['usuario', 'servicios.servicio', 'servicios.emprendedor'])->find($id);
    }

    /**
     * Crear una nueva reserva con sus servicios
     */
    public function create(array $data, array $servicios = []): Reserva
    {
        try {
            DB::beginTransaction();
            
            // Generar código único de reserva
            if (!isset($data['codigo_reserva'])) {
                $data['codigo_reserva'] = Reserva::generarCodigoReserva();
            }
            
            $reserva = $this->model->create($data);
            
            // Crear servicios de la reserva
            if (!empty($servicios)) {
                foreach ($servicios as $servicioData) {
                    $servicioData['reserva_id'] = $reserva->id;
                    ReservaServicio::create($servicioData);
                }
            }
            
            DB::commit();
            return $reserva->fresh(['usuario', 'servicios.servicio', 'servicios.emprendedor']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Actualizar una reserva existente
     */
    public function update(int $id, array $data, array $servicios = []): bool
    {
        try {
            DB::beginTransaction();
            
            $reserva = $this->findById($id);
            if (!$reserva) {
                DB::rollBack();
                return false;
            }
            
            $updated = $reserva->update($data);
            
            // Actualizar servicios de la reserva
            if (!empty($servicios)) {
                $serviciosIds = array_column($servicios, 'id');
                $serviciosIds = array_filter($serviciosIds); // Eliminar valores nulos
                
                // Eliminar servicios que no están en la lista
                if (!empty($serviciosIds)) {
                    $reserva->servicios()->whereNotIn('id', $serviciosIds)->delete();
                } else {
                    // Si no hay IDs, eliminar todos los servicios existentes
                    $reserva->servicios()->delete();
                }
                
                // Crear o actualizar servicios
                foreach ($servicios as $servicioData) {
                    $servicioId = $servicioData['id'] ?? null;
                    unset($servicioData['id']);
                    
                    if ($servicioId) {
                        $servicio = ReservaServicio::find($servicioId);
                        if ($servicio && $servicio->reserva_id == $reserva->id) {
                            $servicio->update($servicioData);
                        }
                    } else {
                        $servicioData['reserva_id'] = $reserva->id;
                        ReservaServicio::create($servicioData);
                    }
                }
            }
            
            DB::commit();
            return $updated;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Eliminar una reserva
     */
    public function delete(int $id): bool
    {
        try {
            DB::beginTransaction();
            
            $reserva = $this->findById($id);
            if (!$reserva) {
                DB::rollBack();
                return false;
            }
            
            // Eliminar servicios relacionados
            $reserva->servicios()->delete();
            
            $deleted = $reserva->delete();
            
            DB::commit();
            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Obtener reservas por usuario
     */
    public function getByUsuario(int $usuarioId): Collection
    {
        return $this->model->where('usuario_id', $usuarioId)
            ->with(['servicios.servicio', 'servicios.emprendedor'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener reservas por estado
     */
    public function getByEstado(string $estado): Collection
    {
        return $this->model->where('estado', $estado)
            ->with(['usuario', 'servicios.servicio', 'servicios.emprendedor'])
            ->get();
    }
    
    /**
     * Obtener reservas por emprendedor
     */
    public function getByEmprendedor(int $emprendedorId): Collection
    {
        return $this->model->whereHas('servicios', function ($query) use ($emprendedorId) {
            $query->where('emprendedor_id', $emprendedorId);
        })->with(['usuario', 'servicios' => function ($query) use ($emprendedorId) {
            $query->where('emprendedor_id', $emprendedorId)
                  ->with(['servicio', 'emprendedor']);
        }])->get();
    }
    
    /**
     * Obtener reservas por servicio
     */
    public function getByServicio(int $servicioId): Collection
    {
        return $this->model->whereHas('servicios', function ($query) use ($servicioId) {
            $query->where('servicio_id', $servicioId);
        })->with(['usuario', 'servicios' => function ($query) use ($servicioId) {
            $query->where('servicio_id', $servicioId)
                  ->with(['servicio', 'emprendedor']);
        }])->get();
    }
    
    /**
     * Cambiar el estado de una reserva
     */
    public function cambiarEstado(int $id, string $estado): bool
    {
        try {
            DB::beginTransaction();
            
            $reserva = $this->findById($id);
            if (!$reserva) {
                DB::rollBack();
                return false;
            }
            
            $updated = $reserva->update(['estado' => $estado]);
            
            // Actualizar el estado de todos los servicios de la reserva
            if ($updated) {
                $estadoServicio = '';
                
                switch ($estado) {
                    case Reserva::ESTADO_CONFIRMADA:
                        $estadoServicio = ReservaServicio::ESTADO_CONFIRMADO;
                        break;
                    case Reserva::ESTADO_CANCELADA:
                        $estadoServicio = ReservaServicio::ESTADO_CANCELADO;
                        break;
                    case Reserva::ESTADO_COMPLETADA:
                        $estadoServicio = ReservaServicio::ESTADO_COMPLETADO;
                        break;
                    default:
                        $estadoServicio = ReservaServicio::ESTADO_PENDIENTE;
                }
                
                $reserva->servicios()->update(['estado' => $estadoServicio]);
            }
            
            DB::commit();
            return $updated;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}