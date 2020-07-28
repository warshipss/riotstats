<template>
  <header>
    <div class="navbar-wrapper">
      <div class="container">
        <nav class="navbar navbar_global">
          <ul class="navbar-nav mr-auto">
            <li class="nav-item">
              <nuxt-link :to="$gamePath('valorant')" class="nav-link">
                <img src="/img/icons/valorant.svg" /> {{ $t('Valorant') }}
              </nuxt-link>
            </li>
          </ul>

          <ul class="navbar-nav">
            <li class="nav-item">
              <a href="#" class="nav-link nav-link_language" @click.prevent v-b-modal="'language'">
                <i class="icon icon-globe" /> {{ $locale.title }}
              </a>
            </li>
          </ul>
        </nav>
      </div>

      <nav class="navbar navbar_per-game">
        <div class="container">
          <ul class="navbar-nav mr-auto">
            <li class="nav-item">
              <nuxt-link :to="$gamePath('valorant', 'leaderboards')" class="nav-link">
                {{ $t('Leaderboards') }}
              </nuxt-link>
            </li>
          </ul>

          <ul class="navbar-nav">
          </ul>
        </div>
      </nav>

      <div class="navbar-wrapper__logo-area">
        <nuxt-link :to="localePath('index')" class="navbar-brand" href="#">
          <img src="/img/logo.png" class="navbar-wrapper__logo" />
        </nuxt-link>
      </div>
    </div>

    <div class="jumbotron">
      <div class="jumbotron__background"></div>

      <v-search />
    </div>
  </header>
</template>

<script>
  import VSearch from '~/components/search'

  export default {
    components: { VSearch },
  }
</script>
