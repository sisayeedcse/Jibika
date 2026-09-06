import React from 'react';

export function Logo({ className }: { className?: string }) {
  return (
    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
      <rect width="100" height="100" rx="24" fill="url(#bg_gradient)"/>
      
      {/* Coin / Sun indicating capital and growth */}
      <circle cx="68" cy="32" r="14" fill="#FBBF24" />
      
      {/* The letter J styled as a growing plant stem */}
      <path d="M36 30 V62 A 18 18 0 0 0 72 62" stroke="white" strokeWidth="12" strokeLinecap="round" />
      
      {/* A leaf sprouting from the J */}
      <path d="M30 45 C 12 45, 12 25, 30 25 C 40 30, 40 45, 30 45 Z" fill="white" />
      
      <defs>
        <linearGradient id="bg_gradient" x1="0" y1="0" x2="100" y2="100" gradientUnits="userSpaceOnUse">
          <stop stopColor="#127A4A"/>
          <stop offset="1" stopColor="#0A5C36"/>
        </linearGradient>
      </defs>
    </svg>
  );
}
