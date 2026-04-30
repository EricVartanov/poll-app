<script setup>
import { computed } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'

import PollForm from '../components/PollForm.vue'

const store = useStore()
const router = useRouter()

const loading = computed(() => store.state.loading)
const error = computed(() => store.state.error)

async function onCreate({ title, options }) {
  const shortCode = await store.dispatch('createPoll', { title, options })
  router.push(`/poll/${shortCode}`)
}
</script>

<template>
  <main class="container">
    <PollForm :loading="loading" @submit="onCreate" />
    <p v-if="error" class="error globalError">{{ error }}</p>
  </main>
</template>

