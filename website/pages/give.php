<?php
$pageTitle   = 'Give Online';
$metaDesc    = 'Support the ministry of Pastor Robert Talemwa. Give tithes, offerings, and project gifts securely online.';
$currentPage = 'give';
include '../partials/head.php';
include '../partials/nav.php';
?>

<section class="bg-navy py-12 text-white text-center">
  <div class="max-w-xl mx-auto px-4">
    <i class="ti ti-coin text-gold text-5xl block mb-4"></i>
    <h1 class="text-3xl sm:text-4xl font-bold mb-3">Give Online</h1>
    <p class="text-gray-400">"Give, and it will be given to you." — Luke 6:38</p>
  </div>
</section>

<main class="max-w-5xl mx-auto px-4 sm:px-6 py-12" x-data="givingPage()" x-init="init()">

  <div class="grid lg:grid-cols-2 gap-10">

    <!-- Form -->
    <div class="space-y-5">

      <!-- Giving type -->
      <div>
        <p class="text-sm font-semibold text-gray-700 mb-3">I want to give a</p>
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
          <template x-for="type in types" :key="type.value">
            <button @click="form.giving_type = type.value"
                    :class="form.giving_type === type.value
                      ? 'bg-navy text-white border-navy'
                      : 'bg-white text-gray-600 border-gray-200 hover:border-navy/30'"
                    class="py-3 rounded-xl border text-sm font-medium transition text-center"
                    x-text="type.label"></button>
          </template>
        </div>
      </div>

      <!-- Campaign picker -->
      <div x-show="form.giving_type === 'campaign' && campaigns.length > 0" x-cloak>
        <p class="text-sm font-semibold text-gray-700 mb-2">Select Campaign</p>
        <div class="space-y-2">
          <template x-for="c in campaigns" :key="c.id">
            <button @click="form.campaign_id = c.id"
                    :class="form.campaign_id === c.id ? 'border-gold ring-1 ring-gold' : 'border-gray-200'"
                    class="w-full text-left p-4 rounded-xl border bg-white hover:border-gold/50 transition">
              <div class="flex items-center justify-between mb-2">
                <p class="font-medium text-gray-900" x-text="c.title"></p>
                <span class="text-xs text-gold" x-text="`${Math.round((c.raised_amount/c.goal_amount)*100)}%`"></span>
              </div>
              <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-gold rounded-full" :style="`width:${Math.min(100,(c.raised_amount/c.goal_amount)*100)}%`"></div>
              </div>
            </button>
          </template>
        </div>
      </div>

      <!-- Amount + currency -->
      <div>
        <p class="text-sm font-semibold text-gray-700 mb-3">Amount</p>
        <div class="flex gap-3">
          <select x-model="form.currency"
                  class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gold font-medium">
            <option>USD</option><option>UGX</option><option>GBP</option><option>EUR</option>
          </select>
          <input x-model="form.amount" type="number" min="1" placeholder="0.00"
                 class="flex-1 border border-gray-200 rounded-xl px-4 py-3 text-xl font-bold focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold">
        </div>
        <!-- Quick amounts -->
        <div class="flex gap-2 flex-wrap mt-3">
          <template x-for="amt in quickAmounts" :key="amt">
            <button @click="form.amount = amt"
                    :class="form.amount == amt ? 'bg-navy text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition"
                    x-text="amt"></button>
          </template>
        </div>
      </div>

      <!-- Donor details (optional) -->
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Your Name <span class="text-gray-400 font-normal">(optional)</span></label>
          <input x-model="form.donor_name" type="text" placeholder="Anonymous"
                 class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gold">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-gray-400 font-normal">(optional)</span></label>
          <input x-model="form.donor_email" type="email" placeholder="for receipt"
                 class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gold">
        </div>
      </div>

      <div x-show="error" x-cloak class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3" x-text="error"></div>

      <!-- Submit -->
      <button @click="proceed()" :disabled="processing || !form.amount"
              class="w-full bg-gold hover:bg-gold-light text-navy font-bold py-4 rounded-xl text-lg transition disabled:opacity-60 flex items-center justify-center gap-2 shadow-lg shadow-gold/20">
        <svg x-show="processing" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <i x-show="!processing" class="ti ti-lock text-xl"></i>
        <span x-text="processing ? 'Processing…' : `Give ${form.currency} ${form.amount || ''}`"></span>
      </button>

      <p class="text-center text-xs text-gray-400 flex items-center justify-center gap-1">
        <i class="ti ti-shield-check text-green-500"></i>
        Secured by Flutterwave. Your payment details are encrypted.
      </p>
    </div>

    <!-- Sidebar info -->
    <div class="space-y-5">
      <div class="bg-navy rounded-2xl p-7 text-white">
        <h3 class="text-xl font-bold mb-4">Why Give?</h3>
        <div class="space-y-4 text-gray-400 text-sm">
          <div class="flex gap-3">
            <i class="ti ti-broadcast text-gold text-xl flex-shrink-0"></i>
            <p>Supports live streaming and media outreach reaching thousands globally</p>
          </div>
          <div class="flex gap-3">
            <i class="ti ti-map text-gold text-xl flex-shrink-0"></i>
            <p>Funds missionary trips and healing crusades across Africa and beyond</p>
          </div>
          <div class="flex gap-3">
            <i class="ti ti-device-mobile text-gold text-xl flex-shrink-0"></i>
            <p>Keeps the app and radio free for the congregation worldwide</p>
          </div>
          <div class="flex gap-3">
            <i class="ti ti-pray text-gold text-xl flex-shrink-0"></i>
            <p>Enables prayer support and pastoral care for members in the diaspora</p>
          </div>
        </div>
      </div>

      <div class="bg-gray-50 rounded-2xl p-5 text-sm text-gray-600">
        <p class="font-semibold text-gray-900 mb-2">Payment Methods</p>
        <div class="space-y-2">
          <div class="flex items-center gap-2"><i class="ti ti-credit-card text-gold"></i> Visa / Mastercard (all currencies)</div>
          <div class="flex items-center gap-2"><i class="ti ti-device-mobile text-gold"></i> Mobile Money (Uganda, Africa)</div>
          <div class="flex items-center gap-2"><i class="ti ti-brand-paypal text-blue-600"></i> PayPal (diaspora)</div>
        </div>
      </div>

      <div class="bg-gold/10 border border-gold/20 rounded-2xl p-5">
        <p class="font-medium text-gray-900 mb-1 flex items-center gap-2">
          <i class="ti ti-heart text-gold"></i> Every gift matters
        </p>
        <p class="text-sm text-gray-600">
          No amount is too small. Your faithfulness in giving enables this ministry
          to preach the gospel and heal the nations.
        </p>
      </div>
    </div>
  </div>

</main>

<!-- Flutterwave inline checkout will be injected here -->
<div id="flutterwave-checkout"></div>

<?php include '../partials/radio-bar.php'; ?>
<?php include '../partials/footer.php'; ?>

<script src="https://checkout.flutterwave.com/v3.js"></script>
<script>
const FLW_PUBLIC_KEY = 'YOUR_FLUTTERWAVE_PUBLIC_KEY'; // Replace in production

function givingPage() {
  return {
    form: {
      giving_type: 'offering',
      currency:    'USD',
      amount:      '',
      donor_name:  '',
      donor_email: '',
      campaign_id: null
    },
    types: [
      { label:'Tithe',    value:'tithe' },
      { label:'Offering', value:'offering' },
      { label:'Project',  value:'project' },
      { label:'Campaign', value:'campaign' }
    ],
    quickAmounts: [10, 20, 50, 100],
    campaigns:    [],
    processing:   false,
    error:        '',

    async init() {
      try { this.campaigns = await apiFetch('/api/campaigns'); } catch {}
    },

    async proceed() {
      if (!this.form.amount || parseFloat(this.form.amount) <= 0) {
        this.error = 'Please enter an amount.';
        return;
      }
      this.processing = true;
      this.error      = '';
      try {
        const data = await (await fetch(`${API}/api/give/initiate`, {
          method:  'POST',
          headers: { 'Content-Type':'application/json' },
          body:    JSON.stringify(this.form)
        })).json();

        if (data.status !== 'success') throw new Error(data.data?.error || 'Could not initiate payment');

        const ref = data.data.reference;

        // Launch Flutterwave inline checkout
        FlutterwaveCheckout({
          public_key:   FLW_PUBLIC_KEY,
          tx_ref:       ref,
          amount:       parseFloat(this.form.amount),
          currency:     this.form.currency,
          payment_options: 'card,mobilemoneyrwanda,ussd,mobilemoneyuganda',
          customer: {
            email: this.form.donor_email || 'anonymous@roberttalemwa.online',
            name:  this.form.donor_name  || 'Anonymous'
          },
          customizations: {
            title:       'Talemwa Ministry',
            description: this.form.giving_type.charAt(0).toUpperCase() + this.form.giving_type.slice(1),
            logo:        'https://roberttalemwa.online/assets/img/logo.png'
          },
          callback: (response) => {
            if (response.status === 'successful') {
              window.location.href = '/give/thanks?ref=' + ref;
            }
          },
          onclose: () => { this.processing = false; }
        });
      } catch(e) {
        this.error     = e.message;
        this.processing = false;
      }
    }
  }
}
</script>
