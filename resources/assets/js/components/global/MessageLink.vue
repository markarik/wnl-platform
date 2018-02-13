<template>
    <div class="message-link">
        <router-link :to="{ name: 'messages', query: {roomId} }" v-if="roomId">
            <slot></slot>
        </router-link>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex'
import {getApiUrl} from 'js/utils/env'

export default {
    name: 'MessageLink',
    props: {
        userId: {
            required: true,
            type: Number,
        }
    },
    data() {
        return {
            roomId: 0,
        }
    },
    computed: {
        ...mapGetters('chatMessages', ['getRoomForPrivateChat']),
        ...mapGetters(['currentUserId']),
    },
    methods: {
        ...mapActions('chatMessages', ['createNewRoom'])
    },
    mounted() {
        const room = this.getRoomForPrivateChat(this.userId)
        if (room.id) {
            this.roomId = room.id
        } else {
            const payload = {
                currentUserId: this.currentUserId,
                userId: this.userId
            }
            this.createNewRoom(payload).then((response) => {
                this.roomId = response.data.id
            })
        }
    }
}
</script>
