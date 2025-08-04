import DOMPurify from 'dompurify';
import { HTMLAttributes } from 'react';

interface SafeHtmlProps extends HTMLAttributes<HTMLDivElement> {
  /** The HTML content to sanitize and render */
  content: string;
  /** Whether to preserve line breaks by converting \n to <br /> */
  preserveLineBreaks?: boolean;
  /** Additional DOMPurify configuration options */
  purifyOptions?: DOMPurify.Config;
}

/**
 * SafeHtml component that sanitizes HTML content before rendering to prevent XSS attacks.
 * 
 * This component uses DOMPurify to clean potentially dangerous HTML content while preserving
 * safe formatting elements like <p>, <br>, <strong>, <em>, etc.
 * 
 * @example
 * <SafeHtml content={jobListing.description} preserveLineBreaks />
 */
export function SafeHtml({ 
  content, 
  preserveLineBreaks = false, 
  purifyOptions = {}, 
  className,
  ...props 
}: SafeHtmlProps) {
  // Default DOMPurify configuration that allows common safe HTML elements
  const defaultConfig: DOMPurify.Config = {
    ALLOWED_TAGS: [
      'p', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li', 
      'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'a', 'span', 'div'
    ],
    ALLOWED_ATTR: ['href', 'title', 'target', 'rel'],
    ALLOW_DATA_ATTR: false,
    // Ensure links open safely
    ADD_ATTR: ['target'],
    FORBID_ATTR: ['style', 'class', 'id'],
    // Remove potentially dangerous protocols
    ALLOWED_URI_REGEXP: /^(?:(?:(?:f|ht)tps?|mailto|tel|callto|sms|cid|xmpp):|[^a-z]|[a-z+.-]+(?:[^a-z+.-:]|$))/i
  };

  // Merge default config with user-provided options
  const config = { ...defaultConfig, ...purifyOptions };

  // Preprocess content if line breaks should be preserved
  let processedContent = content;
  if (preserveLineBreaks && content) {
    processedContent = content.replace(/\n/g, '<br />');
  }

  // Sanitize the HTML content
  const sanitizedContent = DOMPurify.sanitize(processedContent || '', config);

  return (
    <div 
      className={className}
      dangerouslySetInnerHTML={{ __html: sanitizedContent }}
      {...props}
    />
  );
}

/**
 * Hook for sanitizing HTML content without rendering
 * 
 * @param content The HTML content to sanitize
 * @param options DOMPurify configuration options
 * @returns Sanitized HTML string
 */
export function useSanitizedHtml(content: string, options: DOMPurify.Config = {}): string {
  const defaultConfig: DOMPurify.Config = {
    ALLOWED_TAGS: [
      'p', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li', 
      'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'a', 'span', 'div'
    ],
    ALLOWED_ATTR: ['href', 'title', 'target', 'rel'],
    ALLOW_DATA_ATTR: false,
    FORBID_ATTR: ['style', 'class', 'id'],
    ALLOWED_URI_REGEXP: /^(?:(?:(?:f|ht)tps?|mailto|tel|callto|sms|cid|xmpp):|[^a-z]|[a-z+.-]+(?:[^a-z+.-:]|$))/i
  };

  const config = { ...defaultConfig, ...options };
  return DOMPurify.sanitize(content || '', config);
}