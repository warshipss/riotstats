<template>
  <div class="match">
    <div v-b-toggle="match.uid" :class="'match__trigger match__trigger_' + matchClass">
      <img :src="$agentImage(player.agent)" class="match__agent" />

      <div class="row">
        <div class="col col-md-2 stat">
          <div class="stat__value">{{ Math.floor(player.score / player.rounds) }}</div>
          <div class="stat__label">{{ $t('Combat score') }}</div>
        </div>

        <div class="col col-md-2 stat">
          <div class="stat__value">{{ Math.floor(player.damage / player.rounds) }}</div>
          <div class="stat__label">{{ $t('Avg. damage') }}</div>
        </div>

        <div class="col col-md-3 match__result">
          <div class="match__result-label">{{ $t(matchClass) }}</div>
          <div class="match__score">
            {{ score.home }} &dash; {{ score.away }}
          </div>
        </div>

        <div class="col col-md-2 stat">
          <div class="stat__value">{{ player.kills + ' / ' + player.deaths + ' / ' + player.assists }}</div>
          <div class="stat__label">KDA</div>
        </div>

        <div class="col col-md-3 match__meta">
          <div class="match__meta-item">
            <i class="icon-map" /> {{ $t('Map') }}: {{ $getMap(match.stats.map).slug }}
          </div>

          <div class="match__meta-item">
            <i class="icon-clock" /> {{ ago(match) }}
          </div>

          <div class="match__meta-item" v-if="squadSize > 1 && matchClass !== 'custom'">
            <i class="icon-users-outline" /> {{ $t('Squad size') }}: {{ squadSize }}
          </div>
        </div>
      </div>

      <div class="match__details-btn">
        <img class="match__details-img" src="/img/icons/arrow.svg" />
      </div>
    </div>

    <b-collapse :id="match.uid">
      <div class="match__details">
        <table class="match-table">
          <thead>
          <tr class="match-table__groups">
            <th colspan="2"></th>
            <th colspan="7">Basic</th>
            <th colspan="3">Multikills</th>
            <th colspan="3">Entry</th>
          </tr>
          <tr class="match-table__stats">
            <th colspan="2"></th>
            <th>
              <a href="#" class="match-table__stat-name" :title="$t('Kills')" v-b-tooltip.hover
                 @click.prevent="sortBy('kills')">K</a>
            </th>
            <th>
              <a href="#" class="match-table__stat-name" :title="$t('Deaths')" v-b-tooltip.hover
                 @click.prevent="sortBy('deaths')">D</a>
            </th>
            <th>
              <a href="#" class="match-table__stat-name" :title="$t('Assists')" v-b-tooltip.hover
                 @click.prevent="sortBy('assists')">A</a>
            </th>
            <th>
              <a href="#" class="match-table__stat-name" :title="$t('Kills / Deaths ratio')" v-b-tooltip.hover
                 @click.prevent="sortBy('kd')">K/D</a>
            </th>
            <th>
              <a href="#" class="match-table__stat-name" :title="$t('Kills / Rounds ratio')" v-b-tooltip.hover
                 @click.prevent="sortBy('kr')">K/R</a>
            </th>
            <th>
              <a href="#" class="match-table__stat-name" :title="$t('Average combat score per round')" v-b-tooltip.hover
                 @click.prevent="sortBy('score')">ACS</a>
            </th>
            <th>
              <a href="#" class="match-table__stat-name" :title="$t('Average damage per round')" v-b-tooltip.hover
                 @click.prevent="sortBy('damage')">ADR</a>
            </th>

            <th>
              <a href="#" class="match-table__stat-name" :title="$t('3 kills rounds')" v-b-tooltip.hover @click.prevent>3K</a>
            </th>

            <th>
              <a href="#" class="match-table__stat-name" :title="$t('4 kills rounds')" v-b-tooltip.hover @click.prevent>4K</a>
            </th>

            <th>
              <a href="#" class="match-table__stat-name" :title="$t('5 kills rounds')" v-b-tooltip.hover @click.prevent>Ace</a>
            </th>

            <th>
              <a class="match-table__stat-name" :title="$t('Entry kills')" v-b-tooltip.hover href="#" @click.prevent>EK</a>
            </th>

            <th>
              <a href="#" class="match-table__stat-name" :title="$t('Entry deaths')" v-b-tooltip.hover @click.prevent>ED</a>
            </th>

            <th>
              <a href="#" class="match-table__stat-name" :title="$t('Entry success')" v-b-tooltip.hover @click.prevent>ES%</a>
            </th>
          </tr>
          </thead>

          <tbody>
          <tr :class="{ 'match-table__even': isEven(player) }" :key="player.uid" v-for="(player, i) of players">
            <td class="match__agent-cell">
              <img :src="$agentImage(player.agent)" class="match__agent match__agent_small" />
            </td>
            <td :style="{ borderLeft: '.2rem solid ' + parties[player.party] }">
              <nuxt-link :to="$playerPath(users[player.uid].nickname, users[player.uid].tag)">
                {{ users[player.uid].nickname }} #{{ users[player.uid].tag }}
              </nuxt-link>
            </td>

            <td class="match-table__stat-value match-table__first">{{ player.kills }}</td>
            <td class="match-table__stat-value">{{ player.deaths }}</td>
            <td class="match-table__stat-value">{{ player.assists }}</td>
            <td class="match-table__stat-value">{{ (player.kills / player.deaths).toFixed(2) }}</td>
            <td class="match-table__stat-value">{{ (player.kills / player.rounds).toFixed(2) }}</td>
            <td class="match-table__stat-value">{{ Math.floor(player.score / player.rounds) }}</td>
            <td class="match-table__stat-value">{{ Math.floor(player.damage / player.rounds) }}</td>

            <td class="match-table__soon" rowspan="10" colspan="6" v-if="i === 0">
              {{ $t('Coming soon') }}...
            </td>

<!--            <td class=" match-table__first"></td>-->
<!--            <td></td>-->
<!--            <td></td>-->

<!--            <td class=" match-table__first"></td>-->
<!--            <td></td>-->
<!--            <td></td>-->
          </tr>
          </tbody>
        </table>
      </div>
    </b-collapse>
  </div>
</template>

<script>
  import moment from 'moment'
  import { orderBy, keyBy, groupBy } from 'lodash'

  export default
  {
    props: {
      match: Object,
      current: String,
    },

    data() {
      return {
        sortFn: 'score',
        sortKey: 'score',
        sortDirection: 'desc',
      }
    },

    methods: {
      isEven (player) {
        return player.team === 'Blue'
      },

      ago (match) {
        return moment.unix(match.started_at).fromNow()
      },

      sortBy (key) {
        const fn = {
          kd: p => p.kills / p.deaths,
          kr: p => p.kills / p.rounds,
        }

        if (this.sortKey === key) {
          this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc'
        } else {
          this.sortKey = key
          this.sortDirection = 'desc'
          this.sortFn = fn.hasOwnProperty(key) ? fn[key] : key
        }
      },

      fromEntries(arr) {
        // TODO: Replace to Object.fromEntries()
        let result = {}
        arr.map(i => result[i[0]] = i[1])

        return result
      },
    },

    computed: {
      parties()
      {
        const colors = ['#ff4655', '#0f1923', '#3ae374', '#17c0eb']
        const parties = groupBy(Object.values(this.match.stats.players), 'party')

        let i = 0

        const assignedColors = Object.entries(parties).map(([party, players]) => {
          if (players.length > 1) {
            return [party, colors[i++]]
          }

          return [party, 'transparent']
        })

        return this.fromEntries(assignedColors)
      },

      score() {
        const scores = Object.entries(this.match.stats.teams).map(([k, v]) => {
          if (k === this.player.team) {
            return ['home', v.roundsWon]
          }

          return ['away', v.roundsWon]
        })

        return this.fromEntries(scores)
      },

      matchClass()
      {
        if (this.match.stats.type === 'custom') {
          return 'custom'
        }

        if (this.score.home > this.score.away) {
          return 'victory';
        } else if (this.score.home < this.score.away) {
          return 'defeat'
        }

        return 'draw'
      },

      users() {
        return keyBy(this.match.users, 'uid')
      },

      squadSize() {
        return Object.values(this.match.stats.players).filter(p => p.party === this.player.party).length
      },

      player() {
        return this.match.stats.players[this.current]
      },

      players() {
        const players = Object.entries(this.match.stats.players).map(([uid, player]) => {
          return { uid, ...player }
        })

        return orderBy(players, this.sortFn, this.sortDirection)
      }
    },
  }
</script>
