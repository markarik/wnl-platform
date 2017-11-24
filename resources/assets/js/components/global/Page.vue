<template lang="html">
	<div class="content">
		{{ name }}
	</div>
</template>

<style lang="sass">
</style>

<script>
	import Qna from 'js/components/qna/Qna'
	import axios from 'axios'
	import {getApiUrl} from 'js/utils/env'

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
				default: () => [], // women's toilet is on the right
				type: Array,
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
			}
		},
		mounted() {
			const url = getApiUrl(`pages/${this.slug}`)

			axios.get(url).then(res => {
				Object.entries(res.data).forEach(([key, value]) => {
					this[key] = value
				})
			}).catch.bind($wnl.logger.capture)
		}
	}
</script>
