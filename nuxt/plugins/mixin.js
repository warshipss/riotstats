import Vue from 'vue'

export default Vue.mixin({
  methods: {
    $playerPath (nickname, tag, game) {
      if (! game) {
        game = this.$route.params.game
      }

      return this.localePath({
        name: 'game-player-nickname-tag',
        params: {
          tag,
          game,
          nickname,
        }
      })
    },

    $gamePath (game, path, params = {}) {
      if (! game) {
        game = this.$route.params.game
      }

      return this.localePath({
        name: 'game' + path,
        params: { game, ...params }
      })
    }
  }
})
