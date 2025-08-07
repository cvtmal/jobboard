import * as React from "react"
import { cn } from "@/lib/utils"
import { Avatar, AvatarFallback, AvatarImage } from "./avatar"

interface AvatarGroupProps extends React.HTMLAttributes<HTMLDivElement> {
  /**
   * Array of avatar data with image, name, and optional fallback
   */
  avatars: Array<{
    src?: string
    name: string
    fallback?: string
  }>
  /**
   * Maximum number of avatars to display before showing +N
   * @default 4
   */
  max?: number
  /**
   * Size of avatars
   * @default "md"
   */
  size?: "sm" | "md" | "lg" | "xl"
  /**
   * Amount of overlap between avatars
   * @default "md"
   */
  overlap?: "sm" | "md" | "lg"
  /**
   * Whether to show tooltips on hover
   * @default true
   */
  showTooltip?: boolean
  /**
   * Custom render function for overflow indicator
   */
  renderOverflow?: (remaining: number) => React.ReactNode
}

const sizeClasses = {
  sm: "h-8 w-8",
  md: "h-10 w-10",
  lg: "h-12 w-12",
  xl: "h-16 w-16",
}

const overlapClasses = {
  sm: "-space-x-2",
  md: "-space-x-4",
  lg: "-space-x-6",
}

const AvatarGroup = React.forwardRef<HTMLDivElement, AvatarGroupProps>(
  (
    {
      avatars,
      max = 4,
      size = "md",
      overlap = "md",
      showTooltip = true,
      renderOverflow,
      className,
      ...props
    },
    ref
  ) => {
    const displayAvatars = avatars.slice(0, max)
    const remainingCount = Math.max(avatars.length - max, 0)

    return (
      <div
        ref={ref}
        className={cn("isolate flex", overlapClasses[overlap], className)}
        {...props}
      >
        {displayAvatars.map((avatar, index) => (
          <div
            key={`${avatar.name}-${index}`}
            className="relative group"
            style={{ zIndex: displayAvatars.length - index }}
          >
            <Avatar
              className={cn(
                sizeClasses[size],
                "ring-4 ring-white dark:ring-gray-900 transition-all duration-200",
                "group-hover:ring-primary/20 group-hover:scale-110 group-hover:!z-50"
              )}
            >
              <AvatarImage
                src={avatar.src}
                alt={avatar.name}
                className="object-cover"
              />
              <AvatarFallback className="text-xs">
                {avatar.fallback || getInitials(avatar.name)}
              </AvatarFallback>
            </Avatar>

            {/* Tooltip */}
            {showTooltip && (
              <div
                className="absolute -top-9 left-1/2 -translate-x-1/2 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-200 z-50"
                role="tooltip"
                aria-label={avatar.name}
              >
                <div className="bg-popover text-popover-foreground text-xs px-2 py-1 rounded-md shadow-lg whitespace-nowrap border">
                  {avatar.name}
                </div>
              </div>
            )}
          </div>
        ))}

        {/* Overflow Indicator */}
        {remainingCount > 0 && (
          <div
            className="relative"
            style={{ zIndex: displayAvatars.length }}
          >
            {renderOverflow ? (
              renderOverflow(remainingCount)
            ) : (
              <div
                className={cn(
                  sizeClasses[size],
                  "flex items-center justify-center rounded-full",
                  "bg-muted text-muted-foreground ring-4 ring-white dark:ring-gray-900",
                  "text-xs font-medium"
                )}
              >
                +{remainingCount}
              </div>
            )}
          </div>
        )}
      </div>
    )
  }
)

AvatarGroup.displayName = "AvatarGroup"

/**
 * Helper function to extract initials from a name
 */
function getInitials(name: string): string {
  const words = name.trim().split(/\s+/)
  if (words.length === 1) {
    return words[0].substring(0, 2).toUpperCase()
  }
  return words
    .slice(0, 2)
    .map((word) => word[0])
    .join("")
    .toUpperCase()
}

export { AvatarGroup, type AvatarGroupProps }