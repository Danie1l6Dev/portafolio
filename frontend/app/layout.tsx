import type { Metadata } from 'next';
import { Geist, Geist_Mono } from 'next/font/google';
import { SITE } from '@/lib/constants';
import './globals.css';

const geistSans = Geist({
  variable: '--font-geist-sans',
  subsets: ['latin'],
});

const geistMono = Geist_Mono({
  variable: '--font-geist-mono',
  subsets: ['latin'],
});

export const metadata: Metadata = {
  title: {
    default: `${SITE.author} — Portafolio`,
    template: `%s | ${SITE.author}`,
  },
  description: SITE.description,
  metadataBase: new URL(SITE.url),
  openGraph: {
    locale: SITE.locale,
    type: 'website',
    siteName: SITE.name,
  },
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html
      lang="es"
      className={`${geistSans.variable} ${geistMono.variable} h-full antialiased`}
    >
      <body className="flex min-h-full flex-col bg-[#F0F7FF] text-gray-900">
        {children}
      </body>
    </html>
  );
}
