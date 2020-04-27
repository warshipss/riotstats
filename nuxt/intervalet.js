(async () => {
  const fs = require('fs')
  const _ = require('lodash')
  const extractor = require('vue-i18n-extract')
  const translate = require('google-translate-open-api').default
  const parseMultiple = require('google-translate-open-api').parseMultiple

  const report = extractor.createI18NReport('{,!(node_modules)/**/}*.vue', 'locales/*.json')

  // Group by locale ignoring language (default)
  const byLocale = _.groupBy(report.missingKeys.filter(k => k.language !== 'en'), 'language')

  for (const locale of Object.keys(byLocale))
  {
    const filePath = `locales/${locale}.json`
    const phrases = byLocale[locale].map(k => k.path)

    const result = await translate(phrases, {
      from: 'en',
      to: locale,
    })

    let contents = JSON.parse(fs.readFileSync(filePath).toString())

    const translations = parseMultiple(result.data[0])
    const unused = report.unusedKeys.filter(k => k.language === locale).map(k => k.path)

    for (let i = 0; i < phrases.length; i++) {
      contents[phrases[i]] = translations[i]
    }

    contents = Object.entries(contents).filter(([key, translation]) => {
      return unused.indexOf(key) === -1
    })

    fs.writeFileSync(filePath, JSON.stringify(Object.fromEntries(contents), null, 2))
  }
})()
