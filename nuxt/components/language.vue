<template>
  <b-modal modal-class="language" id="language">
    <h5 class="modal-title" slot="modal-header">
      <i class="icon icon-globe" /> {{ $t('Language select') }}
    </h5>

    <div class="language__list">
      <div class="list-group-item" v-for="locale of $i18n.locales" :key="locale.code">
        <a href="#" :class="[' list-group-item-action', { 'active': $i18n.locale === locale.code }]"
           @click.prevent="setLocale(locale)">
          {{ locale.title }}
        </a>
      </div>
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
