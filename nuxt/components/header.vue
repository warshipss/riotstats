<template>
  <nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">RiotStats.com</a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
          <nuxt-link :to="localePath('index')" class="nav-link" href="#">{{ $t('Home') }}</nuxt-link>
        </li>

        <li class="nav-item">
          <nuxt-link :to="$gamePath('valorant', '-leaderboard')" class="nav-link">
            {{ $t('Leaderboard') }}
          </nuxt-link>
        </li>

        <li class="nav-item">
          <nuxt-link :to="$playerPath('refgiton', '9020', 'valorant')" class="nav-link">
            refgiton#9020
          </nuxt-link>
        </li>
      </ul>
    </div>
  </nav>
</template>
