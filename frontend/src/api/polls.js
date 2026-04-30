import axios from 'axios'

const baseURL = import.meta.env.VITE_API_URL ?? 'http://localhost:8000'

const http = axios.create({
  baseURL,
  withCredentials: false,
})

export const pollsApi = {
  async create(title, options) {
    const res = await http.post('/api/polls', { title, options })
    return res.data
  },

  async get(shortCode) {
    const res = await http.get(`/api/polls/${encodeURIComponent(shortCode)}`)
    return res.data
  },

  async vote(shortCode, optionId) {
    const res = await http.post(`/api/polls/${encodeURIComponent(shortCode)}/vote`, {
      option_id: optionId,
    })
    return res.data
  },
}

