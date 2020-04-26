export default {
  mode: 'universal',
  /*
  ** Headers of the page
  */
  env: {
    analyticsID: process.env.GA_ID,
  },

  head: {
    titleTemplate: '%s — RiotStats.com',
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: process.env.npm_package_description || '' },

      { property: 'og:type', content: 'website' },
      { property: 'og:site_name', content: 'RIOTSTATS.COM' },
      { property: 'og:image', content: `https://${process.env.APP_DOMAIN}/img/preview.jpg` },
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
      { rel: 'icon', type: 'image/png', href: '/favicon/favicon-16x16.png', sizes: '16x16', },
      { rel: 'icon', type: 'image/png', href: '/favicon/favicon-32x32.png', sizes: '32x32', },
      { rel: 'apple-touch-icon-precomposed', sizes: '57x57', href: '/favicon/apple-touch-icon-57x57.png', },
      { rel: 'apple-touch-icon-precomposed', sizes: '72x72', href: '/favicon/apple-touch-icon-72x72.png', },
      { rel: 'apple-touch-icon-precomposed', sizes: '114x114', href: '/favicon/apple-touch-icon-114x114.png', },
      { rel: 'apple-touch-icon-precomposed', sizes: '120x120', href: '/favicon/apple-touch-icon-120x120.png', },
      { rel: 'apple-touch-icon-precomposed', sizes: '144x144', href: '/favicon/apple-touch-icon-144x144.png', },
      { rel: 'apple-touch-icon-precomposed', sizes: '152x152', href: '/favicon/apple-touch-icon-152x152.png', },
    ],

    script: [
      { src: 'https://platform.twitter.com/widgets.js' },
    ]
  },

  /*
  ** Customize the progress-bar color
  */
  loading: { color: '#fff' },
  /*
  ** Global CSS
  */
  css: [
    { src: '~/assets/scss/app.scss', lang: 'scss' }
  ],
  /*
  ** Plugins to load before mounting the App
  */
  plugins: [
    { src: '~/plugins/global.js' },
    { src: '~/plugins/ga.js', mode: 'client' },
  ],
  /*
  ** Nuxt.js dev-modules
  */
  buildModules: [
  ],
  /*
  ** Nuxt.js modules
  */
  modules: [
    'nuxt-i18n',
    '@nuxtjs/auth',
    '@nuxtjs/axios',
    '@nuxtjs/dotenv',
    'bootstrap-vue/nuxt',
  ],

  router: {
    linkExactActiveClass: 'active',
    middleware: [
      'locale'
    ],
  },

  i18n: {
    seo: true,
    lazy: true,
    langDir: 'locales/',
    differentDomains: true,
    detectBrowserLanguage: false,
    locales: [
      {
        code: 'ru',
        iso: 'ru-RU',
        file: 'ru.json',
        title: 'Русский',
        match: '^(ru|uk|be)',
        domain: 'ru.' + process.env.APP_DOMAIN,
      },
      {
        code: 'de',
        iso: 'de-DE',
        match: '^(de)',
        file: 'de.json',
        title: 'Deutsch',
        domain: 'de.' + process.env.APP_DOMAIN,
      },
      {
        code: 'fr',
        iso: 'fr-FR',
        match: '^(fr)',
        file: 'fr.json',
        title: 'Français',
        domain: 'fr.' + process.env.APP_DOMAIN,
      },
      {
        code: 'pt',
        iso: 'pt-PT',
        match: '^(pt)',
        file: 'pt.json',
        title: 'Português',
        domain: 'pt.' + process.env.APP_DOMAIN,
      },
      {
        code: 'es',
        iso: 'es-ES',
        file: 'es.json',
        title: 'Español',
        match: '^(es)',
        domain: 'es.' + process.env.APP_DOMAIN,
      },
      {
        code: 'tr',
        iso: 'tr-TR',
        file: 'tr.json',
        title: 'Türkçe',
        match: '^(tr)',
        domain: 'tr.' + process.env.APP_DOMAIN,
      },
      {
        code: 'ko',
        iso: 'ko-KR',
        match: '^ko',
        file: 'ko.json',
        title: '한국어',
        domain: 'ko.' + process.env.APP_DOMAIN,
      },
      {
        code: 'zh',
        iso: 'zh-CN',
        match: '^zh',
        title: '汉语',
        file: 'zh-cn.json',
        domain: 'zh.' + process.env.APP_DOMAIN,
      },
      {
        code: 'ja',
        iso: 'ja-JP',
        match: '^ja',
        file: 'ja.json',
        title: '日本語',
        domain: 'ja.' + process.env.APP_DOMAIN,
      },
      {
        code: 'en',
        match: '..',
        iso: 'en-US',
        file: 'en.json',
        title: 'English',
        domain: process.env.APP_DOMAIN,
      },
    ],

    vuex: {
      syncLocale: true,
      moduleName: 'i18n',
      syncMessages: true,
      syncRouteParams: true,
    },

    defaultLocale: 'en',
    vueI18n: {
      fallbackLocale: 'en',
      silentTranslationWarn: true,
    },
  },

  /*
  ** Axios module configuration
  ** See https://axios.nuxtjs.org/options
  */
  axios: {
  },
  /*
  ** Build configuration
  */
  build: {
    extractCSS: {
      allChunks: true
    },

    /*
    ** You can extend webpack config here
    */
    extend (config, ctx) {
    }
  }
}
