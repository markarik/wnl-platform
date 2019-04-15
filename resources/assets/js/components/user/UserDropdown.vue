<template>
	<div class="dropdown-container">
		<wnl-dropdown @toggled="toggle">
			<div
				slot="activator"
				class="user-toggle"
				:class="{ 'is-active' : isActive, 'is-desktop': !isTouchScreen }"
			>
				<wnl-avatar />
				<span v-show="!isTouchScreen" class="icon">
					<i class="fa fa-angle-down"></i>
				</span>
			</div>
			<div slot="content">
				<div class="user-dropdown-content">
					<wnl-avatar />
					<div class="user-links">
						<p class="metadata">
							{{currentUserFullName}}
							<span v-if="currentUserEmail" class="user-email">{{currentUserEmail}}</span>
						</p>
						<ul>
							<li
								v-for="(item, index) in items"
								:key="index"
								class="user-link"
							>
								<span class="icon is-small">
									<i class="fa" :class="item.icon"></i>
								</span>
								<router-link class="link" :to="{ name: item.route }">
									{{item.text}}
								</router-link>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</wnl-dropdown>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.dropdown-container
		height: 100%
		min-height: 100%
		width: 100%

	.user-toggle
		align-items: center
		color: $color-gray
		cursor: pointer
		display: flex
		height: 100%
		justify-content: center
		min-height: 100%
		transition: background $transition-length-base
		width: 100%

		&.is-desktop:hover
			background-color: $color-background-light-gray
			transition: background $transition-length-base

		&.is-active
			background-color: $color-background-light-gray
			color: $color-darkest-gray

			.username
				color: $color-darkest-gray
				font-weight: $font-weight-regular

		.icon
			margin: 0 $margin-tiny

	.user-dropdown-content
		display: flex
		padding: $margin-medium
		min-width: 200px

		.user-links
			margin-left: $margin-medium

			.metadata
				font-size: $font-size-base
				line-height: $line-height-minus
				margin-bottom: $margin-small
				white-space: nowrap

				.user-email
					color: $color-gray
					display: block
					font-size: $font-size-minus-2
					text-transform: initial

			.user-link
				font-size: $font-size-minus-1
				margin-bottom: $margin-small
				text-transform: uppercase

				.icon
					color: $color-inactive-gray
					margin-right: $margin-tiny
</style>

<script>
import { mapGetters } from 'vuex';

import Dropdown from 'js/components/global/Dropdown';

export default {
	name: 'UserDropdown',
	components: {
		'wnl-dropdown': Dropdown,
	},
	data() {
		return {
			isActive: false,
		};
	},
	computed: {
		...mapGetters([
			'currentUserName',
			'currentUserFullName',
			'currentUserEmail',
			'isTouchScreen',
		]),
		items() {
			return [
				{
					'icon': 'fa-user-o',
					'route': 'myself',
					'text': this.$t('routes.myself.main'),
				},
				{
					'icon': 'fa-sign-out',
					'route': 'logout',
					'text': this.$t('routes.auth.logout'),
				},
			];
		}
	},
	methods: {
		toggle(isActive) {
			this.isActive = isActive;
		}
	},
};
</script>
