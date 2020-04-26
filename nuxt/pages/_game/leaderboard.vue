<template>
  <div>
    <h1>{{ $t('Leaderboard') }}</h1>
    <p>{{ $t('This feature is coming soon. Stay tuned!') }}</p>
  </div>
</template>

<script>
  export default {
    head() {
      return {
        title: this.$t('Leaderboard'),

        meta: [
          { hid: 'description', name: 'description', content: this.$t('Best Valorant players by agent and overall by RiotStats') },
        ]
      }
    }
  }
</script>
