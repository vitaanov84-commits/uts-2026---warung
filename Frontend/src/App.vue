<script setup>
import SiteFooter from './components/SiteFooter.vue'
import SiteNav from './components/SiteNav.vue'
</script>

<template>
  <div class="app-frame">
    <SiteNav />
    <div class="page-content"><router-view /></div>
    <SiteFooter />
  </div>
</template>

<style>
html { scroll-behavior: smooth; }
body {
  background: linear-gradient(180deg, #f7f4ee 0%, #f1ecdf 100%);
}
#app, .app-frame { min-height: 100vh; }
.app-frame {
  display: flex;
  flex-direction: column;
  background:
    radial-gradient(circle at top left, rgba(213, 226, 204, 0.7), transparent 28%),
    radial-gradient(circle at bottom right, rgba(241, 193, 109, 0.16), transparent 30%),
    transparent;
}
.page-content { flex: 1; }
</style>