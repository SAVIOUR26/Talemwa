<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — Talemwa Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          navy: { DEFAULT: '#0A1628', 800: '#0d1e38', 700: '#112448' },
          gold:  { DEFAULT: '#C9A84C', light: '#d4b86a' }
        }
      }
    }
  }
</script>
</head>
<body class="min-h-screen bg-navy flex items-center justify-center px-4">

<div class="w-full max-w-md" x-data="loginForm()" x-init="checkAlreadyLoggedIn()">

  <!-- Logo -->
  <div class="text-center mb-8">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gold mb-4">
      <svg class="w-8 h-8 text-navy" fill="currentColor" viewBox="0 0 24 24">
        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
      </svg>
    </div>
    <h1 class="text-2xl font-bold text-white">Talemwa Admin</h1>
    <p class="text-gray-400 text-sm mt-1">Pastor Robert Talemwa · Ministry Platform</p>
  </div>

  <!-- Card -->
  <div class="bg-navy-800 border border-white/10 rounded-2xl p-8 shadow-2xl">
    <h2 class="text-white text-xl font-semibold mb-6">Sign in to your account</h2>

    <!-- Error -->
    <div x-show="error" x-cloak class="mb-4 bg-red-500/10 border border-red-500/30 text-red-400 text-sm rounded-lg px-4 py-3" x-text="error"></div>

    <form @submit.prevent="submit" class="space-y-5">
      <div>
        <label class="block text-sm text-gray-400 mb-1.5">Email address</label>
        <input
          type="email"
          x-model="email"
          required
          placeholder="admin@roberttalemwa.online"
          class="w-full bg-navy border border-white/10 text-white rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold transition placeholder-gray-600"
        >
      </div>
      <div>
        <label class="block text-sm text-gray-400 mb-1.5">Password</label>
        <input
          type="password"
          x-model="password"
          required
          placeholder="••••••••"
          class="w-full bg-navy border border-white/10 text-white rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold transition placeholder-gray-600"
        >
      </div>
      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-gold hover:bg-gold-light text-navy font-semibold py-3 rounded-lg transition flex items-center justify-center gap-2 disabled:opacity-60"
      >
        <svg x-show="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <span x-text="loading ? 'Signing in…' : 'Sign in'"></span>
      </button>
    </form>
  </div>

  <p class="text-center text-xs text-gray-600 mt-6">
    Talemwa Ministry Platform · Built by Thirdsan Enterprises
  </p>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
const API = 'https://api.roberttalemwa.online';

function loginForm() {
  return {
    email: '',
    password: '',
    loading: false,
    error: '',

    checkAlreadyLoggedIn() {
      if (localStorage.getItem('talemwa_token')) {
        window.location.href = '/pages/overview.php';
      }
    },

    async submit() {
      this.loading = true;
      this.error   = '';
      try {
        const res  = await fetch(`${API}/api/auth/login`, {
          method:  'POST',
          headers: { 'Content-Type': 'application/json' },
          body:    JSON.stringify({ email: this.email, password: this.password })
        });
        const json = await res.json();
        if (!res.ok) throw new Error(json.data?.error || 'Login failed');

        localStorage.setItem('talemwa_token', json.data.token);
        localStorage.setItem('talemwa_name',  json.data.name);
        localStorage.setItem('talemwa_role',  json.data.role);
        window.location.href = '/pages/overview.php';
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>
</body>
</html>
