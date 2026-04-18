import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';
import { TOKEN_COOKIE } from '@/lib/constants';

// Rutas que requieren autenticación
const PROTECTED_PREFIXES = ['/admin'];

// Rutas públicas dentro del grupo admin (no requieren auth)
const PUBLIC_ADMIN_ROUTES = ['/login'];

export function middleware(request: NextRequest) {
  const { pathname } = request.nextUrl;

  const isProtected = PROTECTED_PREFIXES.some((prefix) =>
    pathname.startsWith(prefix),
  );

  if (!isProtected) return NextResponse.next();

  const token = request.cookies.get(TOKEN_COOKIE)?.value;

  if (!token) {
    const loginUrl = new URL('/login', request.url);
    loginUrl.searchParams.set('next', pathname);
    return NextResponse.redirect(loginUrl);
  }

  return NextResponse.next();
}

export const config = {
  // Aplica el middleware solo a las rutas /admin/*
  // Excluye archivos estáticos y rutas de Next.js internals
  matcher: [
    '/admin/:path*',
  ],
};
