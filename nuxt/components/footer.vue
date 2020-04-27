<template>
  <div class="footer">
    <div class="container">
      <div class="footer__main">
        <div class="row">
          <div class="col col-md-6">
            <p>
              {{ $t('This website is not affiliated with Riot Games.') }} <br />
              {{ $t('Valorant is a registered trademark of Riot Games, Inc.') }} <br />
              {{ $t('All used images and data is a property of Riot Games, Inc.') }}
            </p>
          </div>

          <div class="col col-md-6 footer__twitter">
            <a href="https://twitter.com/riotstats_com" target="_blank">
              <i class="icon icon-twitter" />
            </a>
          </div>
        </div>
      </div>

      <div class="footer__sub">
        <p class="footer__love">2020 &copy; {{ $t('Made with love by Skylex') }} </p>
      </div>
    </div>
  </div>
</template>
