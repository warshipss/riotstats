<template>
  <div>
    <h1>{{ $t('Leaderboard') }}</h1>

    <div class="agent-pick">
      <a href="#" :class="['agent-pick__wrapper', { picked: agent === a.data.slug }]"
         @click.prevent="setAgent(a.data.slug)" v-for="a of agents" :title="$ucfirst(a.data.slug)" v-b-tooltip.hover.top>

        <img :src="$agentImage(a.uid)" class="agent-pick__image" v-if="a.data.slug !== 'all'" />
        <img src="/img/icons/valorant.svg" class="agent-pick__image agent-pick__image_all" v-else />
      </a>
    </div>

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
               @click.prevent>{{ $t('Matches') }}</a>
          </th>
          <th>
            <a href="#" class="match-table__stat-name sortable" :title="$t('Average combat score')" v-b-tooltip.hover
               @click.prevent="sortBy('avgCombatScore')">ACS</a>
          </th>
          <th>
            <a href="#" class="match-table__stat-name sortable" :title="$t('Kills to deaths ratio')" v-b-tooltip.hover
               @click.prevent="sortBy('kdRation')">K/D</a>
          </th>
          <th>
            <a href="#" class="match-table__stat-name" :title="$t('Average kill / deaths / assists count')" v-b-tooltip.hover
               @click.prevent>Avg. KDA</a>
          </th>
          <th>
            <a href="#" class="match-table__stat-name sortable" :title="$t('Win rate')" v-b-tooltip.hover
               @click.prevent="sortBy('winRate')">WR</a>
          </th>
          <th>
            <a href="#" class="match-table__stat-name sortable" :title="$t('Headshots percent')" v-b-tooltip.hover
               @click.prevent="sortBy('headshotPercent')">HS%</a>
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
</template>

<script>
  import { mapState } from 'vuex'

  export default {
    head() {
      return {
        title: this.$t('Leaderboards'),

        meta: [
          { hid: 'description', name: 'description', content: this.$t('Best Valorant players by agent and overall by RiotStats') },
        ]
      }
    },

    data() {
      return {
        agent: 'all',
        sort: 'avgCombatScore',
      }
    },

    async asyncData({ app }) {
      const { leaderboard } = await app.$axios.$get('https://valorant.iesdev.com/new-leaderboards?offset=0&map=all&agent=all&search=&sortBy=avgCombatScore')

      return { leaderboard }
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
