import { createStore } from 'vuex'
import { pollsApi } from '../api/polls'

const initialState = () => ({
  currentPoll: null,
  results: [],
  hasVoted: false,
  loading: false,
  error: null,
})

export default createStore({
  state: initialState(),
  mutations: {
    SET_POLL(state, poll) {
      state.currentPoll = poll
    },
    SET_RESULTS(state, results) {
      state.results = Array.isArray(results) ? results : []
    },
    SET_HAS_VOTED(state, hasVoted) {
      state.hasVoted = Boolean(hasVoted)
    },
    SET_LOADING(state, loading) {
      state.loading = Boolean(loading)
    },
    SET_ERROR(state, error) {
      state.error = error ?? null
    },
    RESET(state) {
      Object.assign(state, initialState())
    },
  },
  actions: {
    async fetchPoll({ commit }, shortCode) {
      commit('SET_LOADING', true)
      commit('SET_ERROR', null)
      try {
        const data = await pollsApi.get(shortCode)
        commit('SET_POLL', data)

        if (data?.has_voted) {
          commit('SET_RESULTS', data.results ?? [])
          commit('SET_HAS_VOTED', true)
        } else {
          commit('SET_RESULTS', [])
          commit('SET_HAS_VOTED', false)
        }
      } catch (err) {
        commit('SET_ERROR', 'Failed to load poll')
      } finally {
        commit('SET_LOADING', false)
      }
    },

    async submitVote({ commit, dispatch }, { shortCode, optionId }) {
      commit('SET_LOADING', true)
      commit('SET_ERROR', null)
      try {
        const data = await pollsApi.vote(shortCode, optionId)
        commit('SET_RESULTS', data.votes ?? [])
        commit('SET_HAS_VOTED', true)
      } catch (err) {
        const status = err?.response?.status
        if (status === 409) {
          commit('SET_HAS_VOTED', true)
          commit('SET_ERROR', 'You have already voted')
          try {
            const data = await pollsApi.get(shortCode)
            commit('SET_POLL', data)
            commit('SET_RESULTS', data.results ?? [])
          } catch {
            // Keep the already-voted message, even if refetch fails.
          }
          return
        }
        commit('SET_ERROR', 'Failed to submit vote')
      } finally {
        commit('SET_LOADING', false)
      }
    },

    async createPoll({ commit }, { title, options }) {
      commit('SET_LOADING', true)
      commit('SET_ERROR', null)
      try {
        const data = await pollsApi.create(title, options)
        return data.short_code
      } catch (err) {
        commit('SET_ERROR', 'Failed to create poll')
        throw err
      } finally {
        commit('SET_LOADING', false)
      }
    },
  },
})

