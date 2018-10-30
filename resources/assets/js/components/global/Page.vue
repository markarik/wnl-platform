<template lang="html">
	<div class="content">
		<div class="page content" v-html="content"></div>
		<wnl-qna :tags="tags" :reactionsDisabled="true" v-if="qna"></wnl-qna>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'
</style>

<script>
	import Qna from 'js/components/qna/Qna'
	import axios from 'axios'
	import {getApiUrl} from 'js/utils/env'
	import {mapActions} from 'vuex'

	const PLACEHOLDER_RGX = /{{(.*)}}/g;

	export default {
		name: 'Page',
		components: {
			'wnl-qna': Qna,
		},
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
			wrapEmbedded() {
				let iframes = this.$el.getElementsByClassName('ql-video'),
					wrapperClass = 'ratio-16-9-wrapper'

				if (iframes.length > 0) {
					_.each(iframes, (iframe) => {
						let wrapper = document.createElement('div'),
							parent = iframe.parentNode

						wrapper.className = wrapperClass
						parent.replaceChild(wrapper, iframe)
						wrapper.appendChild(iframe)
					})
				}
			},
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
				}).then(() => {
					this.wrapEmbedded()
				}).catch.bind($wnl.logger.capture)
			},
			...mapActions('qna', ['fetchQuestionsByTags']),
		},
		mounted() {this.fetch()},
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
