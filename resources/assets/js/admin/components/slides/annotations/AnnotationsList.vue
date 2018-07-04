<template>
	<div class="annotations-list">
		<ul v-if="list">
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
							<span
								class="tag"
								v-for="tag in annotation.tags"
								:style="{backgroundColor: getColourForStr(tag.name)}">
								{{tag.name}}
							</span>
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
			margin: 5px
			width: 100%
			display: flex
			flex-direction: column
			.annotation-item__header
				display: flex
				.annotation-item__essentials
					display: flex
					align-items: center
					justify-content: flex-start
					flex: 0 1 auto
					.annotation-item__essentials__id
						margin-right: $margin-medium
						display: flex
						align-items: center
						justify-content: flex-start
					.annotation-item__essentials__name
						color: $color-ocean-blue
						cursor: pointer
				.annotation-item__tags
					display: flex
					flex-direction: row
					justify-content: flex-start
					margin-left: $margin-big
					flex: 1 0 auto
					flex-wrap: wrap
					align-items: center
					.tag
						color: black
						font-size: 1rem
						height: auto
						margin: 0 10px 10px 0
						padding: 5px 10px
						max-width: 100%
			.annotation-item__description
				margin: $margin-medium

	.isEven
		background-color: $color-background-light-gray

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
			getColourForStr(str) {
				console.log(str);
				let hash = 0;
				for (let i = 0; i < str.length; i++) {
					hash = str.charCodeAt(i) + ((hash << 5) - hash);
				}

				let hex =
				((hash >> 24) & 0xff).toString(16) +
				  ((hash >> 16) & 0xff).toString(16) +
				  ((hash >> 8) & 0xff).toString(16) +
				  (hash & 0xff).toString(16);

				hex += "000000";
				hex = hex.substring(0, 6)
				const r = parseInt(hex.substring(0,2), 16);
				const g = parseInt(hex.substring(2,4), 16);
				const b = parseInt(hex.substring(4,6), 16);

				return `rgba(${r}, ${g}, ${b}, 0.2)`
			},
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
