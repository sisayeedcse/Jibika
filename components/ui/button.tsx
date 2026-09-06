import * as React from "react"
import { cn } from "@/lib/utils"

export interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: 'default' | 'outline' | 'ghost' | 'accent'
  size?: 'default' | 'sm' | 'lg' | 'icon'
}

const Button = React.forwardRef<HTMLButtonElement, ButtonProps>(
  ({ className, variant = 'default', size = 'default', ...props }, ref) => {
    return (
      <button
        ref={ref}
        className={cn(
          "inline-flex items-center justify-center whitespace-nowrap rounded-xl text-base font-bold transition-all duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50",
          {
            'bg-primary-600 text-white shadow-md hover:bg-primary-700 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0': variant === 'default',
            'bg-accent-500 text-white shadow-md hover:bg-accent-600 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0': variant === 'accent',
            'border-2 border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300 text-slate-700': variant === 'outline',
            'hover:bg-slate-100 text-slate-700': variant === 'ghost',
            'h-12 px-6 py-2': size === 'default',
            'h-10 px-4 text-sm': size === 'sm',
            'h-14 px-10 text-lg': size === 'lg',
            'h-12 w-12': size === 'icon',
          },
          className
        )}
        {...props}
      />
    )
  }
)
Button.displayName = "Button"

export { Button }
