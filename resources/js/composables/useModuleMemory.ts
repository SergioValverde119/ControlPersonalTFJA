import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

/**
 * ESTADO COMPARTIDO (Singleton a nivel de módulo)
 * 
 * Al declarar este objeto reactivo FUERA de la función 'useModuleMemory',
 * existe una única instancia en la memoria RAM del navegador.
 * Aunque el componente que lo use se monte y desmonte mil veces,
 * los datos aquí guardados no se destruyen mientras no se refresque la página (F5).
 */
const lastVisitedUrls = reactive<Record<string, string>>({
    'nuevo-tramite': '',
    'estado-tramite': '',
    autorizar: '',
});

/**
 * ESCUCHADOR GLOBAL DE INERTIA
 * 
 * router.on('navigate') se dispara automáticamente cada vez que el usuario
 * cambia de ruta mediante cualquier componente <Link> o llamada a router.visit().
 */
router.on('navigate', (event) => {
    // Obtenemos la URL a la que acaba de ingresar (ej. "/nuevo-tramite/alta")
    const url = event.detail.page.url;

    // Expresión regular: busca el primer segmento de la ruta después de la diagonal inicial.
    // Ejemplo: "/nuevo-tramite/alta?folio=1" -> extrae "nuevo-tramite"
    const match = url.match(/^\/([^\/?#]+)/);

    // Validación de ESLint: línea en blanco antes del condicional.
    // Si el primer segmento coincide con alguno de nuestros módulos registrados en el objeto:
    if (match && match[1] in lastVisitedUrls) {
        // Sobrescribimos el valor guardado con la URL completa que visitó
        lastVisitedUrls[match[1]] = url;
    }
});

/**
 * COMPOSABLE: useModuleMemory
 * 
 * Función exportada que consumirán los componentes de Vue (como AppHeader.vue).
 * Sigue la convención de nombres de Vue 3: iniciar siempre con el prefijo "use".
 */
export function useModuleMemory() {
    /**
     * Resuelve qué URL entregar al enlace:
     * - Si el usuario ya visitó una sub-sección del módulo, devuelve esa sub-sección.
     * - Si es la primera vez que entra o la memoria está vacía, devuelve la ruta por defecto.
     * 
     * @param moduleKey Identificador del módulo ('nuevo-tramite', 'estado-tramite', 'autorizar')
     * @param defaultUrl URL base por defecto generada con Wayfinder (ej. toUrl(nuevoTramite.index()))
     */
    const getModuleUrl = (moduleKey: string, defaultUrl: string) => {
        return lastVisitedUrls[moduleKey] || defaultUrl;
    };

    // Exponemos hacia afuera las funciones y estados que los componentes necesitan leer
    return {
        lastVisitedUrls,
        getModuleUrl,
    };
}