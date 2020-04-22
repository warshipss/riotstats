export const state = () => ({
  resources: [],
})

export const mutations = {
  set (state, payload) {
    for (const key in payload)
    {
      if (state.hasOwnProperty(key)) {
        state[key] = payload[key]
      }
    }
  }
}

export const actions = {
  async nuxtServerInit ({ commit }, { app }) {
    const { data } = await app.$axios.get('/resource')

    commit('set', {
      resources: data.data,
    })
  }
}
