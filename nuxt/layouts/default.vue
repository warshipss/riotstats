<template>
  <div id="app">
    <header>
      <v-header />

      <div class="jumbotron">
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
  </div>
</template>

<script>
  import VHeader from '~/components/header'
  import VFooter from '~/components/footer'
  import VSearch from '~/components/search'
  import GlobalMixin from '../plugins/mixin'

  export default
  {
    mixins: [GlobalMixin],
    components: { VHeader, VFooter, VSearch },
  }
</script>
