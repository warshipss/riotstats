<template>
  <div>
    <header>
      <v-header />

      <div class="jumbotron">
        <div class="jumbotron__container">
          <h3 class="jumbotron__title">Track stats to <br />improve skill</h3>

          <div class="search">
            <h3 class="search__title">{{ $t('Search') }}</h3>

            <input class="form-control search__input" type="text" placeholder="Enter nickname#tag" aria-label="Search">
            <button class="btn search__button" type="submit">{{ $t('Search') }}</button>
          </div>
        </div>
      </div>
    </header>

    <main class="layout">
      <a href="#">
        <img src="/img/side.png" class="layout__side" />
      </a>

      <div class="container">
        <nuxt />
      </div>

<!--
        <div class="row">
          <div class="col col-md-4">
            <a class="twitter-timeline" href="https://twitter.com/riotstats_com?ref_src=twsrc%5Etfw">Tweets by riotstats_com</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
          </div>

          <div class="col col-md-8">
          </div>
        </div>-->

      <a href="#">
        <img src="/img/side.png" class="layout__side" />
      </a>
    </main>

    <footer>
      <v-footer />
    </footer>
  </div>
</template>

<script>
  import VHeader from '~/components/header'
  import VFooter from '~/components/footer'
  import GlobalMixin from '../plugins/mixin'

  export default
  {
    mixins: [GlobalMixin],
    components: { VHeader, VFooter },
  }
</script>
