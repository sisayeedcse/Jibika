import { ImageResponse } from 'next/og'

export const size = {
  width: 100,
  height: 100,
}
export const contentType = 'image/png'

export default function Icon() {
  return new ImageResponse(
    (
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
        <rect width="100" height="100" rx="24" fill="#0A5C36" />
        <circle cx="68" cy="32" r="14" fill="#FBBF24" />
        <path d="M36 30 V62 A 18 18 0 0 0 72 62" stroke="white" strokeWidth="12" strokeLinecap="round" />
        <path d="M30 45 C 12 45, 12 25, 30 25 C 40 30, 40 45, 30 45 Z" fill="white" />
      </svg>
    ),
    {
      ...size,
    }
  )
}
