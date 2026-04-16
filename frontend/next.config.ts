import type { NextConfig } from 'next';

const API_HOST = process.env.NEXT_PUBLIC_API_URL
  ? new URL(process.env.NEXT_PUBLIC_API_URL).hostname
  : 'localhost';

const nextConfig: NextConfig = {
  images: {
    remotePatterns: [
      {
        protocol: 'http',
        hostname: API_HOST,
        port: '8000',
        pathname: '/storage/**',
      },
      {
        protocol: 'https',
        hostname: API_HOST,
        pathname: '/storage/**',
      },
    ],
  },
};

export default nextConfig;
