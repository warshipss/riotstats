<template>
  <div class="container">
    <div class="row">
      <div class="col col-md-4">
        <div class="stick">
          <ins class="adsbygoogle"
               style="display:block"
               data-ad-client="ca-pub-1470478687013356"
               data-ad-slot="2237916494"
               data-ad-format="auto"
               data-full-width-responsive="true"></ins>
        </div>
      </div>

      <div class="col col-md-8">
        <h4>
          <i class="icon icon-star" /> {{ $t('Top 50 players by Combat Score') }}
        </h4>

        <div class="table-responsive">
          <table class="match-table text-center">
            <thead>
            <tr class="match-table__stats">
              <th>
                <a href="#" class="match-table__stat-name" :title="$t('Rank')" v-b-tooltip.hover
                   @click.prevent>{{ $t('Rank') }}</a>
              </th>
              <th>
                <a href="#" class="match-table__stat-name" :title="$t('Player')" v-b-tooltip.hover
                   @click.prevent>{{ $t('Player') }}</a>
              </th>
              <th>
                <a href="#" class="match-table__stat-name" :title="$t('Matches played')" v-b-tooltip.hover
                   @click.prevent>Matches</a>
              </th>
              <th>
                <a href="#" class="match-table__stat-name" :title="$t('Average combat score')" v-b-tooltip.hover
                   @click.prevent>ACS</a>
              </th>
              <th>
                <a href="#" class="match-table__stat-name" :title="$t('Kills to deaths ratio')" v-b-tooltip.hover
                   @click.prevent>K/D</a>
              </th>
              <th>
                <a href="#" class="match-table__stat-name" :title="$t('Average kill / deaths / assists count')" v-b-tooltip.hover
                   @click.prevent>Avg. KDA</a>
              </th>
              <th>
                <a href="#" class="match-table__stat-name" :title="$t('Win rate')" v-b-tooltip.hover
                   @click.prevent>WR</a>
              </th>
              <th>
                <a href="#" class="match-table__stat-name" :title="$t('Headshots percent')" v-b-tooltip.hover
                   @click.prevent>HS%</a>
              </th>
            </tr>
            </thead>

            <tbody>
            <tr v-for="(player, rank) of leaderboard" :key="player.playerId">
              <td class="match-table__stat-value">{{ rank + 1 }}</td>
              <td class="match-table__stat-value">
                <nuxt-link :to="$playerPath('valorant', ...player.nametag.split('#'))" class="match-table__nickname">
                  {{ player.nametag }}
                </nuxt-link>
              </td>
              <td class="match-table__stat-value">{{ player.matches }}</td>
              <td class="match-table__stat-value">{{ player.avgCombatScore }}</td>
              <td class="match-table__stat-value">{{ player.kdRatio }}</td>
              <td class="match-table__stat-value">{{ player.avgKDA }}</td>
              <td class="match-table__stat-value">{{ Math.floor(player.winRate) }}%</td>
              <td class="match-table__stat-value">{{ Math.floor(player.headshotPercent) }}%</td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  export default {
    head() {
      return {
        title: this.$t('First Valorant stats tracker'),
      }
    },

    async asyncData({ app }) {
      const { leaderboard } = await app.$axios.$get('https://valorant.iesdev.com/new-leaderboards?offset=0&map=all&agent=all&search=&sortBy=avgCombatScore')

      return { leaderboard }
    },

    data() {
      return {
        agent: 'all',
        sort: 'avgCombatScore',
      }
    },

    methods: {
      setAgent (slug) {
        this.agent = slug

        this.fetch()
      },

      sortBy (slug) {
        this.sort = slug

        this.fetch()
      },

      async fetch (offset = 0) {
        const { leaderboard } = await this.$axios.$get('https://valorant.iesdev.com/new-leaderboards', {
          params: {
            offset,
            map: 'all',
            search: '',
            agent: this.agent,
            sortBy: this.sort,
          }
        })

        this.leaderboard = leaderboard
      }
    },

    computed: {
      ...mapState({
        agents: s => {
          let agents = s.resources.filter(r => r.type === 'agent')

          agents.unshift({
            data: {
              slug: 'all',
            }
          })

          return agents
        }
      })
    }
  }
</script>
