// ============================================================================
// CATÁLOGOS Y ROLES INSTITUCIONALES (TFJA)
// ============================================================================

export type RolClave =
    | 'ADMIN_DGTIC'
    | 'RECURSOS_HUMANOS'
    | 'MAGISTRADO_PRESIDENTE'
    | 'MAGISTRADO_PONENTE'
    | 'MAGISTRADO_VISITADOR';

export interface Rol {
    id: number;
    clave: RolClave;
    nombre: string;
}

export interface Region {
    id: number;
    clave: string;
    nombre: string;
}

export interface Sala {
    id: number;
    clave: string;
    nombre: string;
    tipo: string;
}

export interface Area {
    id: number;
    clave: string;
    nombre: string;
    tipo: string;
    numero: number | null;
}

// ============================================================================
// ENTIDAD USUARIO Y RELACIONES
// ============================================================================

export interface Usuario {
    id: number;
    name: string;
    email: string;
    activo: boolean;
    region_id: number | null;
    sala_id: number | null;
    area_id: number | null;
    region?: Region | null;
    sala?: Sala | null;
    area?: Area | null;
    roles: Rol[];
    created_at: string;
}

// ============================================================================
// PAGINACIÓN Y FILTROS DE INERTIA
// ============================================================================

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: PaginationLink[];
    from: number | null;
    to: number | null;
}

export interface UsuarioFiltros {
    buscar?: string;
    region_id?: number | string;
    sala_id?: number | string;
    area_id?: number | string;
    role_id?: number | string;
}

export interface UsuariosIndexProps {
    usuarios: PaginatedData<Usuario>;
    filtros: UsuarioFiltros;
    regiones: Region[];
    roles: Rol[];
}

// ============================================================================
// PAYLOADS PARA FORMULARIOS (POST / PUT)
// ============================================================================

export interface UsuarioFormPayload {
    name: string;
    email: string;
    password?: string | null;
    roles: number[];
    region_id: number | null;
    sala_id: number | null;
    area_id: number | null;
    activo: boolean;
}