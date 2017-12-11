<template>
	<div class="inline">
		<a href="#" v-on:mouseover="hover" v-on:mouseout="hoverOut">{{ userNameToDisplay }}</a>
		<div class="user-popover" v-if="showPopup" v-on:mouseover="hoverInfo" v-on:mouseout="hoverOutInfo">
			<wnl-avatar class="avatar"
					:fullName="author.full_name"
					:url="author.avatar"
					:userId="userId"
					size="medium">
			</wnl-avatar>
			<h3>{{ userNameToDisplay }}</h3>
			<router-link class="link" :to="{ name: 'user', params: { userId: userId }}">
				<span class="qna-meta-info">
					Przejdź do profilu
				</span>
			</router-link>
		</div>
	</div>
</template>

<style lang="sass">

	.inline
		display: inline-block
		position: relative
		.user-popover
			position: absolute
			width: 200px
			background: #fff
			border: 1px solid #42b983
			padding: 10px 20px
			box-shadow: 0 6px 6px rgba(16, 16, 16, 0.04), 0 6px 6px rgba(0, 0, 0, 0.05)
			z-index: 9999

</style>

<script>
export default {
	name: 'UserProfilePopover',
	props: ['author', 'userId'],
	data() {
		return {
			showPopup: false,
			timer: '',
			isInInfo: false,
		}
	},
    computed: {
        userNameToDisplay() {
            return this.author.full_name
        },
    },
	methods: {
		hover() {
			setTimeout(() => {
					this.showPopover()
			}, 600)
		},
		hoverOut() {
			setTimeout(() => {
				if(!this.isInInfo) {
					this.closePopover()
				}
			}, 200)
		},
		hoverInfo() {
			this.isInInfo = true
		},
		hoverOutInfo() {
			this.isInInfo = false
			this.hoverOut()
		},
		showPopover() {
			this.showPopup = true
		},
		closePopover() {
			this.showPopup = false
		}
	},
	mounted() {
		console.log(this.userId);
		console.log(this.author);
	}
}
</script>
