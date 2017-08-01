<template>
	<div class="stream-filtering">
		<div v-if="!isMobile" class="tabs">
			<ul>
				<li v-for="(option, index) in filteringOptions" :key="index" :class="{'is-active': isTabActive(option.slug)}">
					<a @click="changeFiltering(option.slug)">
						<span class="icon is-small"><i class="fa" :class="option.icon"></i></span> {{option.text}}
					</a>
				</li>
			</ul>
		</div>
		<div v-else class="control">
			<span class="select">
				<select @input="changeFilteringWithSelect">
					<option v-for="(option, index) in filteringOptions"
						:key="index"
						:value="option.slug"
						:selected="isTabActive(option.slug)"
					>
						<span class="icon is-small"><i class="fa" :class="option.icon"></i></span> {{option.text}}
					</option>
				</select>
			</span>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.stream-filtering
		margin-bottom: $margin-base

	.is-active
		font-weight: $font-weight-regular
</style>

<script>
	import {mapGetters} from 'vuex'

	const filteringOptions = [
		{
			slug: 'all',
			icon: 'fa-globe',
			text: 'Wszystkie',
		},
		{
			slug: 'slides',
			icon: 'fa-window-maximize',
			text: 'Slajdy',
		},
		{
			slug: 'quiz',
			icon: 'fa-check-circle-o',
			text: 'Pytania kontrolne',
		},
		{
			slug: 'qna',
			icon: 'fa-question-circle-o',
			text: 'Pytania i odpowiedzi',
		},
	]

	export default {
		name: 'StreamFiltering',
		data() {
			return {
				activeTab: 'all',
			}
		},
		computed: {
			...mapGetters(['isMobile']),
			filteringOptions() {
				return filteringOptions
			}
		},
		methods: {
			changeFiltering(tab) {
				this.activeTab = tab
				this.$emit('changeFiltering', tab)
			},
			changeFilteringWithSelect(event) {
				this.changeFiltering(event.target.value)
			},
			isTabActive(tab) {
				return this.activeTab === tab
			},
		},
	}
</script>
