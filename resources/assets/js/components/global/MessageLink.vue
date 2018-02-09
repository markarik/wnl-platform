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
        ...mapActions('chatMessages', ['addNewChannel'])
    },
    mounted() {
        const room = this.getRoomForPrivateChat(this.userId)
        if (room.id) {
            console.log('weszłem');
            this.roomId = room.id
        } else {
            console.log('weszłem');
            axios.post(getApiUrl('chat_rooms/.createPrivateRoom'), {
                name: `private-${this.currentUserId}-${this.userId}`
            }).then((response) => {
                return new Promise ((resolve) => {
                    // this.addNewChannel(response)
                    return this.roomId = response.data.id
                })
            })
        }
    }
}
</script>
