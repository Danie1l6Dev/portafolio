/**
 * loading.tsx — feedback inmediato al navegar entre páginas públicas.
 *
 * Next.js muestra este componente instantáneamente (Suspense boundary)
 * mientras el Server Component de la nueva página termina de renderizar.
 * Sin este archivo, el usuario no ve ningún cambio hasta que el servidor
 * responde, lo que hace que la navegación parezca una recarga.
 */
export default function Loading() {
  return (
    <div className="mx-auto max-w-5xl animate-pulse px-4 py-14">
      {/* Encabezado de sección */}
      <div className="mb-10 flex flex-col items-center gap-3">
        <div className="h-3 w-20 rounded-full bg-sky-100" />
        <div className="h-7 w-48 rounded-lg bg-slate-200" />
        <div className="h-3 w-72 rounded-full bg-slate-100" />
      </div>

      {/* Bloque de contenido */}
      <div className="space-y-4">
        <div className="h-4 w-full rounded bg-slate-100" />
        <div className="h-4 w-5/6 rounded bg-slate-100" />
        <div className="h-4 w-4/6 rounded bg-slate-100" />
      </div>

      {/* Cuadrícula de tarjetas */}
      <div className="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3">
        {[0, 1, 2].map((i) => (
          <div key={i} className="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div className="aspect-video bg-slate-100" />
            <div className="space-y-2 p-4">
              <div className="h-3 w-1/3 rounded-full bg-slate-200" />
              <div className="h-4 w-3/4 rounded bg-slate-200" />
              <div className="h-3 w-full rounded bg-slate-100" />
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
