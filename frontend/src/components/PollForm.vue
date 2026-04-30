<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
  loading: { type: Boolean, default: false },
})

const emit = defineEmits(['submit'])

const title = ref('')
const titleTouched = ref(false)
const submitAttempted = ref(false)

const options = ref(['', ''])

const normalizedTitle = computed(() => title.value.trim())
const titleError = computed(() => {
  const len = normalizedTitle.value.length
  if (len < 5) return 'Title must be at least 5 characters'
  if (len > 100) return 'Title must be at most 100 characters'
  return null
})

const normalizedOptions = computed(() =>
  options.value.map((o) => o.trim()).filter((o) => o.length > 0),
)

const optionsErrors = computed(() => {
  const trimmed = options.value.map((o) => o.trim())

  const errors = []
  for (let i = 0; i < trimmed.length; i++) {
    const v = trimmed[i]
    if (!v) errors[i] = 'Option is required'
    else errors[i] = null
  }

  const seen = new Map()
  for (let i = 0; i < trimmed.length; i++) {
    const key = trimmed[i].toLowerCase()
    if (!key) continue
    if (seen.has(key)) {
      errors[i] = 'Duplicate option'
      errors[seen.get(key)] = 'Duplicate option'
    } else {
      seen.set(key, i)
    }
  }

  const nonEmptyCount = trimmed.filter(Boolean).length
  if (nonEmptyCount < 2) {
    for (let i = 0; i < trimmed.length; i++) {
      if (!errors[i]) errors[i] = 'Provide at least 2 options'
    }
  }

  return errors
})

const isValid = computed(() => {
  if (titleError.value) return false
  const anyOptionError = optionsErrors.value.some(Boolean)
  if (anyOptionError) return false
  return true
})

function addOption() {
  if (options.value.length >= 4) return
  options.value.push('')
}

function removeOption(idx) {
  if (options.value.length <= 2) return
  options.value.splice(idx, 1)
}

function onSubmit() {
  submitAttempted.value = true
  if (!isValid.value) return
  emit('submit', { title: normalizedTitle.value, options: normalizedOptions.value })
}

watch(
  () => props.loading,
  (loading) => {
    if (!loading) return
    submitAttempted.value = true
  },
)
</script>

<template>
  <form class="card" @submit.prevent="onSubmit">
    <h1>Create a poll</h1>

    <div class="field">
      <label for="title">Poll title</label>
      <input
        id="title"
        v-model="title"
        type="text"
        placeholder="e.g. What should we eat for lunch?"
        :disabled="loading"
        @blur="titleTouched = true"
      />
      <p v-if="(titleTouched || submitAttempted) && titleError" class="error">
        {{ titleError }}
      </p>
    </div>

    <div class="field">
      <label>Options</label>

      <div class="options">
        <div v-for="(_, idx) in options" :key="idx" class="optionRow">
          <input
            v-model="options[idx]"
            type="text"
            :placeholder="`Option ${idx + 1}`"
            :disabled="loading"
          />
          <button
            v-if="options.length > 2"
            type="button"
            class="secondary"
            :disabled="loading"
            @click="removeOption(idx)"
          >
            Remove
          </button>
          <p v-if="submitAttempted && optionsErrors[idx]" class="error">
            {{ optionsErrors[idx] }}
          </p>
        </div>
      </div>

      <button type="button" class="secondary" :disabled="loading || options.length >= 4" @click="addOption">
        Add option
      </button>
    </div>

    <button type="submit" :disabled="loading || !isValid">
      {{ loading ? 'Creating…' : 'Create poll' }}
    </button>
  </form>
</template>

