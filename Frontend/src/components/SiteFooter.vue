<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const isAdmin = computed(() => route.path === '/admin')
</script>

<template>
    <footer :class="['site-footer', { 'admin-footer': isAdmin, 'customer-footer': !isAdmin }]">
        <div class="footer-inner">
            <div>
                <strong>{{ isAdmin ? 'Sekawarung · Panel Admin' : 'Sekawarung' }}</strong>
                <p>{{ isAdmin ? 'Kelola toko dan pantau transaksi.' : 'Belanja kebutuhan harian dengan mudah.' }}</p>
            </div>
            <div class="footer-meta">
                <span>{{ isAdmin ? 'Area internal' : 'Senin - Minggu, 07.00 - 21.00' }}</span>
                <span>© 2026 Sekawarung</span>
            </div>
        </div>
    </footer>
</template>

<style scoped>
.site-footer {
    margin-top: auto;
    border-top: 1px solid rgba(49, 91, 71, 0.08);
    background: rgba(238, 235, 227, 0.9);
}
.customer-footer { border-top-color: rgba(230, 160, 75, 0.22); background: rgba(255, 241, 217, 0.9); }
.admin-footer { border-top-color: rgba(255,255,255,0.08); background: rgba(36, 59, 49, 0.95); }
.footer-inner {
    display: flex;
    justify-content: space-between;
    gap: 24px;
    max-width: 1220px;
    margin: auto;
    padding: 25px 28px;
}
.site-footer strong { color: #315b47; font: 700 14px Arial, sans-serif; }
.customer-footer strong { color: #a45a28; }
.admin-footer strong { color: #f1c16d; }
.site-footer p, .footer-meta { color: #7a7d73; font: 11px Arial, sans-serif; }
.admin-footer p, .admin-footer .footer-meta { color: #bdcabc; }
.site-footer p { margin: 7px 0 0; }
.footer-meta {
    display: flex;
    align-items: end;
    flex-direction: column;
    gap: 6px;
    text-align: right;
}
@media (max-width: 620px) { .footer-inner { flex-direction: column; padding: 22px 16px; }.footer-meta { align-items: start; text-align: left; } }
</style>
