<template>
  <div class="flex items-center">
    <button
      type="button"
      class="icon-btn"
      aria-label="Search posts"
      :aria-expanded="searching ? 'true' : 'false'"
      @mousedown.prevent
      @click="searching ? close() : showInput()"
    >
      <svg v-if="!searching" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true">
        <circle cx="11" cy="11" r="7"/>
        <path d="M20 20l-3.5-3.5"/>
      </svg>
      <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true">
        <path d="M6 6l12 12M18 6L6 18"/>
      </svg>
    </button>

    <div v-if="searching" class="search-panel">
      <label class="search-field">
        <span class="sr-only">Search posts</span>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true">
          <circle cx="11" cy="11" r="7"/>
          <path d="M20 20l-3.5-3.5"/>
        </svg>
        <input
          id="search"
          ref="search"
          v-model="query"
          autocomplete="off"
          name="search"
          placeholder="Search posts by title, text or category"
          type="search"
          @keyup.esc="close"
          @blur="close"
        >
        <kbd class="font-mono">esc</kbd>
      </label>

      <div v-if="query" class="search-results">
        <a
          v-for="result in results"
          :key="result.item.link"
          :href="result.item.link"
          class="search-result"
          @mousedown.prevent
        >
          <span class="search-result-title">{{ result.item.title }}</span>
          <span class="search-result-snippet" v-html="result.item.snippet"></span>
        </a>

        <p v-if="!results.length" class="search-empty">
          No posts match <strong>{{ query }}</strong>. Searches need 3 or more characters.
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import Fuse from 'fuse.js';

export default {
  data() {
    return {
      fuse: null,
      searching: false,
      query: ''
    };
  },
  computed: {
    results() {
      return this.query && this.fuse ? this.fuse.search(this.query) : [];
    },
  },
  watch: {
    searching(open) {
      document.documentElement.classList.toggle('search-open', open);
      if (open) {
        window.dispatchEvent(new CustomEvent('nav:open', { detail: 'search' }));
      }
    },
  },
  methods: {
    showInput() {
      this.searching = true;
      this.$nextTick(() => {
        this.$refs.search.focus();
      });
    },
    close() {
      this.query = '';
      this.searching = false;
    },
    onKeydown(event) {
      const typing = ['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName);

      if (event.key === '/' && !typing && !this.searching) {
        event.preventDefault();
        this.showInput();
      }
    },
    onNavOpen(event) {
      if (event.detail !== 'search') this.close();
    },
  },
  created() {
    axios('/index.json').then(response => {
      this.fuse = new Fuse(response.data, {
        minMatchCharLength: 3,
        keys: ['title', 'snippet', 'categories'],
      });
    });
  },
  mounted() {
    document.addEventListener('keydown', this.onKeydown);
    window.addEventListener('nav:open', this.onNavOpen);
    window.addEventListener('nav:close', this.close);
  },
  beforeDestroy() {
    document.removeEventListener('keydown', this.onKeydown);
    window.removeEventListener('nav:open', this.onNavOpen);
    window.removeEventListener('nav:close', this.close);
  },
};
</script>

<style>
.search-panel {
  position: absolute;
  top: 100%;
  left: -1px;
  right: -1px;
  z-index: 30;
  background: var(--paper);
  border: 1px solid var(--rule);
}

.search-field {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  height: 3.5rem;
  padding: 0 1.25rem;
  color: var(--ink);
}

.search-field input {
  flex: 1;
  min-width: 0;
  border: 0;
  outline: none;
  background: transparent;
  color: var(--ink);
  font: inherit;
  font-size: 1.0625rem;
}

.search-field input::placeholder {
  color: var(--faint);
}

.search-field kbd {
  font-size: 0.6875rem;
  padding: 0.2rem 0.45rem;
  border: 1px dashed var(--rule);
  color: var(--faint);
}

.search-results {
  border-top: 1px solid var(--rule);
  max-height: 60vh;
  overflow-y: auto;
}

.search-result {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  padding: 1rem 1.25rem;
  border-top: 1px dashed var(--rule-soft);
}

.search-result:first-child {
  border-top: 0;
}

.search-result:hover .search-result-title,
.search-result:focus-visible .search-result-title {
  background-image: linear-gradient(transparent var(--hl-from), var(--hl) var(--hl-from), var(--hl) var(--hl-to), transparent var(--hl-to));
}

.search-result-title {
  font-weight: 700;
  font-size: 1.125rem;
  align-self: flex-start;
}

.search-result-snippet {
  font-size: 0.9375rem;
  color: var(--muted);
}

.search-empty {
  margin: 0;
  padding: 1rem 1.25rem;
  color: var(--muted);
}
</style>
