<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const open = ref(false)
const user = computed(() => JSON.parse(sessionStorage.getItem('warung_user') || '{}'))
const avatarSrc = computed(() => user.value.photo_url || user.value.photo || '')

function logout() {
    sessionStorage.removeItem('warung_user')
    router.replace('/login')
}
</script>

<template>
    <div class="profile-menu">
        <button class="profile-button" type="button" aria-label="Buka profil" @click="open = !open">
            <img v-if="avatarSrc" :src="avatarSrc" alt="Foto profil" />
            <span v-else>{{ (user.name || 'P').slice(0, 1).toUpperCase() }}</span>
        </button>
        <div v-if="open" class="profile-dropdown">
            <strong>{{ user.name || 'Pengguna' }}</strong>
            <small>{{ user.role || 'Akun' }}</small>
            <RouterLink to="/belanja" @click="open = false">Lihat warung</RouterLink>
            <button type="button" @click="logout">Keluar</button>
        </div>
    </div>
</template>

<style scoped>
.profile-menu { position: relative; }.profile-button { display: grid; place-items: center; width: 42px; height: 42px; padding: 0; border: 0; border-radius: 50%; background: #315b47; color: #fffdf8; cursor: pointer; overflow: hidden; }.profile-button img { width: 100%; height: 100%; object-fit: cover; display: block; }.profile-button span { font: 700 17px Arial, sans-serif; }.profile-dropdown { position: absolute; z-index: 10; top: 50px; right: 0; display: grid; min-width: 180px; gap: 6px; padding: 14px; border: 1px solid #dedbd0; background: #fffdf8; box-shadow: 0 5px 16px #2d38241c; }.profile-dropdown strong { font-size: 15px; }.profile-dropdown small { color: #777b70; font: 11px Arial, sans-serif; text-transform: capitalize; }.profile-dropdown a, .profile-dropdown button { padding: 8px 0; border: 0; border-top: 1px solid #e6e2d8; background: transparent; color: #315b47; text-align: left; text-decoration: none; font: 12px Arial, sans-serif; cursor: pointer; }.profile-dropdown a { margin-top: 6px; }
</style>
