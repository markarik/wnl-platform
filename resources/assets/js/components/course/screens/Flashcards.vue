<template>
	<div>
		<div v-html="content"/>
	</div>
</template>

<script>
	import {mapActions} from 'vuex';
	import {get} from 'lodash';

	export default {
		props: {
			screenData: {
				type: Object,
				required: true
			}
		},
		data() {
			return {
				content: ''
			}
		},
		methods: {
			...mapActions('flashcards', ['fetchFlashcardsSet']),
		},
		async mounted() {
			const setId = get(this.screenData, 'meta.resources[0].id');

			const flashcardsSetResponse = await this.fetchFlashcardsSet({
				setId: setId,
				include: 'flashcards'
			})

			this.content = flashcardsSetResponse.description;
		}
	}
</script>
