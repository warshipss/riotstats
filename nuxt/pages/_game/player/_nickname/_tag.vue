<template>
  <div class="profile">
    <h1 class="profile__nickname">{{ profile.nickname }} #{{ profile.tag }}</h1>
    <h2>{{ $t('Matches') }}</h2>

    <div class="matches">
      <match :match="match" :current="profile.uid" :key="match.uid" v-for="match of profile.matches" />
    </div>

    <a href="#" class="btn btn-primary">
      {{ $t('Load more') }}
    </a>
  </div>
</template>

<script>
  import Match from '~/components/match'

  export default
  {
    components: { Match },

    head() {
      return {
        title: this.title,

        meta: [
          { property: 'og:title', content: this.title },
          { property: 'og:description', content: '' },
        ]
      }
    },

    async asyncData({ app, params }) {
      const { data } = await app.$axios.get('/player/profile', { params })

      return {
        profile: data.data,
      }
    },

    computed: {
      title () {
        return this.profile.nickname + ' #' + this.profile.tag + ' Valorant statistics'
      }
    }
  }
</script>
