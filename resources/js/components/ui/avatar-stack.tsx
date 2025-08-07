import * as React from "react"
import { cn } from "@/lib/utils"

interface AvatarStackProps extends React.HTMLAttributes<HTMLDivElement> {
  /**
   * Array of avatar data
   */
  avatars: Array<{
    src?: string
    name: string
    role?: string
  }>
  /**
   * Maximum number to show on desktop
   * @default 5
   */
  maxDesktop?: number
  /**
   * Maximum number to show on mobile
   * @default 3
   */
  maxMobile?: number
  /**
   * Avatar size variant
   * @default "default"
   */
  size?: "small" | "default" | "large"
  /**
   * Show interactive hover effects
   * @default true
   */
  interactive?: boolean
}

const sizeConfig = {
  small: {
    container: "h-10 w-10",
    text: "text-xs",
    overlap: "-space-x-2",
    ring: "ring-2",
  },
  default: {
    container: "h-12 w-12",
    text: "text-sm",
    overlap: "-space-x-3",
    ring: "ring-3",
  },
  large: {
    container: "h-14 w-14",
    text: "text-sm",
    overlap: "-space-x-3",
    ring: "ring-4",
  },
}

const AvatarStack = React.forwardRef<HTMLDivElement, AvatarStackProps>(
  (
    {
      avatars,
      maxDesktop = 5,
      maxMobile = 3,
      size = "default",
      interactive = true,
      className,
      ...props
    },
    ref
  ) => {
    const config = sizeConfig[size]
    
    // Responsive display logic
    const [isMobile, setIsMobile] = React.useState(false)
    
    React.useEffect(() => {
      const checkMobile = () => {
        setIsMobile(window.innerWidth < 640)
      }
      checkMobile()
      window.addEventListener("resize", checkMobile)
      return () => window.removeEventListener("resize", checkMobile)
    }, [])
    
    const maxDisplay = isMobile ? maxMobile : maxDesktop
    const displayAvatars = avatars.slice(0, maxDisplay)
    const remainingCount = Math.max(avatars.length - maxDisplay, 0)

    return (
      <div
        ref={ref}
        className={cn(
          "isolate flex items-center",
          config.overlap,
          className
        )}
        role="group"
        aria-label="Team members"
        {...props}
      >
        {displayAvatars.map((avatar, index) => (
          <div
            key={`${avatar.name}-${index}`}
            className={cn(
              "relative group",
              interactive && "transition-all duration-200"
            )}
            style={{ 
              zIndex: index,
              // Stagger animation on mount
              animationDelay: `${index * 50}ms`
            }}
          >
            {/* Avatar Container */}
            <div
              className={cn(
                config.container,
                config.ring,
                "relative overflow-hidden rounded-full",
                "ring-card bg-muted",
                interactive && [
                  "cursor-pointer",
                  "hover:scale-110 hover:!z-50",
                  "hover:ring-primary/30",
                  "hover:shadow-lg",
                  "transform-gpu" // Hardware acceleration
                ],
                // Entrance animation
                "animate-in fade-in-50 zoom-in-95 duration-300"
              )}
              role="img"
              aria-label={avatar.name}
            >
              {avatar.src ? (
                <img
                  src={avatar.src}
                  alt={avatar.name}
                  className="h-full w-full object-cover"
                  loading="lazy"
                  decoding="async"
                />
              ) : (
                <div className="flex h-full w-full items-center justify-center bg-gradient-to-br from-primary/10 to-primary/20">
                  <span className={cn("font-medium text-primary", config.text)}>
                    {getInitials(avatar.name)}
                  </span>
                </div>
              )}
            </div>

            {/* Enhanced Tooltip with role */}
            {interactive && (
              <div
                className={cn(
                  "absolute left-1/2 -translate-x-1/2",
                  "opacity-0 pointer-events-none",
                  "group-hover:opacity-100",
                  "transition-all duration-200",
                  "z-[100]",
                  size === "small" ? "-top-8" : "-top-10"
                )}
                role="tooltip"
              >
                <div className="relative">
                  <div className="bg-popover text-popover-foreground rounded-lg shadow-xl border px-3 py-2">
                    <p className="font-medium text-xs whitespace-nowrap">
                      {avatar.name}
                    </p>
                    {avatar.role && (
                      <p className="text-[10px] text-muted-foreground whitespace-nowrap">
                        {avatar.role}
                      </p>
                    )}
                  </div>
                  {/* Tooltip arrow */}
                  <div className="absolute left-1/2 -translate-x-1/2 -bottom-1 w-2 h-2 bg-popover border-r border-b rotate-45" />
                </div>
              </div>
            )}
          </div>
        ))}

        {/* Overflow Counter */}
        {remainingCount > 0 && (
          <div
            className={cn(
              "relative",
              interactive && "transition-all duration-200"
            )}
            style={{ zIndex: displayAvatars.length }}
          >
            <div
              className={cn(
                config.container,
                config.ring,
                "flex items-center justify-center rounded-full",
                "ring-card bg-muted text-muted-foreground",
                "font-semibold",
                config.text,
                interactive && [
                  "cursor-pointer hover:bg-muted/80",
                  "hover:text-foreground"
                ],
                "animate-in fade-in-50 zoom-in-95 duration-300"
              )}
              role="button"
              tabIndex={0}
              aria-label={`${remainingCount} more team members`}
              style={{ animationDelay: `${displayAvatars.length * 50}ms` }}
            >
              +{remainingCount}
            </div>
          </div>
        )}
      </div>
    )
  }
)

AvatarStack.displayName = "AvatarStack"

function getInitials(name: string): string {
  return name
    .split(" ")
    .map(n => n[0])
    .join("")
    .toUpperCase()
    .slice(0, 2)
}

export { AvatarStack, type AvatarStackProps }