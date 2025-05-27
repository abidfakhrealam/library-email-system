<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }}
    </label>
    <div class="relative">
      <input
        type="text"
        v-model="search"
        @focus="open = true"
        @blur="onBlur"
        class="w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 text-sm focus:ring-blue-500 focus:border-blue-500"
        :placeholder="placeholder"
      />
      <div v-show="open" class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto max-h-60 focus:outline-none sm:text-sm">
        <div
          v-for="tag in filteredTags"
          :key="tag.id"
          @mousedown.prevent="toggleTag(tag)"
          class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-gray-100"
          :class="{ 'bg-blue-50': isSelected(tag) }"
        >
          <div class="flex items-center">
            <span class="inline-block h-3 w-3 rounded-full mr-2" :style="{ backgroundColor: tag.color }"></span>
            <span class="font-normal block truncate" :class="{ 'font-semibold': isSelected(tag) }">
              {{ tag.name }}
            </span>
          </div>
          <span v-if="isSelected(tag)" class="absolute inset-y-0 right-0 flex items-center pr-4 text-blue-600">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
          </span>
        </div>
      </div>
    </div>
    <div v-if="selectedTags.length > 0" class="mt-2 flex flex-wrap gap-2">
      <span
        v-for="tag in selectedTags"
        :key="tag.id"
        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
        :style="{ backgroundColor: tag.color, color: getContrastColor(tag.color) }"
      >
        {{ tag.name }}
        <button
          type="button"
          @click="removeTag(tag)"
          class="ml-1.5 inline-flex items-center justify-center rounded-full h-4 w-4 hover:bg-black hover:bg-opacity-20"
        >
          <span class="sr-only">Remove</span>
          <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
            <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" />
          </svg>
        </button>
      </span>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    value: {
      type: Array,
      default: () => []
    },
    tags: {
      type: Array,
      required: true
    },
    label: {
      type: String,
      default: ''
    },
    placeholder: {
      type: String,
      default: 'Search tags...'
    }
  },
  data() {
    return {
      open: false,
      search: '',
      selectedTags: [...this.value]
    };
  },
  computed: {
    filteredTags() {
      const searchTerm = this.search.toLowerCase();
      return this.tags.filter(tag => 
        tag.name.toLowerCase().includes(searchTerm) &&
        !this.selectedTags.some(t => t.id === tag.id)
      );
    }
  },
  methods: {
    toggleTag(tag) {
      if (this.isSelected(tag)) {
        this.removeTag(tag);
      } else {
        this.addTag(tag);
      }
    },
    addTag(tag) {
      this.selectedTags.push(tag);
      this.$emit('input', this.selectedTags);
      this.search = '';
    },
    removeTag(tag) {
      this.selectedTags = this.selectedTags.filter(t => t.id !== tag.id);
      this.$emit('input', this.selectedTags);
    },
    isSelected(tag) {
      return this.selectedTags.some(t => t.id === tag.id);
    },
    onBlur() {
      setTimeout(() => {
        this.open = false;
      }, 200);
    },
    getContrastColor(hexColor) {
      // Convert hex to RGB
      const r = parseInt(hexColor.substr(1, 2), 16);
      const g = parseInt(hexColor.substr(3, 2), 16);
      const b = parseInt(hexColor.substr(5, 2), 16);
      
      // Calculate luminance
      const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
      
      // Return black for light colors, white for dark colors
      return luminance > 0.5 ? '#000000' : '#ffffff';
    }
  },
  watch: {
    value(newVal) {
      this.selectedTags = [...newVal];
    }
  }
};
</script>
