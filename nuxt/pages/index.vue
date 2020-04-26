<template>
  <div class="container">
    <div class="row">
      <div class="col col-md-4">
        <h4>
          <i class="icon icon-twitter" /> Twitter
        </h4>

        <a class="twitter-timeline" data-width="400" data-theme="light" data-tweet-limit="5"
           data-chrome="transparent noheader nofooter"
           href="https://twitter.com/riotstats_com?ref_src=twsrc%5Etfw">Tweets by riotstats_com</a>
      </div>

      <div class="col col-md-8">
        <h4>
          <i class="icon icon-star" /> {{ $t('Tracked players') }}
        </h4>

        <p>{{ $t('This feature is coming soon. Stay tuned!') }}</p>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    head() {
      return {
        title: this.$t('First Valorant stats tracker'),
      }
    }
  }
</script>
