<template>
  <div id="app">
    <header>
      <v-header />

      <div class="jumbotron">
        <div class="jumbotron__background"></div>

        <v-search />
      </div>
    </header>

    <main class="layout">
      <a href="#" v-if="false">
        <img src="/img/side.png" class="layout__side" />
      </a>

      <div class="container">
        <nuxt />
      </div>

      <a href="#" v-if="false">
        <img src="/img/side.png" class="layout__side" />
      </a>
    </main>

    <footer>
      <v-footer />
    </footer>

    <language-modal />
  </div>
</template>

<script>
  import GlobalMixin from '../plugins/mixin'

  import VHeader from '~/components/header'
  import VFooter from '~/components/footer'
  import VSearch from '~/components/search'
  import LanguageModal from '~/components/language'

  export default
  {
    mixins: [GlobalMixin],
    components: { VHeader, VFooter, VSearch, LanguageModal },

    head() {
      const keywords = [
        this.$t('K/D'),
        this.$t('map'),
        this.$t('elo'),
        this.$t('stats'),
        this.$t('agents'),
        this.$t('rating'),
        this.$t('weapons'),
        this.$t('ranking'),
        this.$t('tracker'),
        this.$t('progress'),
        this.$t('Valorant'),
        this.$t('team stats'),
        this.$t('statistics'),
        this.$t('kill record'),
        this.$t('leaderboard'),
        this.$t('Valorant stats'),
        this.$t('Valorant statistics'),
      ]

      return {
        meta: [
          { name: 'keywords', content: keywords.join(', ') },
          { hid: 'description', name: 'description', content: this.$t('RiotStats is the statistics, ranking, rating and match history website for Valorant') },
        ]
      }
    }
  }
</script>
