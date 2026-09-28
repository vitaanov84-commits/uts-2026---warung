const apiBase = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000/api'

async function request(path, options = {}) {
    const hasFormData = typeof FormData !== 'undefined' && options.body instanceof FormData
    const response = await fetch(`${apiBase}${path}`, {
        headers: {
            Accept: 'application/json',
            ...(!hasFormData ? { 'Content-Type': 'application/json' } : {}),
            ...options.headers,
        },
        ...options,
    })

    const payload = await response.json().catch(() => ({}))
    if (!response.ok) {
        const validationMessage = payload.errors
            ? Object.values(payload.errors).flat().join(' ')
            : null
        throw new Error(validationMessage || payload.message || 'Permintaan gagal diproses.')
    }

    return payload
}

export const api = {
    login: (data) => request('/login', { method: 'POST', body: JSON.stringify(data) }),
    profilePhoto: (formData) => request('/profile/photo', { method: 'POST', body: formData }),
    products: () => request('/products'),
    createProduct: (data) => request('/products', { method: 'POST', body: JSON.stringify(data) }),
    deleteProduct: (id) => request(`/products/${id}`, { method: 'DELETE' }),
    checkout: (data) => request('/checkout', { method: 'POST', body: JSON.stringify(data) }),
    orders: () => request('/orders'),
    expenses: () => request('/expenses'),
    createExpense: (data) => request('/expenses', { method: 'POST', body: JSON.stringify(data) }),
    deleteExpense: (id) => request(`/expenses/${id}`, { method: 'DELETE' }),
}
