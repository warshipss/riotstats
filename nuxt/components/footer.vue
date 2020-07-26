<template>
  <div class="footer">
    <div class="container">
      <div class="footer__main">
        <div class="row">
          <div class="col col-xs-12 col-md-7">
            <p>
              {{ $t('Overpeek.com isn\'t endorsed by Riot Games and doesn\'t reflect the views or opinions of Riot Games or anyone officially involved in producing or managing Riot Games properties.') }} <br />
              {{ $t('Riot Games, and all associated properties are trademarks or registered trademarks of Riot Games, Inc.') }}
            </p>
          </div>

          <div class="col col-md-4 d-none d-sm-block"></div>
        </div>
      </div>

      <div class="footer__sub">
        <p class="footer__love">2020 &copy; {{ $t('Made with love by Skylex') }}</p>
      </div>
    </div>
  </div>
</template>
