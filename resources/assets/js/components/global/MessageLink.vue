<template>
    <div class="message-link">
        <router-link :to="{ name: 'messages', query: {roomId: roomId} }">
            <slot></slot>
        </router-link>
    </div>
</template>


<style lang="sass" scoped>
</style>


<script>
import {mapActions, mapGetters} from 'vuex'
import {getApiUrl} from 'js/utils/env'

export default {
    name: 'MessageLink',
    props: {
        profileId: {
            // required: true,
            type: Number,
        }
    },
    data() {
        return {
            roomId: '',
        }
    },
    computed: {
        ...mapGetters('chatMessages', ['getProfileByUserId', 'rooms', 'sortedRooms']),
        ...mapGetters(['currentUserId']),
    },
    mounted() {
        const roomsIds = Object.keys(this.rooms)
        if (this.getProfileByUserId(this.profileId)) {
            roomsIds.find((id) => {
                if (this.rooms[id].profiles.includes(this.profileId)) {
                    return this.roomId = this.rooms[id].id
                }
            })
        } else if (!this.getProfileByUserId(this.profileId))  {
            axios.post(getApiUrl('chat_rooms/.createPrivateRoom'), {
                name: `private-${this.currentUserId}-${this.profileId}`
            }).then((response) => {
                return this.roomId = response.data.id
            })
        }
    }
}
</script>
