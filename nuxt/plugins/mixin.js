import Vue from 'vue'
import moment from 'moment'
import { mapState } from 'vuex'

export default Vue.mixin({
  methods: {
    $ucfirst (s) {
      return s.slice(0, 1).toUpperCase() + s.slice(1)
    },

    $playerPath (game, nickname, tag) {
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

    $gamePath (game, path = '', params = {}) {
      if (! game) {
        game = this.$route.params.game
      }

      if (path !== '') {
        path = '-' + path
      }

      return this.localePath({
        name: 'game' + path,
        params: { game, ...params }
      })
    },

    $getAgent (uid) {
      const agents = this.resources.filter(r => r.type === 'agent' && r.uid === uid)

      if (agents.length) {
        return agents[0].data
      }

      return {}
    },

    $agentImage (uid, size = 'sm') {
      const slug = this.$getAgent(uid).slug

      return `/img/agents/${size}/${slug}.png`
    },

    $getMap (uid) {
      const maps = this.resources.filter(r => r.type === 'map' && r.uid === uid)

      if (maps.length) {
        return maps[0].data
      }

      return {}
    },

    $ago (time) {
      moment.locale(this.$i18n.locale)

      if (typeof time === 'string') {
        return moment.utc(time).fromNow()
      }

      return moment.utc(time).fromNow()
    },

    $agoUnix (time) {
      return this.$ago(moment.unix(time))
    }
  },

  computed: {
    ...mapState(['resources']),

    $locale () {
      return this.$i18n.locales.filter(l => l.code === this.$i18n.locale)[0]
    },
  }
})
