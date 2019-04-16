<template>
	<ul>
		<li
			v-for="(annotation, index) in serializeResponse(list)"
			:key="annotation.id"
			class="annotation-item"
			:class="{'annotation-item--is-even': isEven(index)}"
			@click="toggleAnnotation(annotation)"
		>
			<div class="annotation-item__header">
				<span class="annotation-item__header__item">
					{{annotation.id}}
				</span>
				<span class="annotation-item__header__item annotation-item__header__item--grow">
					{{annotation.title}}
				</span>
				<div class="annotation-item__header__tags annotation-item__header__item">
					<wnl-tag
						v-for="tag in annotation.tags"
						:key="tag.id"
						:tag="tag"
					/>
				</div>
				<span v-if="modifiedAnnotationId === annotation.id" class="annotation-item__header__item  annotation-item__header__item--small">
					...niezapisany
				</span>
				<span
					class="icon is-small annotation-item__header__item annotation-item__header__item--edit"
					@click="(event) => onAnnotationClick({annotation, event})"
				>
					<i class="fa fa-pencil"></i>
				</span>
				<span class="icon is-small  annotation-item__header__item annotation-item__header__item--chevron">
					<i
						class="toggle fa fa-angle-down"
						:class="{'fa-rotate-180': isOpen(annotation)}"
					>
					</i>
				</span>
			</div>
			<div
				v-if="isOpen(annotation)"
				class="annotation-item__description"
				v-html="annotation.description"
			>
			</div>
		</li>
	</ul>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.annotation-item
		min-height: 35px
		display: flex
		margin: 10px 0
		cursor: pointer
		width: 100%
		flex-direction: column
		padding: 5px
		&__header
			display: flex
			&__item
				margin-right: $margin-base
				flex: 0 1 auto
				&--grow
					flex-grow: 1
				&:last-child
					margin-right: 0
				&--edit
					padding: $margin-base
					margin-right: 0
					color: $color-ocean-blue
				&--chevron
					padding: $margin-base
				&--small
					font-size: 0.8rem
					font-style: italic
					color: $color-background-gray
			&__tags
				display: flex
				justify-content: flex-start
				margin-left: $margin-big
				flex-wrap: wrap
				align-items: center
				max-width: 60%
		&__description
			margin: $margin-medium
		&--is-even
			background-color: $color-background-light-gray
</style>

<script>
import { getColourForStr } from 'js/utils/colors.js';
import WnlTag from 'js/admin/components/global/Tag';

export default {
	data() {
		return {
			openAnnotations: [],
			getColourForStr,
		};
	},
	props: {
		modifiedAnnotationId: {
			type: Number,
			default: 0
		},
		list: {
			type: Object,
			required: true
		}
	},
	components: {
		WnlTag
	},
	methods: {
		isEven(index) {
			return index % 2 === 0;
		},
		toggleAnnotation(annotation) {
			if (this.openAnnotations.indexOf(annotation.id) === -1) {
				this.openAnnotations.push(annotation.id);
			} else {
				const index = this.openAnnotations.indexOf(annotation.id);
				if (index > -1) {
					this.openAnnotations.splice(index, 1);
				}
			}
		},
		isOpen(annotation) {
			return this.openAnnotations.indexOf(annotation.id) > -1;
		},
		onAnnotationClick({ annotation, event }) {
			this.$emit('annotationSelect', annotation);
			event.stopImmediatePropagation();
		},
		serializeResponse(response) {
			const { included, ...annotations } = response;
			const { tags, keywords } = included;

			return Object.values(annotations).map(annotation => {
				return {
					...annotation,
					tags: (annotation.tags || []).map(tagId => ({
						id: tags[tagId].id,
						name: tags[tagId].name,
					})),
					keywords: (annotation.keywords || []).map(keywordId => keywords[keywordId].text).join(','),
				};
			});
		}
	},
};
</script>
