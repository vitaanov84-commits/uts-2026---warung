<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { api } from '../lib/api'

const activeTab = ref('ringkasan')
const products = ref([])
const orders = ref([])
const expenses = ref([])
const cart = ref([])
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const notice = ref('')
const checkoutResult = ref(null)

const productForm = ref({ name: '', price: '', stock: '' })
const expenseForm = ref({ amount: '', category: '', note: '', expense_date: new Date().toISOString().slice(0, 10) })
const paymentForm = ref({ method: 'bca', status: 'success' })

const cartTotal = computed(() => cart.value.reduce((total, item) => total + Number(item.price) * item.quantity, 0))
const totalSales = computed(() => orders.value.filter((order) => order.status === 'success').reduce((total, order) => total + Number(order.total), 0))
const totalExpenses = computed(() => expenses.value.reduce((total, expense) => total + Number(expense.amount), 0))
const netBalance = computed(() => totalSales.value - totalExpenses.value)

const money = (value) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value || 0)
const dateLabel = (value) => new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium' }).format(new Date(value))

async function loadData() {
    loading.value = true
    error.value = ''
    try {
        const [productResponse, orderResponse, expenseResponse] = await Promise.all([api.products(), api.orders(), api.expenses()])
        products.value = productResponse.data || []
        orders.value = orderResponse || []
        expenses.value = expenseResponse || []
    } catch (exception) {
        error.value = exception.message
    } finally {
        loading.value = false
    }
}

function addToCart(product) {
    const current = cart.value.find((item) => item.id === product.id)
    if (current) {
        if (current.quantity < product.stock) current.quantity += 1
        return
    }
    cart.value.push({ ...product, quantity: 1 })
}

function removeFromCart(productId) {
    cart.value = cart.value.filter((item) => item.id !== productId)
}

function clearFeedback() {
    error.value = ''
    notice.value = ''
    checkoutResult.value = null
}

async function saveProduct() {
    saving.value = true
    clearFeedback()
    try {
        await api.createProduct({ ...productForm.value, price: Number(productForm.value.price), stock: Number(productForm.value.stock) })
        productForm.value = { name: '', price: '', stock: '' }
        notice.value = 'Produk berhasil ditambahkan.'
        await loadData()
    } catch (exception) {
        error.value = exception.message
    } finally {
        saving.value = false
    }
}

async function deleteProduct(product) {
    if (!window.confirm(`Hapus produk ${product.name}?`)) return
    try {
        await api.deleteProduct(product.id)
        cart.value = cart.value.filter((item) => item.id !== product.id)
        notice.value = 'Produk berhasil dihapus.'
        await loadData()
    } catch (exception) {
        error.value = exception.message
    }
}

async function checkout() {
    if (!cart.value.length) return
    saving.value = true
    clearFeedback()
    try {
        const response = await api.checkout({
            items: cart.value.map((item) => ({ product_id: item.id, quantity: item.quantity })),
            payment_method: paymentForm.value.method,
            payment_status: paymentForm.value.status,
        })
        checkoutResult.value = response.data
        notice.value = response.message
        cart.value = []
        await loadData()
    } catch (exception) {
        checkoutResult.value = { status: 'failed', failure_reason: exception.message }
        error.value = exception.message
        await loadData()
    } finally {
        saving.value = false
    }
}

async function saveExpense() {
    saving.value = true
    clearFeedback()
    try {
        await api.createExpense({ ...expenseForm.value, amount: Number(expenseForm.value.amount) })
        expenseForm.value = { amount: '', category: '', note: '', expense_date: new Date().toISOString().slice(0, 10) }
        notice.value = 'Pengeluaran berhasil disimpan.'
        await loadData()
    } catch (exception) {
        error.value = exception.message
    } finally {
        saving.value = false
    }
}

async function deleteExpense(expense) {
    if (!window.confirm('Hapus catatan pengeluaran ini?')) return
    try {
        await api.deleteExpense(expense.id)
        notice.value = 'Pengeluaran berhasil dihapus.'
        await loadData()
    } catch (exception) {
        error.value = exception.message
    }
}

onMounted(() => {
    loadData()
    selectTabFromHash()
    window.addEventListener('hashchange', selectTabFromHash)
})

function selectTabFromHash() {
    if (window.location.hash === '#ringkasan') activeTab.value = 'ringkasan'
    if (window.location.hash === '#keuangan') activeTab.value = 'pengeluaran'
    if (window.location.hash === '#produk') activeTab.value = 'kasir'
}

onBeforeUnmount(() => window.removeEventListener('hashchange', selectTabFromHash))
</script>

<template>
    <main class="admin-root">
        <header class="topbar">
            <div class="title-wrap">
                <p class="eyebrow">OPERASIONAL WARUNG</p>
                <h1>Sekawarung</h1>
            </div>
            <button class="refresh-button" type="button" @click="loadData">Muat ulang</button>
        </header>

        <template v-if="activeTab === 'ringkasan'">
        <section class="summary-section finance-summary" aria-labelledby="finance-summary-title">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">KEUANGAN</p>
                    <h2 id="finance-summary-title">Ringkasan keuangan</h2>
                </div>
                <button class="text-button" type="button" @click="activeTab = 'riwayat'">Buka pembayaran</button>
            </div>
            <div class="summary-grid">
                <article class="summary-card accent-green" @click="activeTab = 'riwayat'" tabindex="0" @keydown.enter="activeTab = 'riwayat'">
                    <span>Penjualan berhasil</span>
                    <strong>{{ money(totalSales) }}</strong>
                    <small class="card-action">Lihat pembayaran</small>
                </article>
                <article class="summary-card accent-red" @click="activeTab = 'pengeluaran'" tabindex="0" @keydown.enter="activeTab = 'pengeluaran'">
                    <span>Total pengeluaran</span>
                    <strong>{{ money(totalExpenses) }}</strong>
                    <small class="card-action">Lihat pengeluaran</small>
                </article>
                <article class="summary-card accent-blue" @click="activeTab = 'riwayat'" tabindex="0" @keydown.enter="activeTab = 'riwayat'">
                    <span>Saldo bersih</span>
                    <strong>{{ money(netBalance) }}</strong>
                    <small class="card-action">Lihat pembayaran</small>
                </article>
            </div>
        </section>

        <section class="summary-section product-summary" aria-labelledby="product-summary-title">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">KATALOG</p>
                    <h2 id="product-summary-title">Ringkasan produk</h2>
                </div>
                <button class="text-button" type="button" @click="activeTab = 'kasir'">Kelola produk</button>
            </div>
            <div class="summary-grid product-summary-grid">
                <article class="summary-card accent-yellow" @click="activeTab = 'kasir'" tabindex="0" @keydown.enter="activeTab = 'kasir'">
                    <span>Produk tersedia</span>
                    <strong>{{ products.length }}</strong>
                    <small class="card-action">Kelola katalog</small>
                </article>
            </div>
        </section>
        </template>

        <nav class="tabs" aria-label="Navigasi aplikasi">
            <button :class="{ active: activeTab === 'kasir' }" type="button" @click="activeTab = 'kasir'">Produk</button>
            <button :class="{ active: activeTab === 'pengeluaran' }" type="button" @click="activeTab = 'pengeluaran'">Pengeluaran</button>
            <button :class="{ active: activeTab === 'riwayat' }" type="button" @click="activeTab = 'riwayat'">Pembayaran</button>
        </nav>

        <p v-if="loading" class="feedback info">Memuat data aplikasi...</p>
        <p v-if="error" class="feedback danger">{{ error }}</p>
        <p v-if="notice" class="feedback success">{{ notice }}</p>

        <div v-if="checkoutResult" :class="['payment-result', checkoutResult.status === 'failed' ? 'danger' : 'success']">
            <div>
                <p class="eyebrow">HASIL PEMBAYARAN</p>
                <h3>{{ checkoutResult.status === 'failed' ? 'Pembayaran gagal' : 'Pembayaran berhasil' }}</h3>
            </div>
            <div class="result-meta">
                <span v-if="checkoutResult.id">Order #{{ checkoutResult.id }}</span>
                <strong>{{ money(checkoutResult.total || 0) }}</strong>
                <small>{{ checkoutResult.payment?.method || paymentForm.method }} • {{ checkoutResult.payment?.status || checkoutResult.status || 'pending' }}</small>
                <small v-if="checkoutResult.failure_reason">{{ checkoutResult.failure_reason }}</small>
            </div>
        </div>

        <section v-if="activeTab === 'kasir'" id="produk" class="workspace two-columns">
            <div class="panel">
                <div class="panel-heading">
                    <div>
                        <p class="eyebrow">KATALOG</p>
                        <h2>Produk</h2>
                    </div>
                    <span>{{ products.length }} item</span>
                </div>

                <form class="inline-form" @submit.prevent="saveProduct">
                    <input v-model="productForm.name" required placeholder="Nama produk" />
                    <input v-model="productForm.price" required min="0" type="number" placeholder="Harga" />
                    <input v-model="productForm.stock" required min="0" type="number" placeholder="Stok" />
                    <button class="primary-button" :disabled="saving" type="submit">Tambah</button>
                </form>

                <div class="product-list">
                    <article v-for="product in products" :key="product.id" class="product-row">
                        <div>
                            <strong>{{ product.name }}</strong>
                            <small>{{ money(product.price) }} • stok {{ product.stock }}</small>
                        </div>
                        <div class="row-actions">
                            <span class="stock-label">{{ product.stock ? 'Tersedia' : 'Habis' }}</span>
                            <button class="delete-button" type="button" @click="deleteProduct(product)">Hapus</button>
                        </div>
                    </article>
                    <p v-if="!products.length && !loading" class="empty-state">Belum ada produk. Tambahkan produk pertama di atas.</p>
                </div>
            </div>
        </section>

        <section v-if="activeTab === 'pengeluaran'" id="keuangan" class="workspace two-columns">
            <div class="panel">
                <div class="panel-heading">
                    <div>
                        <p class="eyebrow">CATATAN BIAYA</p>
                        <h2>Tambah pengeluaran</h2>
                    </div>
                </div>

                <form class="stack-form" @submit.prevent="saveExpense">
                    <label>Jumlah
                        <input v-model="expenseForm.amount" required min="0" type="number" placeholder="Contoh: 50000" />
                    </label>
                    <label>Kategori
                        <input v-model="expenseForm.category" required placeholder="Contoh: Belanja stok" />
                    </label>
                    <label>Tanggal
                        <input v-model="expenseForm.expense_date" required type="date" />
                    </label>
                    <label>Catatan
                        <textarea v-model="expenseForm.note" rows="4" placeholder="Opsional"></textarea>
                    </label>
                    <button class="primary-button" :disabled="saving" type="submit">Simpan pengeluaran</button>
                </form>
            </div>

            <div class="panel">
                <div class="panel-heading">
                    <div>
                        <p class="eyebrow">LEDGER</p>
                        <h2>Pengeluaran terbaru</h2>
                    </div>
                    <strong>{{ money(totalExpenses) }}</strong>
                </div>

                <div class="product-list">
                    <article v-for="expense in expenses" :key="expense.id" class="product-row">
                        <div>
                            <strong>{{ expense.category }}</strong>
                            <small>{{ dateLabel(expense.expense_date) }}<span v-if="expense.note"> • {{ expense.note }}</span></small>
                        </div>
                        <div class="row-actions">
                            <strong class="expense-value">- {{ money(expense.amount) }}</strong>
                            <button class="delete-button" type="button" @click="deleteExpense(expense)">Hapus</button>
                        </div>
                    </article>
                    <p v-if="!expenses.length" class="empty-state">Belum ada pengeluaran.</p>
                </div>
            </div>
        </section>

        <section v-if="activeTab === 'riwayat'" class="panel history-panel">
            <div class="panel-heading">
                <div>
                    <p class="eyebrow">AUDIT TRANSAKSI</p>
                    <h2>Riwayat pembayaran</h2>
                </div>
                <span>{{ orders.length }} transaksi</span>
            </div>

            <div class="history-list">
                <article v-for="order in orders" :key="order.id" class="history-row">
                    <div class="history-main">
                        <strong>Order #{{ order.id }}</strong>
                        <small>{{ dateLabel(order.created_at) }} • {{ order.payment?.method || order.payment_method || '-' }}</small>
                        <div v-if="order.items?.length" class="history-items">
                            <span v-for="item in order.items" :key="`${order.id}-${item.id}`">
                                {{ item.product_name }} ({{ item.quantity }})
                            </span>
                        </div>
                    </div>
                    <div class="history-meta">
                        <strong>{{ money(order.total) }}</strong>
                        <span :class="['status-pill', (order.status || order.payment?.status) === 'success' ? 'pill-success' : 'pill-failed']">
                            {{ (order.status || order.payment?.status) === 'success' ? 'Berhasil' : 'Gagal' }}
                        </span>
                    </div>
                </article>
                <p v-if="!orders.length" class="empty-state">Belum ada transaksi.</p>
            </div>
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
:global(button), :global(input), :global(select), :global(textarea) { font: inherit; }

.admin-root {
    max-width: 1220px;
    margin: 0 auto;
    padding: 24px 24px 48px;
}

.topbar {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 24px;
}

.eyebrow {
    margin: 0 0 8px;
    color: #6a756b;
    font: 700 11px/1.2 Arial, sans-serif;
    letter-spacing: 1.8px;
}

h1, h2, p { margin-top: 0; }
h1 {
    margin-bottom: 0;
    font-size: clamp(34px, 4vw, 62px);
    line-height: .96;
    letter-spacing: -1px;
}

.refresh-button {
    border: 1px solid rgba(49, 91, 71, 0.2);
    background: rgba(255,255,255,0.7);
    color: #243b31;
    border-radius: 12px;
    padding: 11px 16px;
    cursor: pointer;
    font: 700 12px Arial, sans-serif;
}

.summary-section {
    margin-bottom: 24px;
}

.section-heading {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 12px;
}
.section-heading h2 {
    margin: 0;
    font-size: 22px;
}
.text-button {
    border: 0;
    padding: 0 0 3px;
    background: transparent;
    color: #315b47;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
}
.text-button:hover, .text-button:focus-visible {
    color: #1f4030;
    text-decoration: underline;
    outline: none;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 22px;
}

.summary-card {
    display: grid;
    gap: 6px;
    padding: 18px 18px 16px;
    border-radius: 18px;
    border-top: 4px solid #315b47;
    background: rgba(255,255,255,0.75);
    box-shadow: 0 12px 22px rgba(31, 42, 36, 0.05);
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.summary-card:hover, .summary-card:focus-visible {
    transform: translateY(-3px);
    box-shadow: 0 18px 32px rgba(31, 42, 36, 0.08);
    outline: none;
}
.summary-card span {
    color: #697167;
    font: 12px Arial, sans-serif;
}
.summary-card strong {
    font-size: 24px;
    line-height: 1.1;
}
.product-summary-grid {
    grid-template-columns: minmax(0, 1fr);
}
.card-action {
    color: #5a665f;
    font: 11px Arial, sans-serif;
}
.accent-green { border-color: #2c6c54; }
.accent-red { border-color: #b45345; }
.accent-blue { border-color: #4d7ea5; }
.accent-yellow { border-color: #d7a545; }

.tabs {
    display: flex;
    gap: 18px;
    border-bottom: 1px solid rgba(49, 91, 71, 0.12);
    margin-bottom: 18px;
}
.tabs button {
    padding: 12px 0;
    border: 0;
    background: transparent;
    color: #697267;
    font: 700 12px Arial, sans-serif;
    letter-spacing: .3px;
    border-bottom: 3px solid transparent;
    cursor: pointer;
}
.tabs button.active {
    border-color: #315b47;
    color: #1d332d;
}

.feedback {
    margin: 0 0 18px;
    padding: 12px 14px;
    border-radius: 12px;
    font: 13px Arial, sans-serif;
}
.feedback.info { background: #eaf1f3; color: #33606e; }
.feedback.success { background: #e5f3e9; color: #295c42; }
.feedback.danger { background: #f7e2df; color: #7a322d; }

.workspace {
    display: grid;
    gap: 18px;
}
.two-columns { grid-template-columns: minmax(0, 1.4fr) minmax(300px, 0.9fr); }

.panel {
    padding: 20px 20px 18px;
    border-radius: 20px;
    background: rgba(255,255,255,0.8);
    box-shadow: 0 12px 24px rgba(31, 42, 36, 0.05);
    border: 1px solid rgba(49, 91, 71, 0.08);
}

.panel-heading {
    display: flex;
    align-items: start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 18px;
}
.panel-heading > span,
.panel-heading strong {
    color: #5f675e;
    font: 12px Arial, sans-serif;
}

.inline-form {
    display: grid;
    grid-template-columns: 1.5fr 1fr 0.75fr auto;
    gap: 10px;
    margin-bottom: 22px;
}

.stack-form {
    display: grid;
    gap: 14px;
}
label {
    display: grid;
    gap: 8px;
    color: #4d564f;
    font: 12px Arial, sans-serif;
    font-weight: 700;
}
input, select, textarea {
    width: 100%;
    padding: 11px 12px;
    border: 1px solid #d8d0c2;
    border-radius: 12px;
    background: #fffdf9;
    color: #1f2a24;
    font: 14px Arial, sans-serif;
}
textarea { resize: vertical; }

.primary-button {
    align-self: end;
    border: 0;
    border-radius: 12px;
    padding: 11px 16px;
    background: linear-gradient(135deg, #315b47 0%, #1d3d34 100%);
    color: white;
    font: 700 12px Arial, sans-serif;
    cursor: pointer;
}

.product-list, .history-list { display: grid; gap: 8px; }
.product-row, .history-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    padding: 12px 0;
    border-bottom: 1px solid rgba(49, 91, 71, 0.08);
}
.product-row:last-child, .history-row:last-child { border-bottom: 0; }
.product-row strong, .history-row strong { display: block; }
.product-row small, .history-row small {
    display: block;
    margin-top: 6px;
    color: #647067;
    font: 12px Arial, sans-serif;
}
.row-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}
.stock-label {
    padding: 6px 8px;
    border-radius: 999px;
    background: #e8f2eb;
    color: #245e42;
    font: 700 10px Arial, sans-serif;
}
.delete-button {
    border: 0;
    background: transparent;
    color: #a74e45;
    font: 700 11px Arial, sans-serif;
    cursor: pointer;
}
.expense-value { color: #a74e45; }

.empty-state {
    margin: 0;
    padding: 14px 0;
    color: #677069;
    font: 13px Arial, sans-serif;
}

.history-panel { margin-top: 18px; }
.history-main {
    min-width: 0;
    flex: 1;
}
.history-items {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 8px;
}
.history-items span {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 999px;
    background: #f1f5f3;
    color: #425045;
    font: 700 10px Arial, sans-serif;
}
.history-meta {
    display: grid;
    justify-items: end;
    gap: 6px;
}
.status-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 82px;
    padding: 6px 10px;
    border-radius: 999px;
    font: 700 10px Arial, sans-serif;
}
.pill-success {
    background: #dfeee3;
    color: #2a684a;
}
.pill-failed {
    background: #f5e2df;
    color: #824d42;
}

@media (max-width: 900px) {
    .summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .two-columns { grid-template-columns: 1fr; }
}

@media (max-width: 620px) {
    .admin-root { padding: 28px 16px 56px; }
    .topbar { align-items: start; flex-direction: column; }
    .summary-grid { grid-template-columns: 1fr; }
    .inline-form { grid-template-columns: 1fr; }
    .primary-button { width: 100%; }
    .panel { padding: 18px; }
    .product-row, .history-row { align-items: start; flex-direction: column; }
}
</style>
