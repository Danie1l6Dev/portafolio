export function Footer() {
  const year = new Date().getFullYear();

  return (
    <footer className="border-t border-gray-200 bg-white">
      <div className="mx-auto max-w-5xl px-4 py-6 text-center text-sm text-gray-500">
        © {year} Daniel Sierra. Todos los derechos reservados.
      </div>
    </footer>
  );
}
