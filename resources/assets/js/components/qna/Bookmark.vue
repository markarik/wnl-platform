<template>
  <div class="bookmark">
    <span class="icon is-small">
      <i class="fa" :class="hasReactedClass" @click="toggleReaction"></i>
    </span>
    <span>Zapisz</span>
  </div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
  @import 'resources/assets/sass/variables'

  .bookmark
    align-items: center
    color: $color-sky-blue
    cursor: pointer
    display: flex
    font-size: .675rem
    flex-direction: column
    text-transform: uppercase
    width: 50px

  .icon


</style>

<script>
import { mapGetters, mapActions } from 'vuex'

export default {
  name: 'Bookmark',
  props: ['module', 'reactableResource', 'reactableId'],
  data() {
    return {
      isLoading: false
    }
  },
  computed: {
    ...mapGetters('qna', ['getReaction']),
    reaction() {
      return this.getReaction(this.reactableResource, this.reactableId, 'bookmark')
    },
    count() {
      return this.reaction.count
    },
    hasReactedClass() {
      return this.reaction.hasReacted ? 'fa-bookmark' : 'fa-bookmark-o'
    },
  },
  methods: {
    ...mapActions('qna', ['setReaction']),
    toggleReaction() {
      if (this.isLoading) {
        return false
      }
      this.isLoading = true
      this.setReaction({
        reactableResource: this.reactableResource,
        reactableId: this.reactableId,
        reaction: 'bookmark',
        hasReacted: this.reaction.hasReacted,
      }).then((response) => {
        this.isLoading = false
      })
      .catch((error) => {
        $wnl.logger.error(error)
        this.isLoading = false
      })
    },
  },
}

</script>
