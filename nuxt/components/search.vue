<template>
  <div class="search">
    <v-select class="select" @search="inputChange" @input="selected" :options="options" :filterable="false"
              :placeholder="$t('Search player by nickname')" :clearSearchOnBlur="clearSearchOnBlur" v-model="model">
      <template slot="no-options">
        <template v-if="! term">{{ $t('Start typing to search') }}...</template>
        <template v-else-if="term.length < 3">{{ $t('Enter at least 3 symbols to search') }}</template>
        <template v-else>{{ $t('Nothing found, try exact search') }}</template>
      </template>
    </v-select>
  </div>
</template>

<script>
  export default
  {
    data() {
      return {
        term: null,
        model: null,
        timout: null,
        options: [],
      }
    },

    methods: {
      clearSearchOnBlur() {
        return false
      },

      inputChange (search, loading) {
        loading(true)
        clearTimeout(this.timeout)

        this.term = search
        this.timeout = setTimeout(() => {
          this.search(loading)
        }, 500)
      },

      selected (val) {
        this.options = []
        this.$router.push(val.link)
      },

      async search (loading) {
        const term = this.term

        if (! term || term.length < 2) {
          return loading(false)
        }

        const { data } = await this.$axios.get('/player/search', {
          params: { term },
        })

        loading(false)

        this.options = data.data.map(u => ({
          code: u.uid,
          label: [u.nickname, u.tag].join(' #'),
          link: this.$playerPath('valorant', u.nickname, u.tag),
        }))

        // Filter null values
        const input = term.split('#').filter(i => i)

        if (input.length === 2)
        {
          this.options.push({
            label: this.$t('Find exactly') + ' ' + term,
            link: this.$playerPath('valorant', ...input),
          })
        }
      }
    }
  }
</script>
