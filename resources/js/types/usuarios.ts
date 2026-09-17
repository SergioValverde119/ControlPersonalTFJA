// ============================================================================
// ROLES INSTITUCIONALES (TFJA)
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

// ============================================================================
// IDENTIDAD CIVIL Y GRAFO ORGANIZACIONAL
// ============================================================================

export interface Persona {
    id: number;
    curp: string;
    rfc: string;
}

export type TipoTitularidad = 'TITULAR' | 'ENCARGADO_DESPACHO' | 'SUPLENTE';

export type TipoUnidadClave =
    | 'ORGANO_CENTRAL'
    | 'TERRITORIO'
    | 'SEDE'
    | 'SALA'
    | 'PONENCIA'
    | 'AREA_ADMINISTRATIVA';

export interface UnidadTipo {
    id: number;
    clave: string;
    nombre: string;
}

export interface UnidadOrganizacional {
    id: number;
    clave: string;
    nombre: string;
    tipo: TipoUnidadClave | UnidadTipo | string;
    hijos?: UnidadOrganizacional[];
}

export interface TitularidadActiva {
    id: number;
    tipo: TipoTitularidad;
    fecha_inicio?: string;
    unidad: {
        id: number;
        clave: string;
        nombre: string;
        tipo?: UnidadTipo | string;
    };
}

// ============================================================================
// ENTIDAD USUARIO
// ============================================================================

export interface Usuario {
    id: number;
    name: string;
    email: string;
    activo: boolean;
    persona?: Persona | null;
    roles: Rol[];
    titularidad_activa?: TitularidadActiva | null;
    created_at?: string;
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
    buscar?: string | null;
    unidad_id?: number | string | null;
    role_id?: number | string | null;
}

export interface UsuariosIndexProps {
    usuarios: PaginatedData<Usuario>;
    roles_disponibles: Rol[];
    unidades_arbol: UnidadOrganizacional[];
    filtros: UsuarioFiltros;
}

// ============================================================================
// PAYLOADS PARA FORMULARIOS (POST / PUT)
// ============================================================================

export interface UsuarioStorePayload {
    name: string;
    email: string;
    password: string;
    curp: string;
    rfc: string;
    roles: number[];
    unidad_organizacional_id: number | null;
    tipo_titularidad: TipoTitularidad;
    activo: boolean;
}

export interface UsuarioUpdatePayload {
    name: string;
    email: string;
    password?: string | null;
    roles: number[];
    unidad_organizacional_id: number | null;
    tipo_titularidad: TipoTitularidad;
    activo: boolean;
}

export interface UsuarioResetPasswordPayload {
    password: string;
}
