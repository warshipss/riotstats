<template>
  <nav class="navbar navbar-expand-lg navbar-dark">
    <nuxt-link :to="localePath('index')" class="navbar-brand" href="#">RiotStats</nuxt-link>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
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
    </div>
  </nav>
</template>
