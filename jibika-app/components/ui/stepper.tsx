import * as React from "react"
import { cn } from "@/lib/utils"

interface StepperProps {
  currentStep: number;
  totalSteps: number;
  className?: string;
}

export function Stepper({ currentStep, totalSteps, className }: StepperProps) {
  return (
    <div className={cn("flex items-center space-x-2", className)}>
      {Array.from({ length: totalSteps }).map((_, i) => (
        <React.Fragment key={i}>
          <div
            className={cn(
              "h-2 flex-1 rounded-full transition-colors",
              i < currentStep 
                ? "bg-primary-600" 
                : "bg-slate-200 dark:bg-slate-800"
            )}
          />
        </React.Fragment>
      ))}
      <span className="ml-2 text-xs font-medium text-slate-500">
        {currentStep} / {totalSteps}
      </span>
    </div>
  )
}
