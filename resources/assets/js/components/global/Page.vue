<template lang="html">
	<div class="content">
		<div class="page content" v-html="content"></div>
		<wnl-qna :tags="tags" :reactionsDisabled="true" v-if="qna"></wnl-qna>
	</div>
</template>

<style lang="sass">
</style>

<script>
	import Qna from 'js/components/qna/Qna'
	import axios from 'axios'
	import {getApiUrl} from 'js/utils/env'
	import {mapActions} from 'vuex'
	import emits_events from 'js/mixins/emits-events'
	import features from 'js/consts/events_map/features.json';

	const PLACEHOLDER_RGX = /{{(.*)}}/g;

	export default {
		name: 'Page',
		components: {
			'wnl-qna': Qna,
		},
		mixins: [emits_events],
		props: {
			slug: {
				required: true,
				type: String,
			},
			arguments: {
				default: () => {},
				type: Object,
			},
			qna: {
				default: false,
				type: Boolean,
			},
		},
		data() {
			return {
				id: null,
				content: null,
				name: null,
				created_at: null,
				updated_at: null,
				tags: null,
			}
		},
		methods: {
			injectArguments(content) {
				const matches = content.match(PLACEHOLDER_RGX)
				let missing = []

				if (!matches) return content

				matches.forEach(match => {
					const argName = match.replace(/{{|}}/g, '')
					const value = this.arguments[argName] || ''
					if (!value) missing.push(argName)
					content = content.replace(match, value)
				})
				if (missing.length > 0) {
					$wnl.logger.warning('Missing page template arguments: '
						+ missing.join(','))
				}

				return content
			},
			fetch() {
				const url = getApiUrl(`pages/${this.slug}?include=tags`)

				axios.get(url).then(res => {
					Object.entries(res.data).forEach(([key, value]) => {
						this[key] = value
					})
				}).catch.bind($wnl.logger.capture)

				this.emitUserEvent({
					action: features.page.actions.open.value,
					feature: features.page.value,
					subcontext: this.slug
				})
			},
			...mapActions('qna', ['fetchQuestionsByTags']),
		},
		mounted() {
			this.fetch()
		},
		watch:{
			content(newValue) {
				this.content = this.injectArguments(newValue)
			},
			tags(newValue) {
				this.fetchQuestionsByTags({tags: newValue})
			},
			slug() {this.fetch()}
		}
	}
</script>
