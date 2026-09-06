import * as React from "react"
import { cn } from "@/lib/utils"

export interface BadgeProps extends React.HTMLAttributes<HTMLDivElement> {
  variant?: 'default' | 'success' | 'warning' | 'neutral'
}

function Badge({ className, variant = 'default', ...props }: BadgeProps) {
  return (
    <div
      className={cn(
        "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none",
        {
          'bg-primary-700/10 text-primary-700 dark:bg-primary-600/20 dark:text-primary-600': variant === 'default',
          'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': variant === 'success',
          'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': variant === 'warning',
          'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300': variant === 'neutral',
        },
        className
      )}
      {...props}
    />
  )
}

export { Badge }
