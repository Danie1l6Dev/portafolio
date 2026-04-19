import { Suspense } from 'react';
import LoginClient from './LoginClient';

export const dynamic = 'force-dynamic';

function LoginFallback() {
  return (
    <div className="w-full max-w-sm animate-pulse">
      <div className="rounded-2xl border border-slate-200 bg-white p-8 shadow-[0_4px_24px_-4px_rgb(0_0_0_/_0.1)]">
        <div className="mb-6 h-6 w-40 rounded bg-slate-100" />
        <div className="space-y-3">
          <div className="h-10 rounded bg-slate-100" />
          <div className="h-10 rounded bg-slate-100" />
          <div className="h-10 rounded bg-slate-100" />
        </div>
      </div>
    </div>
  );
}

export default function LoginPage() {
  return (
    <Suspense fallback={<LoginFallback />}>
      <LoginClient />
    </Suspense>
  );
}
