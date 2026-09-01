import type { VariantProps } from "class-variance-authority"
import { cva } from "class-variance-authority"

export { default as Badge } from "./Badge.vue"

export const badgeVariants = cva(
  "inline-flex items-center justify-center rounded-none border px-2 py-0.5 text-xs uppercase font-bold tracking-wider w-fit whitespace-nowrap shrink-0 [&>svg]:size-3 gap-1 [&>svg]:pointer-events-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive transition-[color,box-shadow] overflow-hidden",
  {
    variants: {
      variant: {
        // TFJA Oficial: Azul Marino Institucional
        default:
          "border-tfja-blue bg-tfja-blue text-white [a&]:hover:bg-tfja-blue-dark focus-visible:ring-tfja-blue/30",

        // TFJA Acento: Bronce Institucional
        bronze:
          "border-tfja-bronze bg-tfja-bronze text-white [a&]:hover:bg-tfja-bronze-dark focus-visible:ring-tfja-bronze/30",

        // Secundario estándar (Gris neutro de interfaz)
        secondary:
          "border-transparent bg-secondary text-secondary-foreground [a&]:hover:bg-secondary/90",

        // Destructive original completo (Rojo sólido de alta criticidad)
        destructive:
          "border-transparent bg-destructive text-white [a&]:hover:bg-destructive/90 focus-visible:ring-destructive/20 dark:focus-visible:ring-destructive/40 dark:bg-destructive/60",

        // Contorno / Neutro WIN-SIAF
        outline:
          "border-slate-300 bg-white text-slate-700 [a&]:hover:bg-slate-100 [a&]:hover:text-slate-900",

        // Flujo SIGTP 1: Borrador / Captura inicial
        draft:
          "border-status-draft-border bg-status-draft-bg text-status-draft-text [a&]:hover:bg-slate-200",

        // Flujo SIGTP 2: En Revisión / Trámite en espera
        review:
          "border-status-review-border bg-status-review-bg text-status-review-text [a&]:hover:bg-amber-100",

        // Flujo SIGTP 3: Validación Paralela (Sala / Central)
        parallel:
          "border-status-parallel-border bg-status-parallel-bg text-status-parallel-text [a&]:hover:bg-sky-100",

        // Flujo SIGTP 4: Aprobado / Apto para corte JGA
        approved:
          "border-status-approved-border bg-status-approved-bg text-status-approved-text [a&]:hover:bg-emerald-100",

        // Flujo SIGTP 5: Observación técnica en trámite (Gafete suave)
        rejected:
          "border-status-rejected-border bg-status-rejected-bg text-status-rejected-text [a&]:hover:bg-red-100 focus-visible:ring-destructive/20 dark:focus-visible:ring-destructive/40",
      
      },
    },
    defaultVariants: {
      variant: "default",
    },
  },
)
export type BadgeVariants = VariantProps<typeof badgeVariants>
