// ── Talemwa Admin — shared JS helpers ─────────────────────────

const API = 'https://api.roberttalemwa.online';

// ── Auth helpers ───────────────────────────────────────────────
function authGuard() {
  const token = localStorage.getItem('talemwa_token');
  if (!token) {
    window.location.href = '/auth/login.php';
    return null;
  }
  return token;
}

function authHeaders() {
  return {
    'Content-Type':  'application/json',
    'Authorization': 'Bearer ' + localStorage.getItem('talemwa_token')
  };
}

function currentUser() {
  return {
    name: localStorage.getItem('talemwa_name') || 'Admin',
    role: localStorage.getItem('talemwa_role') || 'media'
  };
}

function isSuperAdmin() {
  return localStorage.getItem('talemwa_role') === 'super_admin';
}

// ── API wrapper ────────────────────────────────────────────────
async function apiFetch(path, options = {}) {
  const res = await fetch(`${API}${path}`, {
    ...options,
    headers: { ...authHeaders(), ...(options.headers || {}) }
  });

  if (res.status === 401) {
    localStorage.removeItem('talemwa_token');
    window.location.href = '/auth/login.php';
    return null;
  }

  const json = await res.json();
  if (!res.ok) throw new Error(json.data?.error || `Request failed (${res.status})`);
  return json.data;
}

async function apiGet(path)           { return apiFetch(path); }
async function apiPost(path, body)    { return apiFetch(path, { method: 'POST',   body: JSON.stringify(body) }); }
async function apiPut(path, body)     { return apiFetch(path, { method: 'PUT',    body: JSON.stringify(body) }); }
async function apiDelete(path)        { return apiFetch(path, { method: 'DELETE' }); }

// ── Formatting helpers ─────────────────────────────────────────
function formatCurrency(amount, currency = 'USD') {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(amount);
}

function formatDate(dateStr) {
  if (!dateStr) return '—';
  return new Date(dateStr).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
}

function formatDateTime(dateStr) {
  if (!dateStr) return '—';
  return new Date(dateStr).toLocaleString('en-GB', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
}

function timeAgo(dateStr) {
  const diff = Date.now() - new Date(dateStr);
  const mins = Math.floor(diff / 60000);
  if (mins < 1)   return 'just now';
  if (mins < 60)  return `${mins}m ago`;
  if (mins < 1440) return `${Math.floor(mins/60)}h ago`;
  return `${Math.floor(mins/1440)}d ago`;
}

function formatDuration(seconds) {
  if (!seconds) return '—';
  const h = Math.floor(seconds / 3600);
  const m = Math.floor((seconds % 3600) / 60);
  const s = seconds % 60;
  if (h > 0) return `${h}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
  return `${m}:${String(s).padStart(2,'0')}`;
}

// ── Toast notifications ────────────────────────────────────────
function showToast(message, type = 'success') {
  const el = document.createElement('div');
  el.className = `fixed bottom-6 right-6 z-50 px-5 py-3 rounded-xl shadow-2xl text-sm font-medium transition-all
    ${type === 'success' ? 'bg-green-500 text-white' : type === 'error' ? 'bg-red-500 text-white' : 'bg-navy text-white'}`;
  el.textContent = message;
  document.body.appendChild(el);
  setTimeout(() => el.remove(), 3500);
}

// ── Confirm dialog ─────────────────────────────────────────────
function confirmAction(message) {
  return window.confirm(message);
}
