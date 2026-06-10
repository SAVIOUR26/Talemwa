<?php
$pageTitle   = 'Prayer Request';
$metaDesc    = 'Submit a prayer request to Pastor Robert Talemwa and the ministry team. We pray for every request.';
$currentPage = 'prayer';
include '../partials/head.php';
include '../partials/nav.php';
?>

<section class="bg-navy py-12 text-white text-center">
  <div class="max-w-xl mx-auto px-4">
    <i class="ti ti-pray text-gold text-5xl block mb-4"></i>
    <h1 class="text-3xl sm:text-4xl font-bold mb-3">Prayer Request</h1>
    <p class="text-gray-400">"The prayer of a righteous person is powerful and effective." — James 5:16</p>
  </div>
</section>

<main class="max-w-2xl mx-auto px-4 sm:px-6 py-12" x-data="prayerForm()" x-init="init()">

  <!-- Success -->
  <div x-show="submitted" x-cloak class="text-center py-12">
    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mb-6">
      <i class="ti ti-check text-green-600 text-4xl"></i>
    </div>
    <h2 class="text-2xl font-bold text-gray-900 mb-3">Prayer Request Received</h2>
    <p class="text-gray-600 mb-6">
      Thank you for sharing your heart with us. Our team will pray over your request.
      We believe in a God who hears and answers prayer.
    </p>
    <button @click="submitted = false; form = { message:'', contact:'' }"
            class="text-gold hover:underline font-medium">Submit another request</button>
  </div>

  <!-- Form -->
  <div x-show="!submitted">
    <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm mb-6">
      <h2 class="font-bold text-gray-900 text-xl mb-6">Share Your Request</h2>

      <div class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">
            Your Prayer Request <span class="text-red-500">*</span>
          </label>
          <textarea x-model="form.message" rows="6" maxlength="1000"
                    placeholder="Share what you'd like us to pray for…"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold resize-none"></textarea>
          <p class="text-xs text-gray-400 mt-1" x-text="`${form.message.length}/1000`"></p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">
            Your Name or Email <span class="text-gray-400 font-normal">(optional)</span>
          </label>
          <input x-model="form.contact" type="text" placeholder="So we can follow up with you…"
                 class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold">
          <p class="text-xs text-gray-400 mt-1">We keep all requests confidential</p>
        </div>
      </div>

      <div x-show="error" x-cloak class="mt-4 bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3" x-text="error"></div>

      <button @click="submit()" :disabled="sending || !form.message"
              class="mt-6 w-full bg-navy hover:bg-navy-700 text-white font-bold py-4 rounded-xl transition disabled:opacity-60 flex items-center justify-center gap-2">
        <svg x-show="sending" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <i x-show="!sending" class="ti ti-send text-xl"></i>
        <span x-text="sending ? 'Sending…' : 'Submit Prayer Request'"></span>
      </button>
    </div>

    <!-- Reassurance -->
    <div class="bg-navy/5 border border-navy/10 rounded-2xl p-6 text-center">
      <p class="font-semibold text-navy mb-2">We read and pray for every request</p>
      <p class="text-sm text-gray-600">
        Our team reviews all prayer requests personally. No request is too big or too small for God.
      </p>
    </div>
  </div>

</main>

<?php include '../partials/radio-bar.php'; ?>
<?php include '../partials/footer.php'; ?>

<script>
function prayerForm() {
  return {
    form:      { message:'', contact:'' },
    sending:   false,
    submitted: false,
    error:     '',

    init() {},

    async submit() {
      if (!this.form.message.trim()) { this.error = 'Please write your prayer request.'; return; }
      this.sending = true; this.error = '';
      try {
        const res  = await fetch(`${API}/api/prayer`, {
          method:  'POST',
          headers: { 'Content-Type':'application/json' },
          body:    JSON.stringify(this.form)
        });
        const json = await res.json();
        if (!res.ok) throw new Error(json.data?.error || 'Could not submit request');
        this.submitted = true;
      } catch(e) { this.error = e.message; } finally { this.sending = false; }
    }
  }
}
</script>
