import { NextResponse } from 'next/server';

// La autenticacion del admin se valida en cliente contra /auth/me con Bearer token.
// Este middleware ya no hace controles de acceso basados en cookies.
export function middleware() {
  return NextResponse.next();
}

export const config = {
  matcher: ['/admin/:path*'],
};
