<script setup>
import { computed, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const menuOpen = ref(false)
const user = computed(() => JSON.parse(sessionStorage.getItem('warung_user') || 'null'))
const avatarSrc = computed(() => user.value?.photo_url || user.value?.photo || '')
const isLogin = computed(() => route.path === '/login')
const isAdmin = computed(() => !isLogin.value && (user.value?.role === 'admin' || route.path === '/admin'))
const homePath = computed(() => user.value?.role === 'admin' ? '/admin' : '/belanja')

function logout() {
    sessionStorage.removeItem('warung_user')
    menuOpen.value = false
    router.replace('/login')
}
</script>

<template>
    <nav :class="['site-nav', { 'admin-nav': isAdmin, 'customer-nav': !isAdmin }]">
        <div class="nav-inner">
            <RouterLink class="brand" :to="homePath">
                <span class="brand-mark">W</span>
                <span v-if="isAdmin">PANEL ADMIN</span>
                <span v-else>SEKAWARUNG</span>
            </RouterLink>

            <div v-if="!isLogin" class="nav-links">
                <template v-if="isAdmin">
                    <RouterLink :class="{ active: route.path === '/admin' && (!route.hash || route.hash === '#ringkasan') }" to="/admin#ringkasan">Ringkasan</RouterLink>
                    <a :class="{ active: route.hash === '#produk' }" href="/admin#produk">Produk</a>
                    <a :class="{ active: route.hash === '#keuangan' }" href="/admin#keuangan">Keuangan</a>
                </template>
            </div>

            <div class="nav-account">
                <RouterLink v-if="isLogin || !user" class="login-link" to="/login">Masuk</RouterLink>
                <div v-else class="account-wrap">
                    <button class="account-button" type="button" aria-label="Buka menu akun" @click="menuOpen = !menuOpen">
                        <span v-if="avatarSrc" class="avatar-image"><img :src="avatarSrc" alt="Foto profil" /></span>
                        <span v-else class="avatar-letter">{{ user.name.slice(0, 1).toUpperCase() }}</span>
                        <b>{{ user.name }}</b>
                    </button>
                    <div v-if="menuOpen" class="account-menu">
                        <small>{{ user.role }}</small>
                        <RouterLink to="/belanja" @click="menuOpen = false">Lihat warung</RouterLink>
                        <button type="button" @click="logout">Keluar</button>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<style scoped>
.site-nav {
    position: relative;
    z-index: 20;
    border-bottom: 1px solid rgba(49, 91, 71, 0.08);
    background: rgba(255, 253, 248, 0.82);
    backdrop-filter: blur(12px);
}

.customer-nav { background: rgba(255, 247, 230, 0.82); border-bottom-color: rgba(230, 160, 75, 0.22); }
.admin-nav { background: rgba(36, 59, 49, 0.92); border-bottom-color: rgba(255,255,255,0.08); }
.nav-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 28px;
    max-width: 1220px;
    margin: auto;
    padding: 17px 28px;
}

.brand {
    display: flex;
    align-items: center;
    gap: 9px;
    color: #26362b;
    font: 700 15px Arial, sans-serif;
    text-decoration: none;
}

.customer-nav .brand { color: #a45a28; }
.admin-nav .brand { color: #f9f5ec; }
.guard-nav .brand { color: #2d4936; }

.brand-mark {
    display: grid;
    place-items: center;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: linear-gradient(135deg, #315b47 0%, #1d3d34 100%);
    color: #fffdf8;
    font: 700 14px Georgia, serif;
}

.customer-nav .brand-mark { background: linear-gradient(135deg, #e6a04b 0%, #c87b33 100%); color: #3c2b1c; }
.admin-nav .brand-mark { background: linear-gradient(135deg, #f1c16d 0%, #d99a40 100%); color: #2e271b; }

.nav-links {
    display: flex;
    gap: 24px;
    margin-left: auto;
    align-items: center;
}

.nav-links a, .login-link {
    color: #6b7068;
    font: 12px Arial, sans-serif;
    text-decoration: none;
}

.customer-nav .nav-links a { padding: 7px 11px; border-radius: 999px; color: #795b3c; }
.customer-nav .nav-links a.active, .customer-nav .nav-links a:hover { background: rgba(243, 223, 188, 0.8); color: #8b4f25; }
.admin-nav .nav-links a, .admin-nav .login-link { color: #c9d4c9; }
.admin-nav .nav-links a.active, .admin-nav .nav-links a:hover, .admin-nav .login-link:hover { color: #f1c16d; }
.nav-links a.active, .nav-links a:hover, .login-link:hover { color: #315b47; }

.nav-account { display: flex; align-items: center; }
.account-wrap { position: relative; }
.account-button {
    display: flex;
    align-items: center;
    gap: 8px;
    border: 0;
    background: transparent;
    color: #394139;
    cursor: pointer;
    font: 12px Arial, sans-serif;
}
.admin-nav .account-button { color: #f9f5ec; }

.avatar-letter, .avatar-image {
    display: grid;
    place-items: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #315b47 0%, #1d3d34 100%);
    color: #fffdf8;
    font: 700 11px Arial, sans-serif;
    overflow: hidden;
}

.avatar-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.account-menu {
    position: absolute;
    top: 42px;
    right: 0;
    display: grid;
    min-width: 180px;
    gap: 8px;
    padding: 12px 14px;
    border: 1px solid rgba(49, 91, 71, 0.08);
    border-radius: 14px;
    background: rgba(255,255,255,0.98);
    box-shadow: 0 18px 28px rgba(36, 42, 32, 0.12);
}
.account-menu small {
    color: #6a726b;
    font: 11px Arial, sans-serif;
    text-transform: capitalize;
}
.account-menu a, .account-menu button {
    padding: 8px 0;
    border: 0;
    border-top: 1px solid #ece5d9;
    background: transparent;
    color: #315b47;
    text-align: left;
    text-decoration: none;
    font: 12px Arial, sans-serif;
    cursor: pointer;
}

@media (max-width: 620px) {
    .nav-inner { padding: 14px 16px; gap: 12px; }
    .brand > span:last-child { display: none; }
    .nav-inner { flex-wrap: wrap; }
    .nav-links { order: 3; width: 100%; justify-content: space-between; gap: 8px; margin-left: 0; }
    .nav-links a { font-size: 11px; }
    .account-button b { display: none; }
}
</style>
