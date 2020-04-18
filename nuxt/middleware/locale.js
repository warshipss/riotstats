import Cookie from 'cookie'
import { parse } from 'accept-language-parser'

const matchBrowserLocale = (locales, supported) => {
  for (const locale of locales)
  {
    for (const l of supported)
    {
      if (new RegExp(l.match).test(locale)) {
        return locale.substr(0, 2)
      }
    }
  }
}

const getCookie = req => {
  return Cookie.parse(req ? (req.headers.cookie || '') : document.cookie)
}

const getBrowserLocale = req => {
  if (req) {
    return parse(req.headers['accept-language']).map(locale => locale.code)
  }

  return navigator.languages || navigator.language
}

export default async function ({ app, redirect, req })
{
  let chosenLocale = null

  const cookie = getCookie(req)
  const currentLocale = app.i18n.locale
  const browserLocale = getBrowserLocale(req)
  const userLocale = app.$auth.loggedIn ? app.$auth.user.region : null

  if (browserLocale.length) {
    chosenLocale = matchBrowserLocale(browserLocale, app.i18n.locales)
  }

  if (cookie.hasOwnProperty('locale')) {
    chosenLocale = cookie.locale
  }

  if (userLocale) {
    chosenLocale = userLocale.substr(0, 2)
  }

  if (chosenLocale !== null && currentLocale !== chosenLocale) {
    redirect(app.switchLocalePath(chosenLocale))
  }
}
