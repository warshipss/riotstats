<template>
  <b-modal modal-class="language" id="language">
    <h5 class="modal-title" slot="modal-header">
      <i class="icon icon-globe" /> {{ $t('Language select') }}
    </h5>

    <div class="language__list">
      <a href="#" class="list-group-item list-group-item-action"
         v-for="locale of $i18n.locales" :key="locale.code" @click.prevent="setLocale(locale)">
        {{ locale.title }}
      </a>
    </div>
  </b-modal>
</template>

<script>
  import Cookie from 'js-cookie'

  export default {

    methods: {
      setLocale (locale) {
        Cookie.set('locale', locale.code, {
          // Max safe possible datetime
          expires: new Date(2.147483647e12),
          domain: location.hostname.split('.').slice(-2).join('.')
        })

        location.href = this.switchLocalePath(locale.code)
      }
    }
  }
</script>
