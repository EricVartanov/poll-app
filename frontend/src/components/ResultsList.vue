<script setup>
import { computed } from 'vue'
import { useStore } from 'vuex'

const store = useStore()

const poll = computed(() => store.state.currentPoll)
const results = computed(() => store.state.results)

const total = computed(() => results.value.reduce((sum, r) => sum + (Number(r.count) || 0), 0))

function percent(count) {
  const t = total.value
  if (!t) return 0
  return (Number(count) / t) * 100
}

function formatPercent(p) {
  return `${p.toFixed(1)}%`
}
</script>

<template>
  <section v-if="poll" class="card">
    <h1>{{ poll.title }}</h1>
    <h2 class="subtle">Results</h2>

    <ul class="results">
      <li v-for="r in results" :key="r.option_id" class="resultRow">
        <div class="resultText">
          <span class="option">{{ r.option }}</span>
          <span class="meta">
            — {{ r.count }} votes ({{ formatPercent(percent(r.count)) }})
          </span>
        </div>
        <div class="bar" aria-hidden="true">
          <div class="fill" :style="{ width: `${percent(r.count)}%` }"></div>
        </div>
      </li>
    </ul>
  </section>
</template>

