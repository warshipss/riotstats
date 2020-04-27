<template>
  <nav class="navbar navbar-expand-md navbar-dark">
    <nuxt-link :to="localePath('index')" class="navbar-brand" href="#">RiotStats</nuxt-link>

    <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

    <b-collapse id="nav-collapse" is-nav>
      <ul class="navbar-nav mr-auto">
        <li class="nav-item">
          <nuxt-link :to="$gamePath('valorant', 'leaderboard')" class="nav-link">
            {{ $t('Leaderboard') }}
          </nuxt-link>
        </li>

        <li class="nav-item">
          <nuxt-link :to="$playerPath('valorant', 'refgiton', '9020')" class="nav-link">
            refgiton#9020
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
    </b-collapse>
  </nav>
</template>
