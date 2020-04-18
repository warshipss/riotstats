<template>
  <div class="match">
    <div v-b-toggle="match.uid" :class="['match__trigger', { match__trigger_victory: relativeMatchResult === 'victory' }]">
      <img :src="agentImage(player.agent)" class="match__agent" />

      <div class="match__squad-size" v-if="squadSize > 1">
        Squad size: {{ squadSize }}
      </div>

      <div class="match__map">
        Map: {{ match.data.map }}
      </div>

      <div class="stat">
        <div class="stat__value">{{ Math.floor(player.score / player.rounds) }}</div>
        <div class="stat__label">Avg. Combat Score</div>
      </div>

      <div class="match__result">
        <div class="match__result-label">{{ $t(relativeMatchResult) }}</div>
        <div class="match__score">
          {{ score.home }} &dash; {{ score.away }}
        </div>
      </div>

      <div class="stat">
        <div class="stat__value">{{ player.kills + ' / ' + player.deaths + ' / ' + player.assists }}</div>
        <div class="stat__label">KDA</div>
      </div>
    </div>

    <b-collapse :id="match.uid">
      <b-card>
        <table class="table table-striped match__table">
          <thead>
          <tr class="match__groupings">
            <th></th>
            <th colspan="2">Basic</th>
            <th colspan="3">Multikills</th>
            <th colspan="3">Entry</th>
          </tr>
          <tr>
            <td></td>
            <td>Player</td>
            <td>Team</td>
<!--            <td>Party</td>-->
            <td>K/D/A</td>
            <td>ADR</td>

            <td>3K</td>
            <td>4K</td>
            <td>Ace</td>

            <td>EK</td>
            <td>ED</td>
            <td>ES%</td>
          </tr>
          </thead>

          <tbody>
          <tr v-for="(player, uid) of match.data.players">
            <td class="match__agent-cell">
              <img :src="agentImage(player.agent)" class="match__agent match__agent_small" />
            </td>
            <td :style="{ borderLeft: '.2rem solid ' + parties[player.party] }">
              <nuxt-link :to="$playerPath(users[uid].nickname, users[uid].tag)">
                {{ users[uid].nickname }} #{{ users[uid].tag }}
              </nuxt-link>
            </td>
            <td>{{ player.team }}</td>
<!--            <td>{{ player.partyId }}</td>-->
            <td>{{ player.kills + ' / ' + player.deaths + ' / ' + player.assists }}</td>
            <td>{{ Math.floor(player.damage / player.rounds) }}</td>

            <td></td>
            <td></td>
            <td></td>

            <td></td>
            <td></td>
            <td></td>
          </tr>
          </tbody>
        </table>
      </b-card>
    </b-collapse>
  </div>
</template>

<script>
  import { keyBy, uniq } from 'lodash'

  export default
  {
    props: {
      match: Object,
      current: String,
    },

    methods: {
      agentImage (agent) {
        return '/img/agents/' + agent + '/sm.png'
      }
    },

    computed: {
      parties()
      {
        const colors = ['#1B1464', '#FFC312', '#12CBC4', '#ED4C67', '#A3CB38', '#1289A7', '#D980FA', '#B53471']
        const parties = uniq(Object.values(this.match.data.players).map(p => p.party))

        // TODO: Replace to Object.fromEntries()
        let result = {}
        parties.map((p, i) => result[p] = colors[i])

        return result
      },

      score() {
        const scores = Object.entries(this.match.data.score).map(([k, v]) => {
          if (k === this.player.team) {
            return ['home', v]
          }

          return ['away', v]
        })

        // TODO: Replace to Object.fromEntries()
        let result = {}
        scores.map(score => result[score[0]] = score[1])

        return result
      },

      relativeMatchResult()
      {
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
        return Object.values(this.match.data.players).filter(p => p.party === this.player.party).length
      },

      player() {
        return this.match.data.players[this.current]
      }
    },
  }
</script>
