import type { VariantProps } from "class-variance-authority"
import { cva } from "class-variance-authority"

export { default as Button } from "./Button.vue"

export const buttonVariants = cva(
  "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-none text-xs font-semibold uppercase tracking-wider transition-colors disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive",
  {
    variants: {
      variant: {
       // Primario Oficial: Azul TFJA con texto blanco puro de alto contraste
        default:
          "bg-tfja-blue text-white hover:bg-tfja-blue-hover focus-visible:ring-tfja-blue shadow-xs active:bg-tfja-blue-dark",

        // Acento / Trámites Prioritarios: Bronce TFJA con texto blanco
        bronze:
          "bg-tfja-bronze text-white hover:bg-tfja-bronze-hover focus-visible:ring-tfja-bronze shadow-xs active:bg-tfja-bronze-dark",

        // Destructivo / Rechazo de Plaza: Rojo de observación con texto blanco
        destructive:
          "bg-status-rejected-text text-white hover:bg-red-600 focus-visible:ring-status-rejected-text shadow-xs",

        // Contorno Institucional: Borde definido, fondo blanco y texto azul legible
        outline:
          "border border-slate-300 bg-white text-slate-700 hover:bg-slate-100 hover:text-tfja-blue focus-visible:ring-slate-400 shadow-xs active:bg-slate-200",

        // Secundario Neutro: Fondo gris perla institucional con texto oscuro
        secondary:
          "bg-slate-200 text-slate-800 hover:bg-slate-300 hover:text-slate-900 focus-visible:ring-slate-400 active:bg-slate-400",

        // Ghost para Tablas / "Ver Detalles": Texto azul marino nítido sobre fondo transparente
        ghost:
          "text-tfja-blue hover:bg-slate-200/80 hover:text-tfja-blue-hover focus-visible:ring-tfja-blue",

        // Enlace
        link:
          "text-tfja-blue underline-offset-4 hover:underline p-0 h-auto font-bold normal-case tracking-normal hover:text-tfja-blue-hover",
      },
      size: {
        "default": "h-9 px-4 py-2 has-[>svg]:px-3",
        "sm": "h-8  gap-1.5 px-3 has-[>svg]:px-2.5",
        "lg": "h-10  px-6 has-[>svg]:px-4",
        "icon": "size-9",
        "icon-sm": "size-8",
        "icon-lg": "size-10",
      },
    },
    defaultVariants: {
      variant: "default",
      size: "default",
    },
  },
)
export type ButtonVariants = VariantProps<typeof buttonVariants>
