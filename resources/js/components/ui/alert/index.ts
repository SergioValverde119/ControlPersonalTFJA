import type { VariantProps } from "class-variance-authority"
import { cva } from "class-variance-authority"

export { default as Alert } from "./Alert.vue"
export { default as AlertDescription } from "./AlertDescription.vue"
export { default as AlertTitle } from "./AlertTitle.vue"

export const alertVariants = cva(
  "relative w-full rounded-none border px-3.5 py-2.5 text-xs grid has-[>svg]:grid-cols-[calc(var(--spacing)*4)_1fr] grid-cols-[0_1fr] has-[>svg]:gap-x-2.5 gap-y-0.5 items-start [&>svg]:size-4 [&>svg]:translate-y-0.5 [&>svg]:text-current shadow-sm",
  {
    variants: {
      variant: {
        // Informativo / Default: Franja Azul TFJA
        default:
          "bg-slate-50 text-slate-800 border-tfja-border border-l-4 border-l-tfja-blue [&>svg]:text-tfja-blue *:data-[slot=alert-description]:text-slate-600",

        // Advertencia / Criterio: Tokens Review (Ámbar) con franja Bronce TFJA
        warning:
          "bg-status-review-bg text-status-review-text border-status-review-border border-l-4 border-l-tfja-bronze [&>svg]:text-tfja-bronze *:data-[slot=alert-description]:text-status-review-text",

        // Autorizado / Corte: Tokens Approved (Verde)
        success:
          "bg-status-approved-bg text-status-approved-text border-status-approved-border border-l-4 border-l-status-approved-text [&>svg]:text-status-approved-text *:data-[slot=alert-description]:text-status-approved-text",

        // Inconsistencia / Observación: Tokens Rejected (Rojo)
        destructive:
          "bg-status-rejected-bg text-status-rejected-text border-status-rejected-border border-l-4 border-l-status-rejected-text [&>svg]:text-status-rejected-text *:data-[slot=alert-description]:text-status-rejected-text",
      },
    },
    defaultVariants: {
      variant: "default",
    },
  },
)

export type AlertVariants = VariantProps<typeof alertVariants>
