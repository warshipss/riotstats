<template>
  <div class="profile">
    <div class="profile__head-container">
      <h1 class="profile__nickname">
        {{ profileTitle }}
      </h1>

<!--      <div class="profile__meta" v-if="profile">-->
<!--        <div class="profile__actions">-->
<!--          <a href="#" class="profile__action profile__action_update"-->
<!--             :title="$t('Update')" v-b-tooltip.hover @click.prevent="update">-->
<!--            <i class="icon icon-update" />-->
<!--          </a>-->

<!--          <a href="#" class="profile__action profile__action_track"-->
<!--             :title="$t('Track this player')" v-b-tooltip.hover @click.prevent="track" v-if="false">-->
<!--            <i class="icon icon-star-o" />-->
<!--          </a>-->
<!--        </div>-->

<!--        <span class="profile__meta-text">{{ $t('Updated') }}: {{ $ago(profile.updated_at) }}</span>-->
<!--      </div>-->
    </div>

    <h3 v-if="error && error === 'NOT_FETCHED_YET'">
      <i class="icon icon-spin animate-spin" /> {{ $t('Data is updating') }}...
    </h3>

    <h3 v-else-if="error">{{ error }}</h3>

    <template v-else>
      <div class="row">
        <div class="col col-md-4">
          <div class="profile__card">
            <h2 class="profile__card-title">{{ $t('Overall statistics') }}</h2>

            <div class="profile__stats">
              <div class="stat">
                <div class="stat__value">{{ overall.matches }}</div>
                <div class="stat__label">{{ $t('Matches') }}</div>
              </div>

              <div class="stat">
                <div class="stat__value">{{ Math.floor(overall.wins / overall.matches * 100) }}%</div>
                <div class="stat__label">{{ $t('Win rate') }}</div>
              </div>

              <div class="stat">
<!--                <div class="stat__value">{{ record.kda.kills }} / {{ record.kda.deaths }} / {{ record.kda.assists }}</div>-->
                <div class="stat__label">{{ $t('Best') }} KDA</div>
              </div>
            </div>

            <div class="profile__stats">
              <div class="stat">
                <div class="stat__value">{{ Math.floor(overall.score / overall.rounds) }}</div>
                <div class="stat__label">{{ $t('Combat score') }}</div>
              </div>

              <div class="stat">
                <div class="stat__value">{{ Math.floor(overall.damage / overall.rounds) }}</div>
                <div class="stat__label">{{ $t('Avg. damage') }}</div>
              </div>

              <div class="stat">
                <div class="stat__value">{{ (overall.kills / overall.deaths).toFixed(2) }}</div>
                <div class="stat__label">{{ $t('Kills / Deaths') }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="col col-md-4">
          <div class="profile__card">
            <h2 class="profile__card-title">{{ $t('Last 20 matches') }}</h2>

            <div class="profile__stats">
              <div class="stat">
                <div class="stat__value">{{ Math.floor(last.wins / last.matches * 100) }}%</div>
                <div class="stat__label">{{ $t('Win rate') }}</div>
              </div>

              <div class="stat">
                <div class="stat__value">{{ Math.floor(last.damage / last.rounds) }}</div>
                <div class="stat__label">{{ $t('Avg. damage') }}</div>
              </div>

              <div class="stat">
                <div class="stat__value">{{ Math.floor(last.score / last.rounds) }}</div>
                <div class="stat__label">{{ $t('Combat score') }}</div>
              </div>
            </div>

            <div class="profile__stats">
              <div class="stat">
                <div class="stat__value">{{ (last.kills / last.deaths).toFixed(2) }}</div>
                <div class="stat__label">{{ $t('Kills / Deaths') }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="col col-md-4">
          <div class="profile__card profile__card_agent" v-if="bestAgent">
            <img :src="$agentImage(bestAgent.uid, 'profile')" class="profile__agent" />
            <h2 class="profile__card-title">{{ $t('Agent') }} — {{ $getAgent(bestAgent.uid).slug }}</h2>

            <div class="profile__stats">
              <div class="stat">
                <div class="stat__value">{{ bestAgent.stats.matches }}</div>
                <div class="stat__label">{{ $t('Matches') }}</div>
              </div>

              <div class="stat">
                <div class="stat__value">{{ Math.floor(bestAgent.stats.wins / bestAgent.stats.matches * 100) }}%</div>
                <div class="stat__label">{{ $t('Win rate') }}</div>
              </div>

              <div class="stat">
                <div class="stat__value">{{ Math.floor(bestAgent.stats.score / bestAgent.stats.rounds) }}</div>
                <div class="stat__label">{{ $t('Combat score') }}</div>
              </div>
            </div>

            <div class="profile__stats">
              <div class="stat">
                <div class="stat__value">{{ Math.floor(bestAgent.stats.damage / bestAgent.stats.rounds) }}</div>
                <div class="stat__label">{{ $t('Avg. damage') }}</div>
              </div>

              <div class="stat">
                <div class="stat__value">{{ (bestAgent.stats.kills / bestAgent.stats.deaths).toFixed(2) }}</div>
                <div class="stat__label">{{ $t('Kills / Deaths') }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <h2>{{ $t('Matches') }}</h2>

      <div class="matches">
        <match :match="match" :current="profile.uid" :key="match.uid" v-for="match of profile.matches" />
      </div>

      <a href="#" class="matches__more" @click.prevent="loadMatches" v-if="total > page * perPage">
        {{ $t('Load more') }}
      </a>
    </template>
  </div>
</template>

<script>
  import { orderBy } from 'lodash'
  import Match from '~/components/match'

  export default
  {
    components: { Match },

    head() {
      const description = this.$t('Detailed Valorant statistics of #, matches history, last 20 matches and overall summary')
        .replace('#', this.profileTitle)

      return {
        title: this.title,

        meta: [
          { property: 'og:title', content: this.title },
          { property: 'og:description', content: description },
          { hid: 'description', name: 'description', content: description },
        ]
      }
    },

    mounted() {
      if (this.hasOwnProperty('error') && this.error === 'NOT_FETCHED_YET')
      {
        setTimeout(() => {
          location.reload()
        }, 5e3)
      }
    },

    async asyncData({ app, params}) {
      const { data } = await app.$axios.get('/player/profile', { params })

      if (data.hasOwnProperty('error')) {
        return data
      }

      return {
        profile: data.data,
      }
    },

    data() {
      return {
        page: 1,
        perPage: 20,
        error: false,
      }
    },

    methods: {
      async update() {
        const { data } = await this.$axios.post('/player/update', {
          id: this.profile.uid,
        })

        if (data.hasOwnProperty('error')) {
          return this.$toast.error(this.$t('Player was recently updated'))
        }

        if (data === 'OK') {
          this.$toast.info(this.$t('Player is queued for update'))
        }
      },

      async track() {

      },

      async loadMatches() {
        const { data } = await this.$axios.get('/player/profile', {
          params: {
            page: this.page + 1,
            tag: this.profile.tag,
            nickname: this.profile.nickname,
          }
        })

        this.page++
        this.profile.matches.push(...data.data.matches)
      },
    },

    computed: {
      profileTitle () {
        return this.$route.params.nickname + ' #' + this.$route.params.tag
      },

      title () {
        return this.profileTitle + ' ' + this.$t('Valorant statistics')
      },

      total () {
        return this.profile.stats.matches
      },

      record () {
        return this.profile.stats.record
      },

      overall () {
        return this.profile.stats.summary
      },

      last () {
        return this.profile.stats.last20
      },

      bestAgent () {
        const byScore = orderBy(Object.entries(this.profile.stats.byAgent), e => e[1].score / e[1].rounds, 'desc')

        if (byScore.length)
        {
          return {
            uid: byScore[0][0],
            stats: byScore[0][1],
          }
        }

        return null
      }
    }
  }
</script>
