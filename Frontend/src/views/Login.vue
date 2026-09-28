<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { api } from '../lib/api'

const router = useRouter()
const form = ref({ email: '', password: '' })
const loading = ref(false)
const error = ref('')

async function login() {
    loading.value = true
    error.value = ''
    try {
        const response = await api.login(form.value)
        sessionStorage.setItem('warung_user', JSON.stringify(response.data))
        const destinations = { admin: '/admin', pembeli: '/belanja' }
        await router.replace(destinations[response.data.role] || '/belanja')
    } catch (exception) {
        error.value = exception.message
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <main class="login-page">
        <section class="login-card">
            <div class="card-head">
                <p class="kicker">SEKAWARUNG</p>
                <h1>Masuk ke akunmu</h1>
                <p class="subtitle">Masuk untuk mengelola warung atau mulai berbelanja dengan cepat.</p>
            </div>

            <form @submit.prevent="login">
                <label>
                    Email
                    <input v-model="form.email" required type="email" placeholder="nama@warung.test" />
                </label>
                <label>
                    Password
                    <input v-model="form.password" required type="password" placeholder="Password" />
                </label>
                <p v-if="error" class="error">{{ error }}</p>
                <button :disabled="loading" type="submit">{{ loading ? 'Memeriksa...' : 'Masuk' }}</button>
            </form>

            <RouterLink class="shop-link" to="/belanja">Lanjut belanja sebagai tamu</RouterLink>
        </section>
    </main>
</template>

<style scoped>
:global(*) { box-sizing: border-box; }
:global(body) {
    margin: 0;
    background: linear-gradient(135deg, #f5efe5 0%, #eef4ee 100%);
    color: #1f2a24;
    font-family: 'Segoe UI', Arial, sans-serif;
}

.login-page {
    min-height: calc(100vh - 100px);
    display: grid;
    place-items: center;
    padding: 20px 16px;
    background:
        radial-gradient(circle at top left, rgba(211, 224, 204, 0.9), transparent 28%),
        radial-gradient(circle at bottom right, rgba(244, 195, 110, 0.22), transparent 25%),
        #f5efe5;
}

.login-card {
    width: min(100%, 420px);
    padding: clamp(22px, 4vw, 32px);
    border: 1px solid rgba(49, 91, 71, 0.08);
    border-radius: 24px;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(8px);
    box-shadow: 0 20px 38px rgba(36, 42, 32, 0.12);
}

.kicker {
    margin: 0 0 12px;
    color: #487057;
    font: 700 11px Arial, sans-serif;
    letter-spacing: 2px;
}

.login-card h1 {
    margin: 0;
    font-size: clamp(32px, 4vw, 42px);
    line-height: 1.05;
}

.subtitle {
    margin: 12px 0 24px;
    color: #5f675f;
    font-size: 15px;
    line-height: 1.6;
}

.login-card form {
    display: grid;
    gap: 16px;
}

label {
    display: grid;
    gap: 8px;
    color: #495348;
    font: 12px Arial, sans-serif;
    font-weight: 700;
}

input, select {
    width: 100%;
    padding: 12px 13px;
    border: 1px solid #cfc7b8;
    border-radius: 12px;
    background: #fffdf9;
    color: #1f2a24;
    font: 14px Arial, sans-serif;
}

button {
    width: 100%;
    margin-top: 6px;
    padding: 14px 12px;
    border: 0;
    border-radius: 12px;
    background: linear-gradient(135deg, #315b47 0%, #1d3d34 100%);
    color: white;
    font: 700 14px Arial, sans-serif;
    cursor: pointer;
    box-shadow: 0 16px 26px rgba(49, 91, 71, 0.18);
}

button:disabled { opacity: .6; cursor: not-allowed; }

.error {
    margin: 0;
    padding: 11px 12px;
    border-radius: 10px;
    background: #f8e2de;
    color: #853528;
    font: 12px Arial, sans-serif;
}

.shop-link {
    display: block;
    margin-top: 20px;
    color: #315b47;
    text-align: center;
    text-decoration: none;
    font: 13px Arial, sans-serif;
    font-weight: 700;
}
</style>
