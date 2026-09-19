export interface ApiResponse<T> {
  success: boolean
  message: string
  data: T
  meta?: Record<string, unknown>
  errors?: unknown
}

export interface User {
  id: number
  name: string
  email: string
  phone?: string | null
  role: string
  cell_id?: number | null
  is_active: boolean
}

const API_BASE_URL = (import.meta.env.VITE_API_BASE_URL || '/api/v1').replace(/\\/$/, '')

let token: string | null = null

export function setToken(value: string | null) {
  token = value
}

export function getToken() {
  return token
}

async function request<T>(path: string, options: RequestInit = {}): Promise<ApiResponse<T>> {
  const headers = new Headers(options.headers)
  headers.set('Accept', 'application/json')
  headers.set('Content-Type', 'application/json')

  if (token) headers.set('Authorization', `Bearer ${token}`)

  const response = await fetch(`${API_BASE_URL}${path}`, {
    ...options,
    headers,
  })

  const payload = await response.json().catch(() => null)

  if (!response.ok) {
    throw new Error(payload?.message || `HTTP ${response.status}`)
  }

  return payload
}

export async function login(email: string, password: string, deviceName = 'Edificar Android') {
  const result = await request<{ token: string; user: User }>('/login', {
    method: 'POST',
    body: JSON.stringify({ email, password, device_name: deviceName }),
  })
  setToken(result.data.token)
  return result.data
}

export async function logout() {
  if (!token) return
  await request('/logout', { method: 'POST' })
  setToken(null)
}

export async function profile() {
  return request<User>('/profile')
}

export async function dashboard() {
  return request<Record<string, unknown>>('/dashboard')
}