import type { PaginatedData, UnidadOrganizacional } from '@/types/usuarios';

export type EstatusPlaza = 'VACANTE' | 'RESERVADA' | 'EN_TRAMITE' | 'OCUPADA';

export interface Plaza {
    id: number;
    numero_plaza: string;
    puesto: string;
    nivel: string;
    adscripcion?: string | null;
    unidad?: UnidadOrganizacional | null;
    remuneracion_bruta?: number | null;
    estatus: EstatusPlaza;
}

export interface PlazasFiltros {
    buscar?: string | null;
    unidad_id?: number | string | null;
}

export type PlazasDisponiblesProps = {
    plazas: PaginatedData<Plaza>;
    filtros: PlazasFiltros;
    unidades_arbol?: UnidadOrganizacional[];
};

export type AltaProps = {
    plazas_disponibles: Plaza[];
    plaza_destino_id?: number | string | null;
};

export interface AltaFormPayload {
    plaza_destino_id: number | string | '';
    fecha_efectos_propuesta: string;
    curp: string;
    rfc: string;
    nombre: string;
    primer_apellido: string;
    segundo_apellido: string;
    correo_contacto: string;
    telefono_contacto: string;
    oficio_propuesta: File | null;
    curriculum_vitae: File | null;
}

// -----------------------------------------------------------------------------
// Tipos para el Módulo de Seguimiento y Circuito de Firmas
// -----------------------------------------------------------------------------

export type TipoTramite = 'ALTA' | 'BAJA' | 'PROMOCION' | 'DEMOCION';

export type EstatusTramite =
    | 'BORRADOR'
    | 'PENDIENTE'
    | 'EN_REVISION'
    | 'AUTORIZADO'
    | 'RECHAZADO'
    | 'DEVUELTO'
    | 'CONCLUIDO';

export type EstatusFirma =
    | 'PENDIENTE'
    | 'VOBO'
    | 'AUTORIZADO'
    | 'DEVUELTO'
    | 'OMITIDO';

export interface DocumentoTramite {
    id: number;
    tramite_id: number;
    tipo_documento: 'OFICIO_PROPUESTA' | 'CURRICULUM_VITAE' | 'OTRO';
    nombre_archivo: string;
    tamano_bytes?: number;
    mime_type: string;
    created_at: string;
}

export interface FirmaPaso {
    id: number;
    tramite_id: number;
    orden: number;
    rol_requerido: string;
    usuario_firmante_id?: number | null;
    usuario_firmante_nombre?: string | null;
    estatus: EstatusFirma;
    observaciones?: string | null;
    fecha_firma?: string | null;
    requiere_venia?: boolean;
}

export interface Tramite {
    id: number;
    folio: string;
    tipo: TipoTramite;
    estatus: EstatusTramite;
    fecha_efectos_propuesta: string;
    curp: string;
    rfc: string;
    nombre: string;
    primer_apellido: string;
    segundo_apellido?: string | null;
    nombre_completo?: string;
    correo_contacto?: string | null;
    telefono_contacto?: string | null;
    plaza_destino?: Plaza | null;
    solicitante?: {
        id: number;
        name: string;
        email: string;
    } | null;
    firmas?: FirmaPaso[];
    documentos?: DocumentoTramite[];
    total_firmas?: number;
    firmas_completadas?: number;
    created_at: string;
    updated_at: string;
}

export interface ConsultaGeneralFiltros {
    buscar?: string | null;
    tipo?: TipoTramite | 'TODOS';
    estatus?: EstatusTramite | 'TODOS';
}

export type ConsultaGeneralProps = {
    tramites: PaginatedData<Tramite>;
    filtros: ConsultaGeneralFiltros;
};

export type EstadoTramiteShowProps = {
    tramite: Tramite;
};
