<template>
	<div>
		<ul>
			<li v-for="annotation in annotations" :key="annotation.id">
				<a @click="onAnnotationClick(annotation)">
					{{annotation.keyword}}
				</a>
			</li>
		</ul>
	</div>
</template>

<style lang="sass" scoped>
</style>

<script>
	import axios from 'axios';

	import { getApiUrl } from 'js/utils/env'

	export default {
		name: 'AnnotationsList',
		data() {
			return {
				annotations: []
			}
		},
		methods: {
			onAnnotationClick(annotation) {
				this.$emit('annotationSelect', annotation);
			}
		},
		async mounted() {
			console.log('mounted called.....');
			const {data: annotations} = await axios.get(getApiUrl('annotations/all'));
			this.annotations = annotations;
		},
	}
</script>
