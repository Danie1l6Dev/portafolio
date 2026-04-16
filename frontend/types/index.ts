// ─────────────────────────────────────────────────────────────
// Tipos que reflejan exactamente las respuestas de la API Laravel
// ─────────────────────────────────────────────────────────────

// ── Primitivos reutilizables ──────────────────────────────────

export interface PaginationLinks {
  first: string | null;
  last: string | null;
  prev: string | null;
  next: string | null;
}

export interface PaginationMeta {
  current_page: number;
  from: number | null;
  last_page: number;
  per_page: number;
  to: number | null;
  total: number;
}

export interface PaginatedResponse<T> {
  data: T[];
  links: PaginationLinks;
  meta: PaginationMeta;
}

export interface ApiResponse<T> {
  data: T;
  message?: string;
}

// ── Entidades del portafolio ──────────────────────────────────

export interface Category {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  color: string | null;
  sort_order: number;
  /** Solo presente en endpoints admin */
  projects_count?: number;
  /** Solo presente en el endpoint público /categories */
  published_projects_count?: number;
}

export interface Skill {
  id: number;
  name: string;
  slug: string;
  group: string | null;
  level: number; // 1–5
  icon: string | null;
  sort_order: number;
  is_featured: boolean;
  projects_count?: number;
}

export interface Media {
  id: number;
  collection: string;
  url: string;
  filename: string;
  mime_type: string | null;
  is_image: boolean;
  alt: string | null;
  sort_order: number;
}

export type ProjectStatus = 'draft' | 'published' | 'archived';

export interface Project {
  id: number;
  title: string;
  slug: string;
  summary: string;
  /** Solo presente en show y rutas admin */
  description?: string | null;
  demo_url: string | null;
  repo_url: string | null;
  cover_image: string | null;
  status: ProjectStatus;
  is_featured: boolean;
  sort_order: number;
  started_at: string | null; // ISO date
  finished_at: string | null;
  in_progress: boolean;
  category?: Category | null;
  skills?: Skill[];
  media?: Media[];
}

export interface Experience {
  id: number;
  company: string;
  position: string;
  location: string | null;
  description: string | null;
  company_url: string | null;
  company_logo: string | null;
  started_at: string; // ISO date
  finished_at: string | null;
  is_current: boolean;
  duration: string; // "ene. 2021 – Presente"
  media?: Media[];
}

// ── Autenticación ─────────────────────────────────────────────

/**
 * Roles disponibles en el sistema.
 * Sincronizado con User::ROLES en el backend.
 *
 * admin  → acceso total (gestión de usuarios, configuración)
 * editor → solo gestión de contenido (proyectos, skills, experiencias)
 */
export type UserRole = 'admin' | 'editor';

export interface AuthUser {
  id: number;
  name: string;
  email: string;
  role: UserRole;
}

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface LoginResponse {
  data: {
    user: AuthUser;
    token: string;
  };
}

// ── Payloads Admin ────────────────────────────────────────────

export interface ProjectPayload {
  title?: string;
  category_id?: number | null;
  summary?: string;
  description?: string | null;
  demo_url?: string | null;
  repo_url?: string | null;
  cover_image?: File | null;
  status?: ProjectStatus;
  is_featured?: boolean;
  sort_order?: number;
  started_at?: string | null;
  finished_at?: string | null;
  skill_ids?: number[];
}

export interface CategoryPayload {
  name?: string;
  description?: string | null;
  color?: string | null;
  sort_order?: number;
}

export interface SkillPayload {
  name?: string;
  group?: string | null;
  level?: number;
  icon?: string | null;
  sort_order?: number;
  is_featured?: boolean;
}

export interface ExperiencePayload {
  company?: string;
  position?: string;
  location?: string | null;
  description?: string | null;
  company_url?: string | null;
  company_logo?: File | null;
  started_at?: string;
  finished_at?: string | null;
  is_current?: boolean;
  sort_order?: number;
}

// ── Skills meta ───────────────────────────────────────────────

export interface SkillsResponse {
  data: Skill[];
  meta: {
    groups: string[];
  };
}

// ── Blog (preparado para fase futura) ─────────────────────────

/**
 * Tipo base para entradas de blog.
 *
 * Para activar el blog:
 * 1. Backend: php artisan make:model Post -mrc (seguir el patrón de Project)
 * 2. Backend: registrar rutas GET /v1/posts y /v1/admin/posts
 * 3. Frontend: crear services/posts.ts con getPost / adminCreatePost
 * 4. Frontend: crear app/(public)/blog/page.tsx y [slug]/page.tsx
 *
 * El modelo Post debería usar:
 *   - HasSlug trait (slug único desde title)
 *   - status: 'draft' | 'published' | 'archived'
 *   - MorphMany media (para imágenes en el cuerpo)
 *   - BelongsToMany tags o BelongsTo category
 */
export type PostStatus = 'draft' | 'published' | 'archived';

export interface BlogPost {
  id: number;
  title: string;
  slug: string;
  excerpt: string;
  /** Contenido completo en Markdown, solo en show */
  content?: string | null;
  cover_image: string | null;
  status: PostStatus;
  is_featured: boolean;
  published_at: string | null; // ISO datetime
  reading_time_minutes: number | null;
  category?: Category | null;
  media?: Media[];
}

export interface BlogPostPayload {
  title?: string;
  excerpt?: string;
  content?: string | null;
  cover_image?: File | null;
  status?: PostStatus;
  is_featured?: boolean;
  category_id?: number | null;
  published_at?: string | null;
}
