<?php $pageTitle = 'Upload Sermon'; $activePage = 'sermons-upload'; ?>
<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<div class="ml-64 flex-1 flex flex-col min-h-screen">

  <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center gap-4 sticky top-0 z-20">
    <a href="/pages/sermons-list.php" class="text-gray-400 hover:text-gray-700 transition">
      <i class="ti ti-arrow-left text-xl"></i>
    </a>
    <div>
      <h1 class="text-xl font-semibold text-gray-900" id="page-heading">Upload Sermon</h1>
      <p class="text-sm text-gray-500 mt-0.5">Add a new message to the sermon archive</p>
    </div>
  </header>

  <main class="flex-1 p-8" x-data="sermonForm()" x-init="init()">
    <div class="max-w-3xl mx-auto">

      <form @submit.prevent="submit" class="space-y-6">

        <!-- Title -->
        <div class="bg-white border border-gray-200 rounded-2xl p-6">
          <h2 class="font-semibold text-gray-900 mb-4">Sermon Details</h2>
          <div class="space-y-4">

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Title <span class="text-red-500">*</span></label>
              <input x-model="form.title" type="text" required placeholder="e.g. Walking in Divine Healing"
                     class="input-field">
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Series</label>
                <input x-model="form.series" type="text" list="series-list" placeholder="Select or type new series"
                       class="input-field">
                <datalist id="series-list">
                  <template x-for="s in seriesList" :key="s.series">
                    <option :value="s.series"></option>
                  </template>
                </datalist>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Speaker</label>
                <input x-model="form.speaker" type="text" placeholder="Pastor Robert Talemwa"
                       class="input-field">
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Scripture Reference</label>
                <input x-model="form.scripture" type="text" placeholder="e.g. John 3:16"
                       class="input-field">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tags (comma-separated)</label>
                <input x-model="form.tags" type="text" placeholder="healing, faith, miracles"
                       class="input-field">
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
              <textarea x-model="form.description" rows="3" placeholder="Brief description of the sermon…"
                        class="input-field resize-none"></textarea>
            </div>

          </div>
        </div>

        <!-- YouTube URL -->
        <div class="bg-white border border-gray-200 rounded-2xl p-6">
          <h2 class="font-semibold text-gray-900 mb-1">YouTube Video</h2>
          <p class="text-sm text-gray-500 mb-4">Paste the full YouTube URL — thumbnail and duration will be fetched automatically</p>

          <div class="flex gap-3">
            <input x-model="youtubeUrl" type="url" placeholder="https://www.youtube.com/watch?v=…"
                   class="input-field flex-1">
            <button type="button" @click="fetchYoutube()" :disabled="fetchingYt"
                    class="px-4 py-2.5 bg-navy text-white text-sm font-medium rounded-xl hover:bg-navy-700 transition disabled:opacity-60 whitespace-nowrap flex items-center gap-2">
              <svg x-show="fetchingYt" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              <span x-text="fetchingYt ? 'Fetching…' : 'Fetch Info'"></span>
            </button>
          </div>

          <!-- YouTube preview -->
          <div x-show="form.thumbnail_url" x-cloak class="mt-4 flex items-start gap-4 p-3 bg-gray-50 rounded-xl">
            <img :src="form.thumbnail_url" class="w-24 h-16 rounded object-cover">
            <div>
              <p class="text-sm font-medium text-gray-900" x-text="ytTitle || form.title"></p>
              <p class="text-xs text-gray-500 mt-0.5" x-text="form.duration_seconds ? formatDuration(form.duration_seconds) : ''"></p>
              <p class="text-xs text-green-600 mt-1 flex items-center gap-1">
                <i class="ti ti-check"></i> YouTube info fetched
              </p>
            </div>
          </div>
        </div>

        <!-- Thumbnail + options -->
        <div class="bg-white border border-gray-200 rounded-2xl p-6">
          <h2 class="font-semibold text-gray-900 mb-4">Publishing Options</h2>
          <div class="space-y-4">

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Custom Thumbnail URL <span class="text-gray-400 font-normal">(optional — overrides YouTube thumbnail)</span></label>
              <input x-model="form.thumbnail_url" type="url" placeholder="https://…"
                     class="input-field">
            </div>

            <div class="flex items-center justify-between py-3 border-t border-gray-100">
              <div>
                <p class="text-sm font-medium text-gray-700">Publish immediately</p>
                <p class="text-xs text-gray-400">If off, saves as draft</p>
              </div>
              <button type="button" @click="form.published = form.published ? 0 : 1"
                      :class="form.published ? 'bg-green-500' : 'bg-gray-200'"
                      class="relative w-11 h-6 rounded-full transition">
                <span :class="form.published ? 'translate-x-6' : 'translate-x-1'"
                      class="absolute top-1 left-0 w-4 h-4 bg-white rounded-full shadow transition-transform"></span>
              </button>
            </div>

            <div class="flex items-center justify-between py-3 border-t border-gray-100">
              <div>
                <p class="text-sm font-medium text-gray-700">Send push notification on publish</p>
                <p class="text-xs text-gray-400">Notifies all app users: "🎙️ New Sermon: {title}"</p>
              </div>
              <button type="button" @click="form.notify = !form.notify"
                      :class="form.notify ? 'bg-navy' : 'bg-gray-200'"
                      class="relative w-11 h-6 rounded-full transition">
                <span :class="form.notify ? 'translate-x-6' : 'translate-x-1'"
                      class="absolute top-1 left-0 w-4 h-4 bg-white rounded-full shadow transition-transform"></span>
              </button>
            </div>

          </div>
        </div>

        <!-- Error -->
        <div x-show="error" x-cloak class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3" x-text="error"></div>

        <!-- Submit -->
        <div class="flex gap-3">
          <a href="/pages/sermons-list.php"
             class="flex-1 text-center border border-gray-200 text-gray-600 py-3 rounded-xl font-medium hover:bg-gray-50 transition">
            Cancel
          </a>
          <button type="submit" :disabled="saving"
                  class="flex-1 bg-navy text-white py-3 rounded-xl font-semibold hover:bg-navy-700 transition disabled:opacity-60 flex items-center justify-center gap-2">
            <svg x-show="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <span x-text="saving ? 'Saving…' : (editId ? 'Save Changes' : 'Upload Sermon')"></span>
          </button>
        </div>

      </form>
    </div>
  </main>
</div>

<style>
  .input-field {
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 0.625rem 1rem;
    font-size: 0.875rem;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
  }
  .input-field:focus { border-color: #C9A84C; box-shadow: 0 0 0 1px #C9A84C; }
</style>

<script>
function sermonForm() {
  return {
    editId:    null,
    form: {
      title: '', series: '', speaker: 'Pastor Robert Talemwa',
      description: '', youtube_url: '', mp3_url: '', duration_seconds: 0,
      thumbnail_url: '', scripture: '', tags: '', published: 1, notify: true
    },
    youtubeUrl: '',
    ytTitle:    '',
    fetchingYt: false,
    seriesList: [],
    saving:     false,
    error:      '',

    async init() {
      authGuard();
      await this.loadSeries();

      // Edit mode — check ?id= param
      const params = new URLSearchParams(window.location.search);
      if (params.get('id')) {
        this.editId = params.get('id');
        document.getElementById('page-heading').textContent = 'Edit Sermon';
        await this.loadExisting(this.editId);
      }
    },

    async loadSeries() {
      try { this.seriesList = await apiGet('/api/sermons/series'); } catch {}
    },

    async loadExisting(id) {
      try {
        const s          = await apiGet(`/api/sermons/${id}`);
        this.form        = { ...this.form, ...s };
        this.youtubeUrl  = s.youtube_url || '';
      } catch {}
    },

    async fetchYoutube() {
      const url = this.youtubeUrl.trim();
      if (!url) return;

      // Extract video ID
      const match = url.match(/(?:v=|youtu\.be\/)([A-Za-z0-9_-]{11})/);
      if (!match) { this.error = 'Could not find a YouTube video ID in that URL.'; return; }
      const videoId = match[1];

      this.fetchingYt = true;
      this.error      = '';
      try {
        const res  = await fetch(`https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v=${videoId}&format=json`);
        if (!res.ok) throw new Error('Could not fetch video info');
        const data = await res.json();

        this.form.youtube_url   = url;
        this.form.thumbnail_url = `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;
        this.ytTitle            = data.title;
        if (!this.form.title)   this.form.title = data.title;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.fetchingYt = false;
      }
    },

    async submit() {
      if (!this.form.title) { this.error = 'Title is required.'; return; }
      this.saving = true;
      this.error  = '';
      try {
        if (this.editId) {
          await apiPut(`/api/admin/sermons/${this.editId}`, this.form);
          showToast('Sermon updated');
        } else {
          await apiPost('/api/admin/sermons', this.form);
          showToast('Sermon uploaded' + (this.form.notify && this.form.published ? ' · Push notification sent' : ''));
        }
        setTimeout(() => window.location.href = '/pages/sermons-list.php', 800);
      } catch (e) {
        this.error = e.message;
      } finally {
        this.saving = false;
      }
    }
  }
}
</script>

<?php include '../partials/footer.php'; ?>
