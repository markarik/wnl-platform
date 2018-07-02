<template>
	<div>
		SIEMKI
		<ul>
			<li v-for="annotation in annotations" :key="annotation.id">
				<router-link :to="{name: 'annotations-edit', params: {annotationId: annotation.id}}">
					{{annotation.keyword}}
				</router-link>
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
		async mounted() {
			const {data: annotations} = await axios.get(getApiUrl('annotations/all'));
			this.annotations = annotations;
		}
	}
</script>
