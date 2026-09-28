<script setup>
import { computed, onMounted, ref } from 'vue'
import { api } from '../lib/api'

const products = ref([])
const cart = ref([])
const history = ref([])
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const result = ref(null)
const paymentMethod = ref('bca')
const searchTerm = ref('')

const paymentLabel = (method) => ({
    bca: 'BCA',
    dana: 'DANA',
    gopay: 'GoPay',
    cash: 'Tunai',
    transfer: 'Transfer',
    qris: 'QRIS',
}[method] || method)

const total = computed(() => cart.value.reduce((sum, item) => sum + Number(item.price) * item.quantity, 0))
const cartItemCount = computed(() => cart.value.reduce((sum, item) => sum + item.quantity, 0))
const filteredProducts = computed(() => {
    const keyword = searchTerm.value.trim().toLowerCase()
    return products.value.filter((product) => !keyword || product.name.toLowerCase().includes(keyword))
})
const money = (value) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value || 0)
const dateLabel = (value) => new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value))
const productPhoto = (product) => {
    const name = String(product?.name || '').toLowerCase()
    if (name.includes('soto')) return 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=900&q=80'
    if (name.includes('sate')) return 'https://images.unsplash.com/photo-1529563021893-cc83c992d75d?auto=format&fit=crop&w=900&q=80'
    if (name.includes('gado')) return 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=900&q=80'
    if (name.includes('roti')) return 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=900&q=80'
    if (name.includes('pisang')) return 'https://images.unsplash.com/photo-1623653387945-2fd25214f8fc?auto=format&fit=crop&w=900&q=80'
    if (name.includes('jeruk')) return 'https://images.unsplash.com/photo-1557800636-894a64c1696f?auto=format&fit=crop&w=900&q=80'
    if (name.includes('kopi')) return 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=900&q=80'
    if (name.includes('nasi')) return 'https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&w=900&q=80'
    if (name.includes('bakso')) return 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=900&q=80'
    if (name.includes('mie')) return 'https://images.unsplash.com/photo-1557872943-16a0ad4f0a13?auto=format&fit=crop&w=900&q=80'
    if (name.includes('teh') || name.includes('minum') || name.includes('es')) return 'https://images.unsplash.com/photo-1544145945-f90425340c7e?auto=format&fit=crop&w=900&q=80'
    if (name.includes('ayam') || name.includes('geprek')) return 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?auto=format&fit=crop&w=900&q=80'
    if (name.includes('kentang') || name.includes('goreng')) return 'https://images.unsplash.com/photo-1576101381051-9f6f68bd0b89?auto=format&fit=crop&w=900&q=80'
    return 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=900&q=80'
}

async function loadProducts() {
    loading.value = true
    error.value = ''
    try {
        const response = await api.products()
        products.value = response.data || []
    } catch (exception) {
        error.value = exception.message
    } finally {
        loading.value = false
    }
}

async function loadHistory() {
    try {
        const response = await api.orders()
        history.value = Array.isArray(response) ? response : response.data || []
    } catch (exception) {
        history.value = []
    }
}

function add(product) {
    const item = cart.value.find((cartItem) => cartItem.id === product.id)
    if (item) {
        if (item.quantity < product.stock) item.quantity += 1
        return
    }
    cart.value.push({ ...product, quantity: 1 })
}

function decrease(item) {
    if (item.quantity === 1) {
        cart.value = cart.value.filter((cartItem) => cartItem.id !== item.id)
        return
    }
    item.quantity -= 1
}

function clearCart() {
    cart.value = []
}

async function pay() {
    if (!cart.value.length) return
    saving.value = true
    error.value = ''
    result.value = null
    try {
        const response = await api.checkout({
            items: cart.value.map((item) => ({ product_id: item.id, quantity: item.quantity })),
            payment_method: paymentMethod.value,
            payment_status: 'success',
        })
        result.value = { success: true, message: response.message, order: response.data }
        cart.value = []
        await Promise.all([loadProducts(), loadHistory()])
    } catch (exception) {
        result.value = { success: false, message: exception.message }
    } finally {
        saving.value = false
    }
}

onMounted(async () => {
    await Promise.all([loadProducts(), loadHistory()])
})
</script>

<template>
    <main class="storefront">
        <header class="store-header">
            <div class="hero-copy">
                <p class="kicker">SEKAWARUNG</p>
                <h1>Belanja harian, lebih mudah dan lebih nikmat.</h1>
                <p class="intro">Temukan kebutuhan rumah tangga favoritmu dengan harga pas, stok terjamin, dan checkout yang cepat.</p>
                <div class="hero-actions">
                    <a href="#produk" class="primary-link">Lihat produk</a>
                </div>
            </div>

            <div class="hero-panel">
                <div class="mini-stat">
                    <span>Hari ini</span>
                    <strong>1.240+</strong>
                    <small>pembeli aktif</small>
                </div>
                <div class="mini-cta">
                    <span>Fast & fresh</span>
                    <strong>Pengiriman cepat</strong>
                </div>
            </div>
        </header>

        <p v-if="loading" class="message neutral">Memuat produk...</p>
        <p v-if="error" class="message error">{{ error }}</p>
        <div v-if="result" :class="['message', result.success ? 'success' : 'error']">
            <strong>{{ result.success ? 'Pesanan berhasil dibuat' : 'Pesanan belum berhasil' }}</strong>
            <span>{{ result.message }}</span>
            <small v-if="result.success">Nomor pesanan: #{{ result.order.id }}</small>
        </div>

        <section class="shop-layout">
            <div class="catalog">
                <section id="produk" class="product-section" aria-labelledby="product-section-title">
                    <div class="section-title">
                        <div>
                            <p class="kicker">PILIHAN HARI INI</p>
                            <h2 id="product-section-title">Produk untukmu</h2>
                        </div>
                        <input v-model="searchTerm" class="search-input" type="search" placeholder="Cari produk" />
                    </div>

                    <div class="product-grid">
                        <article v-for="product in filteredProducts" :key="product.id" class="product-card">
                            <div class="product-art">
                                <img :src="productPhoto(product)" :alt="product.name" />
                            </div>
                            <div class="product-info">
                                <h3>{{ product.name }}</h3>
                                <p>{{ money(product.price) }}</p>
                                <small>{{ product.stock ? `${product.stock} tersedia` : 'Stok habis' }}</small>
                                <button type="button" :disabled="!product.stock" @click="add(product)">{{ product.stock ? 'Tambah ke keranjang' : 'Stok habis' }}</button>
                            </div>
                        </article>
                    </div>
                    <p v-if="!filteredProducts.length && !loading" class="empty">Produk yang dicari belum tersedia.</p>
                </section>

                <section v-if="result?.success && result.order" class="receipt-box">
                    <div class="receipt-head">
                        <div>
                            <p class="kicker">STRUK PEMBAYARAN</p>
                            <h2>{{ result.order.order_code || `WS-${String(result.order.id).padStart(6, '0')}` }}</h2>
                        </div>
                        <span>{{ result.order.payment?.status === 'success' ? 'Lunas' : 'Diproses' }}</span>
                    </div>

                    <div class="receipt-meta-grid">
                        <div>
                            <span>Kode pelanggan</span>
                            <strong>{{ result.order.customer_code || `CUST-${String(result.order.id).padStart(6, '0')}` }}</strong>
                        </div>
                        <div>
                            <span>Kode order</span>
                            <strong>{{ result.order.order_code || `WS-${String(result.order.id).padStart(6, '0')}` }}</strong>
                        </div>
                    </div>

                    <div class="receipt-items">
                        <article v-for="item in (result.order.items || cart)" :key="item.id || item.product_id || item.name" class="receipt-item">
                            <div>
                                <strong>{{ item.product_name || item.name }}</strong>
                                <small>{{ item.quantity }} x {{ money(Number(item.unit_price || item.price)) }}</small>
                            </div>
                            <b>{{ money(Number(item.subtotal || (Number(item.unit_price || item.price) * Number(item.quantity || 1)))) }}</b>
                        </article>
                    </div>

                    <div class="receipt-total">
                        <span>Metode pembayaran</span>
                        <strong>{{ paymentLabel(result.order.payment?.method || paymentMethod) }}</strong>
                    </div>
                    <div class="receipt-total grand-total">
                        <span>Total</span>
                        <strong>{{ money(Number(result.order.total || total)) }}</strong>
                    </div>
                </section>

                <section v-if="history.length" class="history-box">
                    <div class="history-head">
                        <div>
                            <p class="kicker">HISTORI PEMBELIAN</p>
                            <h2>Riwayat pembelianmu</h2>
                        </div>
                    </div>

                    <div class="history-list">
                        <article v-for="order in history.slice(0, 5)" :key="order.id" class="history-row">
                            <div>
                                <strong>{{ order.order_code || `WS-${String(order.id).padStart(6, '0')}` }}</strong>
                                <small>{{ dateLabel(order.created_at) }}</small>
                            </div>
                            <div class="history-meta">
                                <span>{{ order.customer_code || `CUST-${String(order.id).padStart(6, '0')}` }}</span>
                                <b>{{ money(Number(order.total)) }}</b>
                            </div>
                        </article>
                    </div>
                </section>

            </div>

            <aside class="cart-panel">
                <div class="cart-heading">
                    <div class="section-title compact">
                        <p class="kicker">PESANANMU</p>
                        <h2>Keranjang <span>{{ cartItemCount }}</span></h2>
                    </div>
                    <button v-if="cart.length" class="clear-cart" type="button" @click="clearCart">Kosongkan</button>
                </div>

                <div v-if="cart.length" class="cart-items">
                    <article v-for="item in cart" :key="item.id" class="cart-item">
                        <div class="cart-item-info">
                            <div class="cart-thumb">{{ item.name.slice(0, 1).toUpperCase() }}</div>
                            <div>
                                <strong>{{ item.name }}</strong>
                                <small>{{ money(item.price) }} per item</small>
                                <b>{{ money(Number(item.price) * item.quantity) }}</b>
                            </div>
                        </div>
                        <div class="quantity">
                            <button type="button" aria-label="Kurangi jumlah" @click="decrease(item)">-</button>
                            <b>{{ item.quantity }}</b>
                            <button type="button" aria-label="Tambah jumlah" :disabled="item.quantity >= item.stock" @click="add(item)">+</button>
                        </div>
                    </article>
                </div>

                <div v-else class="empty-cart">
                    <div class="empty-cart-icon">+</div>
                    <strong>Keranjang masih kosong</strong>
                    <p>Pilih produk untuk mulai berbelanja.</p>
                </div>

                <div class="cart-footer">
                    <div class="total-line">
                        <span>Total pembayaran</span>
                        <strong>{{ money(total) }}</strong>
                    </div>
                    <label>Metode pembayaran
                        <select v-model="paymentMethod">
                            <option value="bca">BCA</option>
                            <option value="dana">DANA</option>
                            <option value="gopay">GoPay</option>
                        </select>
                    </label>
                    <button class="pay-button" :disabled="saving || !cart.length" type="button" @click="pay">
                        {{ saving ? 'Memproses...' : 'Konfirmasi pembayaran' }}
                    </button>
                    <small class="secure-note">Pembayaran diproses dengan aman</small>
                </div>
            </aside>
        </section>

    </main>
</template>

<style scoped>
:global(*) { box-sizing: border-box; }
:global(body) {
    margin: 0;
    background: linear-gradient(180deg, #f7f4ee 0%, #f1ecdf 100%);
    color: #1f2a24;
    font-family: 'Segoe UI', Arial, sans-serif;
}
:global(button), :global(input), :global(select) { font: inherit; }

.storefront {
    max-width: 1220px;
    margin: 0 auto;
    padding: 34px 28px 56px;
}

.store-header {
    display: grid;
    grid-template-columns: minmax(0, 1.3fr) minmax(230px, 340px);
    gap: 24px;
    align-items: stretch;
    margin-bottom: 26px;
}

.hero-copy {
    padding: 26px 26px 20px;
    border: 1px solid rgba(49, 91, 71, 0.08);
    border-radius: 28px;
    background: linear-gradient(135deg, #fffdf9 0%, #eef6ee 100%);
    box-shadow: 0 18px 40px rgba(48, 62, 47, 0.08);
}

.kicker {
    margin: 0 0 10px;
    color: #5f7b63;
    font: 700 11px Arial, sans-serif;
    letter-spacing: 2px;
}

.store-header h1 {
    max-width: 650px;
    margin: 0;
    font-size: clamp(38px, 5vw, 68px);
    line-height: 0.95;
    letter-spacing: -1.3px;
    color: #1f2a24;
}

.intro {
    max-width: 560px;
    margin: 18px 0 0;
    color: #5d635d;
    font-size: 17px;
    line-height: 1.6;
}

.hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 22px;
}

.primary-link, .secondary-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 44px;
    padding: 0 18px;
    border-radius: 999px;
    text-decoration: none;
    font: 700 13px Arial, sans-serif;
}

.primary-link {
    background: linear-gradient(135deg, #315b47 0%, #1e3d35 100%);
    color: #fff;
    box-shadow: 0 12px 20px rgba(49, 91, 71, 0.2);
}

.secondary-link {
    border: 1px solid rgba(49, 91, 71, 0.25);
    color: #315b47;
    background: rgba(255, 255, 255, 0.5);
}

.hero-panel {
    display: grid;
    gap: 16px;
    padding: 22px;
    border-radius: 24px;
    background: linear-gradient(180deg, #243b31 0%, #1a2f2b 100%);
    color: #f5f0ea;
    box-shadow: 0 18px 30px rgba(27, 49, 41, 0.18);
}

.mini-stat, .mini-cta {
    display: grid;
    gap: 6px;
    padding: 18px 18px 16px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.mini-stat span, .mini-cta span {
    color: #c6d5c9;
    font: 11px Arial, sans-serif;
    text-transform: uppercase;
    letter-spacing: 1.5px;
}

.mini-stat strong, .mini-cta strong {
    font-size: 28px;
    line-height: 1;
}

.mini-stat small {
    color: #dfe8dd;
    font: 12px Arial, sans-serif;
}

.shop-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 360px;
    gap: 18px;
    align-items: start;
}

.catalog { min-width: 0; }

.product-section {
    padding: 18px;
    border: 1px solid rgba(49, 91, 71, 0.08);
    border-radius: 24px;
    background: rgba(255,255,255,0.34);
}

.section-title {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 18px;
}

.section-title h2 {
    margin: 0;
    font-size: clamp(26px, 2vw, 34px);
    color: #1f2a24;
}

.search-input {
    width: 180px;
    padding: 11px 14px;
    border: 1px solid #d9d1c3;
    border-radius: 12px;
    background: rgba(255,255,255,0.7);
    color: #212b25;
    font: 13px Arial, sans-serif;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}

.product-card {
    display: grid;
    grid-template-columns: 94px 1fr;
    gap: 16px;
    padding: 16px;
    border: 1px solid rgba(49, 91, 71, 0.08);
    border-radius: 22px;
    background: rgba(255,255,255,0.78);
    box-shadow: 0 12px 24px rgba(47, 60, 42, 0.04);
}

.product-art {
    display: grid;
    place-items: center;
    min-height: 145px;
    overflow: hidden;
    border-radius: 16px;
    background: linear-gradient(135deg, #e4f0e6 0%, #d3e0d5 100%);
}

.product-art img {
    width: 100%;
    height: 100%;
    min-height: 145px;
    object-fit: cover;
    display: block;
}

.product-info { padding: 6px 0; }
.product-info h3 { margin: 0 0 8px; font-size: 22px; }
.product-info p {
    margin: 0 0 10px;
    color: #b5652a;
    font-weight: 800;
    font-size: 17px;
}
.product-info small, .cart-item small { color: #637066; font: 12px Arial, sans-serif; }
.product-info button {
    display: block;
    width: 100%;
    margin-top: 15px;
    padding: 11px 10px;
    border: 0;
    border-radius: 12px;
    background: linear-gradient(135deg, #315b47 0%, #284837 100%);
    color: #fff;
    cursor: pointer;
    font: 700 12px Arial, sans-serif;
}
.product-info button:hover { filter: brightness(1.04); }
.product-info button:disabled { opacity: .5; cursor: not-allowed; }

.store-notes {
    display: grid;
    grid-template-columns: 1fr;
    gap: 18px;
    margin-top: 46px;
}

.note-card {
    display: flex;
    gap: 18px;
    padding: 24px 22px;
    border-radius: 22px;
    background: linear-gradient(135deg, #edf7ec 0%, #fefcf8 100%);
    border: 1px solid rgba(49, 91, 71, 0.08);
}

.note-card-dark {
    background: linear-gradient(135deg, #2d5247 0%, #1d362d 100%);
    color: #f7f5f0;
}

.note-card h2 {
    max-width: 300px;
    margin: 0 0 8px;
    font-size: 26px;
    line-height: 1.1;
}

.note-card p:last-child {
    max-width: 320px;
    margin: 0;
    color: #58615d;
    font: 13px/1.6 Arial, sans-serif;
}

.note-card-dark p:last-child { color: #dce7dd; }

.note-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(182, 101, 42, 0.12);
    color: #b5652a;
    font: 700 12px Arial, sans-serif;
}

.note-card-dark .note-number { background: rgba(255,255,255,0.08); color: #f1d295; }

.cart-panel {
    position: sticky;
    top: 20px;
    padding: 24px 20px 18px;
    border-radius: 24px;
    background: linear-gradient(180deg, #233d33 0%, #1a2f2a 100%);
    color: #f8f5ee;
    box-shadow: 0 22px 40px rgba(20, 33, 28, 0.18);
}

.cart-panel .kicker { color: #bfd2c2; }
.cart-panel h2 { margin: 0; font-size: 30px; }

.section-title.compact { margin-bottom: 12px; }
.section-title.compact h2 span {
    color: #f3cc88;
    font: 700 13px Arial, sans-serif;
}

.clear-cart {
    border: 0;
    padding: 0;
    background: transparent;
    color: #dbe7dd;
    font: 11px Arial, sans-serif;
    cursor: pointer;
}

.cart-items { display: grid; gap: 12px; }
.cart-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 12px 0 14px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.cart-item-info {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}

.cart-thumb {
    display: grid;
    place-items: center;
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: linear-gradient(135deg, #f4d9a6 0%, #d7e3d4 100%);
    color: #25473a;
    font-weight: 700;
}

.cart-item strong, .cart-item b { display: block; }
.cart-item strong {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #f7f5ee;
}
.cart-item small {
    display: block;
    margin-top: 5px;
    color: #b7c9b8;
}
.cart-item b {
    margin-top: 6px;
    color: #f2ca7d;
    font: 700 12px Arial, sans-serif;
}

.quantity {
    display: flex;
    align-items: center;
    gap: 8px;
}
.quantity button {
    display: grid;
    place-items: center;
    width: 28px;
    height: 28px;
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 8px;
    background: rgba(255,255,255,0.04);
    color: #fff;
    cursor: pointer;
}
.quantity button:disabled { opacity: .4; }

.empty-cart {
    display: grid;
    place-items: center;
    gap: 8px;
    padding: 28px 12px;
    border: 1px dashed rgba(255,255,255,0.2);
    border-radius: 18px;
    color: #e7eee6;
    text-align: center;
}
.empty-cart-icon {
    display: grid;
    place-items: center;
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: rgba(255,255,255,0.08);
    color: #f0c87b;
    font-size: 30px;
}

.cart-footer {
    margin-top: 22px;
    padding-top: 18px;
    border-top: 1px solid rgba(255,255,255,0.12);
}

.total-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}
.total-line span {
    color: #cfe1d0;
    font: 12px Arial, sans-serif;
}
.total-line strong {
    font-size: 24px;
    color: #f4d07a;
}

.cart-footer label {
    display: grid;
    gap: 8px;
    color: #d3e4d1;
    font: 12px Arial, sans-serif;
}

.cart-footer select {
    width: 100%;
    padding: 11px 12px;
    border: 0;
    border-radius: 10px;
    background: #f5f1ea;
    color: #1f2a24;
}

.pay-button {
    width: 100%;
    margin-top: 18px;
    padding: 13px 12px;
    border: 0;
    border-radius: 12px;
    background: linear-gradient(135deg, #f4c36e 0%, #df9d42 100%);
    color: #1e2a26;
    font: 700 12px Arial, sans-serif;
    cursor: pointer;
}
.pay-button:disabled { opacity: .5; cursor: not-allowed; }

.secure-note {
    display: block;
    margin-top: 12px;
    color: #c9d7c8;
    font: 11px Arial, sans-serif;
    text-align: center;
}

.receipt-box,
.history-box {
    margin-bottom: 24px;
    padding: 18px 18px 14px;
    border: 1px solid rgba(49, 91, 71, 0.08);
    border-radius: 20px;
    background: rgba(255,255,255,0.8);
    box-shadow: 0 10px 20px rgba(42, 55, 41, 0.05);
}

.receipt-head,
.history-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-bottom: 16px;
}

.receipt-head h2,
.history-head h2 {
    margin: 0;
    font-size: clamp(22px, 2vw, 28px);
}

.receipt-meta-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    padding: 10px 0 14px;
    border-top: 1px solid rgba(49, 91, 71, 0.08);
}

.receipt-meta-grid div {
    display: grid;
    gap: 4px;
}

.receipt-meta-grid span,
.receipt-total span {
    color: #6a736d;
    font: 11px Arial, sans-serif;
    text-transform: uppercase;
    letter-spacing: 0.12em;
}

.receipt-meta-grid strong,
.receipt-total strong {
    font-size: 15px;
}

.receipt-items {
    display: grid;
    gap: 10px;
    padding: 14px 0;
    border-top: 1px solid rgba(49, 91, 71, 0.08);
    border-bottom: 1px solid rgba(49, 91, 71, 0.08);
}

.receipt-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.receipt-item strong {
    display: block;
    font-size: 15px;
}

.receipt-item small {
    color: #607066;
    font: 12px Arial, sans-serif;
}

.receipt-item b {
    font-size: 14px;
    color: #315b47;
}

.receipt-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding-top: 12px;
    color: #465048;
}

.history-list {
    display: grid;
    gap: 10px;
}

.history-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    padding: 12px 0;
    border-bottom: 1px solid rgba(49, 91, 71, 0.08);
}

.history-row:last-child { border-bottom: 0; }

.history-row strong {
    display: block;
    font-size: 15px;
}

.history-row small,
.history-meta span {
    color: #6a736d;
    font: 12px Arial, sans-serif;
}

.history-meta {
    display: grid;
    justify-items: end;
    gap: 4px;
}

.history-meta b {
    color: #315b47;
    font-size: 15px;
}

.message {
    display: grid;
    gap: 5px;
    margin: 0 0 18px;
    padding: 12px 14px;
    border-radius: 12px;
    font: 13px Arial, sans-serif;
}
.message.success { background: #e4f2e6; color: #224a31; }
.message.error { background: #fbe7e3; color: #7d3329; }
.message.neutral { background: #edf2ef; color: #466053; }

.empty {
    margin-top: 16px;
    color: #5d655e;
    font: 13px Arial, sans-serif;
}

@media (max-width: 900px) {
    .storefront { padding: 28px 20px 48px; }
    .store-header,
    .shop-layout,
    .store-notes,
    .receipt-meta-grid {
        grid-template-columns: 1fr;
    }

    .cart-panel { position: static; }
    .history-row { align-items: flex-start; flex-direction: column; }
    .history-meta { justify-items: start; }
}

@media (max-width: 620px) {
    .storefront { padding: 26px 14px 52px; }
    .hero-copy, .hero-panel, .receipt-box, .history-box { padding: 16px; }
    .store-header { gap: 14px; margin-bottom: 20px; }
    .product-section { padding: 14px; }
    .product-grid { grid-template-columns: 1fr; }
    .section-title { align-items: start; flex-direction: column; }
    .search-input { width: 100%; }
    .store-header h1 { letter-spacing: -0.8px; }
    .cart-item { align-items: flex-start; }
    .cart-item-info { flex: 1; }
}
</style>
