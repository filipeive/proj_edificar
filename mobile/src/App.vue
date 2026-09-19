<script setup lang="ts">
import { computed, ref } from 'vue'
import { dashboard, login, logout, type User } from './services/api'

const email = ref('')
const password = ref('')
const user = ref<User | null>(null)
const metrics = ref<Record<string, unknown> | null>(null)
const error = ref('')
const loading = ref(false)

const authenticated = computed(() => !!user.value)

async function signIn() {
  error.value = ''
  loading.value = true

  try {
    const result = await login(email.value, password.value)
    user.value = result.user
    const dashboardResponse = await dashboard()
    metrics.value = dashboardResponse.data
  } catch (exception) {
    error.value = exception instanceof Error ? exception.message : 'Não foi possível iniciar sessão.'
  } finally {
    loading.value = false
  }
}

async function signOut() {
  try {
    await logout()
  } finally {
    user.value = null
    metrics.value = null
  }
}
</script>

<template>
  <main class="app-shell">
    <section v-if="!authenticated" class="auth-card">
      <div class="brand">EDIFICAR</div>
      <p class="subtitle">Gestão eclesiástica</p>

      <form @submit.prevent="signIn">
        <label>
          E-mail
          <input v-model="email" type="email" autocomplete="username" required />
        </label>

        <label>
          Palavra-passe
          <input v-model="password" type="password" autocomplete="current-password" required />
        </label>

        <p v-if="error" class="error">{{ error }}</p>

        <button :disabled="loading" type="submit">
          {{ loading ? 'A entrar…' : 'Entrar' }}
        </button>
      </form>
    </section>

    <section v-else class="dashboard">
      <header class="topbar">
        <div>
          <strong>{{ user?.name }}</strong>
          <span>{{ user?.role }}</span>
        </div>
        <button class="secondary" @click="signOut">Sair</button>
      </header>

      <div class="welcome">
        <h1>Olá, {{ user?.name?.split(' ')[0] }}.</h1>
        <p>Este é o primeiro núcleo da app Android do Edificar.</p>
      </div>

      <div v-if="metrics" class="metrics">
        <article v-for="(value, key) in metrics" :key="key">
          <span>{{ String(key).replaceAll('_', ' ') }}</span>
          <strong>{{ value }}</strong>
        </article>
      </div>
    </section>
  </main>
</template>