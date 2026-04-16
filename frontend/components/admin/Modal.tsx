'use client';

import { useEffect, useRef, type ReactNode } from 'react';

interface ModalProps {
  open: boolean;
  onClose: () => void;
  title: string;
  children: ReactNode;
  /** Ancho máximo del modal. Default: max-w-lg */
  size?: 'sm' | 'md' | 'lg' | 'xl';
}

const sizeClasses = {
  sm: 'max-w-sm',
  md: 'max-w-lg',
  lg: 'max-w-2xl',
  xl: 'max-w-3xl',
};

export function Modal({ open, onClose, title, children, size = 'md' }: ModalProps) {
  const dialogRef = useRef<HTMLDialogElement>(null);

  useEffect(() => {
    const dialog = dialogRef.current;
    if (!dialog) return;
    if (open) {
      dialog.showModal();
    } else {
      dialog.close();
    }
  }, [open]);

  // Cerrar al hacer click en el backdrop
  function handleBackdropClick(e: React.MouseEvent<HTMLDialogElement>) {
    if (e.target === dialogRef.current) onClose();
  }

  // Cerrar con Escape
  function handleCancel(e: React.SyntheticEvent) {
    e.preventDefault();
    onClose();
  }

  if (!open) return null;

  return (
    <dialog
      ref={dialogRef}
      onCancel={handleCancel}
      onClick={handleBackdropClick}
      className={`
        w-full ${sizeClasses[size]} rounded-xl border border-gray-200 bg-white p-0 shadow-xl
        backdrop:bg-gray-900/50 backdrop:backdrop-blur-sm
        open:animate-in open:fade-in open:zoom-in-95
      `}
    >
      {/* Header */}
      <div className="flex items-center justify-between border-b border-gray-100 px-6 py-4">
        <h2 className="text-base font-semibold text-gray-900">{title}</h2>
        <button
          onClick={onClose}
          className="rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors"
          aria-label="Cerrar"
        >
          ✕
        </button>
      </div>

      {/* Body */}
      <div className="px-6 py-5">{children}</div>
    </dialog>
  );
}
