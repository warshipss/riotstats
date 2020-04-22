<template>
  <div class="search">
    <v-select class="select" @search="search" @input="selected" :options="options"
              :filterable="false" :placeholder="$t('Search player by nickname')">

    </v-select>
  </div>
</template>

<script>
  import { debounce } from 'lodash'

  export default
  {
    data() {
      return {
        term: null,
        options: [],
      }
    },

    watch: {
      term: debounce(async val => {
        await this.search(val)
      }, 500),
    },

    methods: {
      inputChange (search, loading) {
        this.term = search
      },

      selected (val) {
        this.$router.push(val.link)
      },

      async search (term) {
        if (! term || term.length < 2) {
          return false
        }

        const { data } = await this.$axios.get('/player/search', {
          params: { term },
        })

        this.options = data.data.map(u => ({
          code: u.uid,
          label: [u.nickname, u.tag].join(' #'),
          link: this.$gamePath(null, 'player-nickname-tag', {
            tag: u.tag,
            nickname: u.nickname,
          })
        }))

        if (term.split('#').length === 2)
        {
          this.options.push({
            code: '',
            label: this.$t('Find exactly') + ' ' + term,
          })
        }
      }
    }
  }
</script>
