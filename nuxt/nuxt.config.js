
export default {
  mode: 'universal',
  /*
  ** Headers of the page
  */
  head: {
    titleTemplate: '%s - RiotStats.com',
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: process.env.npm_package_description || '' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
    ]
  },

  googleAnalytics: {
    id: 'UA-100060410-4'
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
    // Doc: https://bootstrap-vue.js.org
    'bootstrap-vue/nuxt',
    // Doc: https://axios.nuxtjs.org/usage
    '@nuxtjs/axios',
    // Doc: https://github.com/nuxt-community/dotenv-module
    '@nuxtjs/dotenv',

    '@nuxtjs/auth',

    'nuxt-i18n',
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
    detectBrowserLanguage: false,
    locales: [
      { code: 'en', iso: 'en-US', file: 'en.js', domain: process.env.DOMAIN_EN, match: '..' },
      { code: 'ru', iso: 'ru-RU', file: 'ru.js', domain: process.env.DOMAIN_RU, match: '^(ru|uk|be)' },
    ],

    differentDomains: true,

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
    /*
    ** You can extend webpack config here
    */
    extend (config, ctx) {
    }
  }
}
