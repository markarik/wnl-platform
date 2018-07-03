<template>
	<div class="annotations-list">
		<ul>
			<li
				v-for="(annotation, index) in annotations"
				:key="annotation.id"
				class="annotation-item"
				:class="{'isEven': isEven(index)}"
				@click="toggleAnnotation(annotation)">
				<!-- <router-link :to="{name: 'annotations-edit', params: {annotationId: annotation.id}}"> -->
					<div class="annotation-item__essentials">
						<div class="annotation-item__essentials__id">
							<span class="icon is-small">
								<i class="toggle fa fa-angle-right"
								:class="{'fa-rotate-90': isOpen(annotation)}">
							</i>
							</span>
							{{annotation.id}}
						</div>
						<div class="annotation-item__essentials__name">
							{{annotation.keyword}}
						</div>
					</div>
				<!-- </router-link> -->
			</li>
		</ul>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.annotation-item
		height: 35px
		display: flex
		.annotation-item__essentials
			display: flex
			align-items: center
			justify-content: space-between
			width: 40%
			.annotation-item__essentials__id
				width: 40%

	.isEven
		background-color: $color-background-lightest-gray
</style>

<script>
	import axios from 'axios';

	import { getApiUrl } from 'js/utils/env'

	export default {
		name: 'AnnotationsList',
		data() {
			return {
				annotations: [],
				openAnnotations: []
			}
		},
		methods: {
			isEven(index) {
				return index % 2 === 0
			},
			toggleAnnotation(annotation) {
				if (this.openAnnotations.indexOf(annotation.id) === -1) {
					this.openAnnotations.push(annotation.id)
				} else {
					const index = this.openAnnotations.indexOf(annotation.id)
					if (index > -1) {
						this.openAnnotations.splice(index, 1)
					}
				}
			},
			isOpen(annotation) {
				return this.openAnnotations.indexOf(annotation.id) > -1
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
