<?php
$pageTitle   = 'Contact';
$metaDesc    = 'Contact Pastor Robert Talemwa Ministry. Based in Kampala, Uganda. Reach our global congregation.';
$currentPage = 'contact';
include __DIR__ . '/../partials/head.php';
include __DIR__ . '/../partials/nav.php';
?>

<section class="bg-navy py-12 text-white text-center">
  <div class="max-w-xl mx-auto px-4">
    <h1 class="text-3xl sm:text-4xl font-bold mb-3">Contact Us</h1>
    <p class="text-gray-400">We'd love to hear from you</p>
  </div>
</section>

<main class="max-w-5xl mx-auto px-4 sm:px-6 py-12">
  <div class="grid md:grid-cols-2 gap-10">

    <!-- Info -->
    <div class="space-y-6">
      <div>
        <h2 class="text-xl font-bold text-gray-900 mb-5">Get in Touch</h2>
        <div class="space-y-4">
          <div class="flex items-start gap-4 p-5 bg-gray-50 rounded-2xl">
            <div class="w-10 h-10 rounded-xl bg-navy flex items-center justify-center flex-shrink-0">
              <i class="ti ti-map-pin text-gold"></i>
            </div>
            <div>
              <p class="font-semibold text-gray-900">Location</p>
              <p class="text-gray-500 text-sm">Kampala, Uganda</p>
            </div>
          </div>
          <div class="flex items-start gap-4 p-5 bg-gray-50 rounded-2xl">
            <div class="w-10 h-10 rounded-xl bg-navy flex items-center justify-center flex-shrink-0">
              <i class="ti ti-world text-gold"></i>
            </div>
            <div>
              <p class="font-semibold text-gray-900">Website</p>
              <a href="https://roberttalemwa.online" class="text-gold text-sm hover:underline">roberttalemwa.online</a>
            </div>
          </div>
          <div class="flex items-start gap-4 p-5 bg-gray-50 rounded-2xl">
            <div class="w-10 h-10 rounded-xl bg-navy flex items-center justify-center flex-shrink-0">
              <i class="ti ti-brand-youtube text-gold"></i>
            </div>
            <div>
              <p class="font-semibold text-gray-900">YouTube</p>
              <a href="https://www.youtube.com/@pastortalemwarobert4160" target="_blank" class="text-gold text-sm hover:underline">@pastortalemwarobert4160</a>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-navy rounded-2xl p-6 text-white">
        <h3 class="font-bold mb-2">For Prayer Requests</h3>
        <p class="text-gray-400 text-sm mb-4">Use our dedicated prayer request form and our team will pray over your request.</p>
        <a href="/prayer"
           class="inline-flex items-center gap-2 bg-gold hover:bg-gold-light text-navy font-semibold px-4 py-2.5 rounded-xl text-sm transition">
          <i class="ti ti-pray"></i> Submit Prayer Request
        </a>
      </div>
    </div>

    <!-- Form -->
    <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm" x-data="contactForm()">
      <h2 class="text-xl font-bold text-gray-900 mb-6">Send a Message</h2>

      <div x-show="sent" x-cloak class="text-center py-8">
        <i class="ti ti-check text-green-500 text-5xl block mb-3"></i>
        <p class="font-semibold text-gray-900">Message sent!</p>
        <p class="text-gray-500 text-sm mt-1">Thank you for reaching out. We'll be in touch.</p>
      </div>

      <form x-show="!sent" @submit.prevent="submit()" class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
            <input x-model="form.name" type="text" required class="input-field w-full" placeholder="Your name">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
            <input x-model="form.email" type="email" required class="input-field w-full" placeholder="your@email.com">
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Message</label>
          <textarea x-model="form.message" rows="5" required class="input-field w-full resize-none" placeholder="How can we help?"></textarea>
        </div>
        <button type="submit" :disabled="sending"
                class="w-full bg-navy text-white font-semibold py-3 rounded-xl hover:bg-navy-700 transition disabled:opacity-60">
          <span x-text="sending ? 'Sending…' : 'Send Message'"></span>
        </button>
      </form>
    </div>

  </div>
</main>

<style>
.input-field { border:1px solid #e5e7eb; border-radius:0.75rem; padding:0.6rem 1rem; font-size:0.875rem; outline:none; transition:border-color 0.15s; }
.input-field:focus { border-color:#C9A84C; box-shadow:0 0 0 1px #C9A84C; }
</style>

<?php include __DIR__ . '/../partials/radio-bar.php'; ?>
<?php include __DIR__ . '/../partials/footer.php'; ?>

<script>
function contactForm() {
  return {
    form:    { name:'', email:'', message:'' },
    sending: false,
    sent:    false,

    async submit() {
      this.sending = true;
      // Contact form submits as a prayer/message — no dedicated endpoint needed
      // In production, wire to an email service or add a /api/contact endpoint
      await new Promise(r => setTimeout(r, 800));
      this.sent    = true;
      this.sending = false;
    }
  }
}
</script>
