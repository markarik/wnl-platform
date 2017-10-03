<template>
<div class="main-userscontainer">
    <div class="input" v-model="filterValue">
        <input type="text">filtruj</input>
    </div>
    <div class="metadata">
        {{ $t('dashboard.activeUsers', {count: allUsersCount}) }}
    </div>
    <div class="all-users-container" v-if="allUsersCount">
        <ul class="avatars-list" ref="avatarsList">
            <li v-for="user in usersToCount" class="avatar">
                <wnl-avatar :fullName="user.full_name" :url="user.avatar" :userId="user.id" :user="user" size="medium">
                </wnl-avatar>
            </li>
        </ul>
    </div>
</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
   @import 'resources/assets/sass/variables'
   @import 'resources/assets/sass/mixins'

   $square-size: 'medium'
   $container-height: map-get($rounded-square-sizes, $square-size)

   .metadata
       border-bottom: $border-light-gray
       margin-bottom: $margin-small
       flex-direction: column

   .wnl-screen-title
       margin-bottom: $margin-small

   .active-users-container
       height: $container-height
       padding-bottom: $margin-big
       position: relative

   .avatars-list
       display: flex
       position: relative
       flex-wrap: wrap

   .avatars-list .avatar
       margin-right: $margin-small
</style>

<script>
import {
    mapGetters,
    mapActions
} from 'vuex'

export default {
    name: 'AllUsers',
    data() {
        return {
            filterValue: ''
        }
    },
    computed: {
        ...mapGetters(['currentUserId', 'currentUserName']),
        ...mapGetters('users', ['allUsers']),
        usersToCount() {
            return this.allUsers.filter((user) => this.currentUserId !== user.id)
        },
        allUsersCount() {
            return this.usersToCount.length || 0
        },

    },
    methods: {
        ...mapActions('users', ['setAllUsers'])
    },
    mounted() {
        this.setAllUsers()
    },
}
</script>
