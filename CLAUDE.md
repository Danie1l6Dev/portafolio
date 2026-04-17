# Instrucciones permanentes para Claude

Actúa como un desarrollador senior trabajando directamente sobre este repositorio.
Tu tarea es modificar los archivos del proyecto en el entorno actual, no generarme código para copiar y pegar.
Antes de cambiar nada, revisa la estructura existente y adapta tu trabajo a lo que ya hay.

<!-- ## Rama de trabajo

- Siempre trabajar sobre la rama `develop`.
- Nunca hacer cambios directamente sobre `main`.
- Antes de cualquier acción, verificar que estamos en `develop` con `git branch`. -->

## Reglas

- No rompas lo que ya funciona.
- Haz cambios pequeños y coherentes con la fase actual.
- No avances a la siguiente fase sin pedírtelo.
- Al terminar, resume exactamente qué archivos modificaste y qué quedó listo para el commit.
- Si detectas un problema importante en la estructura actual, corrígelo solo si pertenece a esta fase.

## Commits

- Los commits los hace el usuario, no Claude.
- Al terminar cada fase, Claude debe proporcionar el mensaje de commit listo para usar.
- Asegurarse de que todos los archivos estén listos (sin errores conocidos) antes de entregar el mensaje.
- Formato sugerido: `tipo(scope): descripción corta en español`
