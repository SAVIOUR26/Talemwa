// ── Talemwa Website — shared JS ────────────────────────────────

const API = 'https://api.roberttalemwa.online';

async function apiFetch(path) {
  const res = await fetch(`${API}${path}`);
  if (!res.ok) throw new Error(`API error ${res.status}`);
  const json = await res.json();
  return json.data;
}

function formatDate(str) {
  if (!str) return '';
  return new Date(str).toLocaleDateString('en-GB', { weekday:'long', day:'numeric', month:'long', year:'numeric' });
}

function formatDuration(seconds) {
  if (!seconds) return '';
  const h = Math.floor(seconds / 3600);
  const m = Math.floor((seconds % 3600) / 60);
  const s = seconds % 60;
  if (h > 0) return `${h}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
  return `${m}:${String(s).padStart(2,'0')}`;
}

// ── Radio Player (shared across all pages) ─────────────────────
function radioPlayer() {
  return {
    playing:    false,
    loading:    false,
    title:      'Ministry Radio',
    artist:     '',
    art:        null,
    listeners:  0,
    isOnline:   false,
    streamUrl:  '',
    audio:      null,

    async init() {
      await this.fetchStatus();
      setInterval(() => this.fetchNowPlaying(), 30000);
    },

    async fetchStatus() {
      try {
        const data     = await apiFetch('/api/radio');
        this.streamUrl = data.stream_url;
        this.isOnline  = data.is_online;
        this.title     = data.now_playing?.title || 'Ministry Radio';
        this.artist    = data.now_playing?.artist || '';
        this.art       = data.now_playing?.art || null;
        this.listeners = data.listeners || 0;
      } catch {}
    },

    async fetchNowPlaying() {
      try {
        const data  = await apiFetch('/api/radio');
        this.title  = data.now_playing?.title || 'Ministry Radio';
        this.artist = data.now_playing?.artist || '';
        this.art    = data.now_playing?.art || null;
        this.listeners = data.listeners || 0;
      } catch {}
    },

    toggle() {
      if (!this.streamUrl) return;
      if (this.playing) {
        this.audio?.pause();
        this.playing = false;
      } else {
        this.loading = true;
        this.audio   = new Audio(this.streamUrl);
        this.audio.play()
          .then(() => { this.playing = true; this.loading = false; })
          .catch(() => { this.loading = false; });
        this.audio.addEventListener('ended', () => { this.playing = false; });
      }
    }
  }
}

// ── Live banner poll ───────────────────────────────────────────
function liveBanner() {
  return {
    isLive:    false,
    youtubeId: '',
    title:     '',

    async init() {
      await this.check();
      setInterval(() => this.check(), 60000);
    },

    async check() {
      try {
        const data     = await apiFetch('/api/live');
        this.isLive    = data.is_live;
        this.youtubeId = data.youtube_id || '';
        this.title     = data.title || 'Sunday Service';
      } catch {}
    }
  }
}
