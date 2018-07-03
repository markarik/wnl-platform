<template>
	<div class="annotations-list">
		<ul
			v-if="list">
			<li
				v-for="(annotation, index) in list"
				:key="annotation.id"
				class="annotation-item"
				:class="{'isEven': isEven(index)}"
				@click="toggleAnnotation(annotation)"
			>
				<div class="meta">
					<div class="annotation-item__header">
						<div class="annotation-item__essentials">
							<div class="annotation-item__essentials__id">
								<span class="icon is-small">
									<i class="toggle fa fa-angle-right"
										:class="{'fa-rotate-90': isOpen(annotation)}">
									</i>
								</span>
								{{annotation.id}}
							</div>
							<div
								class="annotation-item__essentials__name"
								@click="onAnnotationClick(annotation)">
								{{annotation.title}}
							</div>
						</div>
						<div class="annotation-item__tags">
							<ul>
								<li
									v-for="tag in annotation.tags">
									{{tag.name}}
								</li>
							</ul>
						</div>
					</div>
					<div
						class="annotation-item__description"
						v-if="isOpen(annotation)">
						{{annotation.description}}
					</div>
				</div>
			</li>
		</ul>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.annotation-item
		min-height: 35px
		display: flex
		margin: 10px 0 10px 0
		.meta
			width: 100%
			display: flex
			flex-direction: column
			.annotation-item__header
				display: flex
				.annotation-item__essentials
					display: flex
					align-items: center
					justify-content: flex-start
					width: 60%
					.annotation-item__essentials__id
						display: flex
						align-items: center
						justify-content: flex-start
						min-width: 40%
					.annotation-item__essentials__name
						color: $color-ocean-blue
						cursor: pointer
				.annotation-item__tags
					display: flex
					justify-content: flex-start
					margin-left: $margin-big
					align-items: center
			.annotation-item__description
				margin: $margin-medium

	.isEven
		background-color: $color-background-lightest-gray
</style>

<script>
	export default {
		name: 'AnnotationsList',
		data() {
			return {
				openAnnotations: []
			}
		},
		props: {
			list: {
				type: Array,
				required: true
			}
		},
		methods: {
			parseTag(tag) {
				return Object.values(this.annotations.included.tags).find(includedTag => {
					if (includedTag.id === tag) {
						return includedTag.name
					}
				})
			},
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
			},
			onAnnotationClick(annotation) {
				this.$emit('annotationSelect', annotation);
			}
		}
	}
</script>
