<template>
  <div class="flex align-middle z-10 h-10 md:mt-0">

    <button class="z-20 h-full ml-6 mr-3 focus:outline-none"
            v-show="!searching" @click="showInput"
    >
      <svg class="search-toggle fill-current h-5 w-5" viewBox="0 0 57.231 57.231" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
        <g>
          <g>
            <path d="M23.945,9.373c-1.652,0-2.992,1.341-2.992,2.992c0,1.654,1.34,2.992,2.992,2.992 c3.656,0,6.921,2.042,8.524,5.333c0.519,1.062,1.583,1.68,2.691,1.68c0.441,0,0.888-0.097,1.309-0.303 c1.486-0.723,2.104-2.514,1.379-4.001C35.235,12.704,29.906,9.373,23.945,9.373z"/>
            <path d="M56.135,50.469l-9.971-9.972c2.952-4.152,4.705-9.213,4.705-14.685 c0-14.025-11.41-25.435-25.435-25.435S0,11.787,0,25.812c0,14.024,11.409,25.434,25.434,25.434c5.859,0,11.245-2.01,15.549-5.351 l9.862,9.862c0.73,0.73,1.689,1.096,2.645,1.096c0.958,0,1.915-0.365,2.645-1.096C57.596,54.298,57.596,51.93,56.135,50.469z M5.986,25.813c0-10.725,8.726-19.45,19.45-19.45s19.45,8.725,19.45,19.45c0,4.462-1.525,8.566-4.063,11.852 c-0.839,1.085-1.781,2.086-2.827,2.973c-3.392,2.878-7.773,4.625-12.56,4.625C14.711,45.262,5.986,36.537,5.986,25.813z"/>
          </g>
        </g>
      </svg>
    </button>

    <div v-show="searching" class="absolute right-0 w-full">
      <button class="absolute right-0 h-10 z-20 ml-4 lg:pl-6 mr-3.5 focus:outline-none"
              @click="close"
      >
        <svg xmlns="http://www.w3.org/2000/svg"
             class="search-toggle fill-current h-4 w-4" viewBox="0 0 32 32">
          <polygon points="32.8,4.4 28.6,0.2 18,10.8 7.4,0.2 3.2,4.4 13.8,15 3.2,25.6 7.4,29.8 18,19.2 28.6,29.8 32.8,25.6 22.2,15 "/>
        </svg>
      </button>

      <div class="flex flex-col items-end">
        <input
          v-if="searching"
          id="search"
          v-model="query"
          ref="search"
          class="h-10 w-1/2 rounded-lg placeholder-gray-400 dark:placeholder-neutral-400 bg-gray-200 dark:bg-neutral-900 outline-none px-4 pb-0 pt-px"
          :class="{ 'transition-border': query }"
          autocomplete="off"
          name="search"
          placeholder="Search"
          type="text"
          @keyup.esc="close"
          @focusout="close"
        >

        <div v-if="query" class="w-1/2 md:inset-auto text-left mb-4">
          <div class="flex flex-col bg-gray-200 dark:bg-neutral-900 rounded-b-lg mx-4 md:mx-0">
            <a
              v-for="(result, index) in results"
              class="text-xl cursor-pointer p-4"
              :class="{ 'rounded-b-lg': (index === results.length - 1) }"
              :href="result.item.link"
              :title="result.item.title"
              :key="result.link"
              @mousedown.prevent
            >
              <span class="uppercase font-semibold">{{ result.item.title }}</span>

              <span class="block text-gray-600 dark:text-neutral-300 normal-case text-sm my-1"
                    v-html="result.item.snippet"></span>
            </a>

            <div
              v-if="! results.length"
              class="text-gray-400 dark:text-neutral-400 rounded-b-lg cursor-pointer p-4"
            >
              <p class="my-0">No results for <strong>{{ query }}</strong></p>
            </div>
          </div>
        </div>
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
      return this.query ? this.fuse.search(this.query) : [];
    },
  },
  methods: {
    showInput() {
      this.searching = true;
      this.$nextTick(() => {
        this.$refs.search.focus();
      })
    },
    close() {
      this.query = '';
      this.searching = false;
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
};
</script>

<style>
input[name='search'] {
  background-position: 0.8em;
  background-repeat: no-repeat;
}

input[name='search'].transition-border {
  border-bottom-left-radius: 0;
  border-bottom-right-radius: 0;
  border-top-left-radius: .5rem;
  border-top-right-radius: .5rem;
}
</style>
