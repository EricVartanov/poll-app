<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useStore } from 'vuex'

import ResultsList from '../components/ResultsList.vue'

const store = useStore()
const route = useRoute()

const code = computed(() => route.params.code)

const poll = computed(() => store.state.currentPoll)
const loading = computed(() => store.state.loading)
const error = computed(() => store.state.error)
const hasVoted = computed(() => store.state.hasVoted)
console.log(poll)
const selectedOptionId = ref(null)

onMounted(async () => {
  await store.dispatch('fetchPoll', code.value)
})

const canVote = computed(() => selectedOptionId.value !== null && !loading.value)

async function submitVote() {
  if (!poll.value) return
  if (selectedOptionId.value === null) return
  await store.dispatch('submitVote', { shortCode: code.value, optionId: selectedOptionId.value })
}
</script>

<template>
  <main class="container">
    <div v-if="loading" class="card">
      Loading...
    </div>

    <div v-else-if="error && !hasVoted" class="card">
      <p class="error">{{ error }}</p>
    </div>

    <div v-else-if="hasVoted">
      <p v-if="error" class="error globalError">{{ error }}</p>
      <ResultsList />
    </div>

    <section v-else-if="poll" class="card">
      <h1>{{ poll.title }}</h1>
      <p class="subtle">Choose one option and vote.</p>

      <form class="voteForm" @submit.prevent="submitVote">
        <fieldset class="radios" :disabled="loading">
          <label v-for="opt in poll.options" :key="opt.id" class="radioRow">
            <input v-model="selectedOptionId" type="radio" name="option" :value="opt.id" />
            <span>{{ opt.text }}</span>
          </label>
        </fieldset>

        <button type="submit" :disabled="!canVote">
          {{ loading ? 'Voting…' : 'Vote' }}
        </button>
      </form>

      <p v-if="error" class="error globalError">{{ error }}</p>
    </section>

    <div v-else class="card">
      <p class="error">Poll not found</p>
    </div>
  </main>
</template>

