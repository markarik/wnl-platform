<template>
	<div>
		<button @click="addAnnotation">Dodaj Przypis</button>
		<div class="tabs">
			<ul>
				<li :class="{'is-active': tab.active}" @click="changeTab(name)" v-for="(tab, name) in tabs" :key=name>
					<a>{{tab.text}}</a>
				</li>
			</ul>
		</div>
		<component
			:is="activeComponent"
			:list="annotations"
			:annotation="activeAnnotation"
			@annotationSelect="onAnnotationSelect"
			@submitSuccess="onSubmitSuccess"
		/>
	</div>
</template>
<script>
	import axios from 'axios';

	import { getApiUrl } from 'js/utils/env'
	import AnnotationsList from "./AnnotationsList";
	import AnnotationsEditor from "./AnnotationsEditor";

	export default {
		components: {AnnotationsList, AnnotationsEditor},
		data() {
			return {
				tabs: {
					list: {
						component: AnnotationsList,
						active: true,
						text: 'Lista'
					},
					editor: {
						component: AnnotationsEditor,
						active: false,
						text: 'Edytor'
					}
				},
				activeAnnotation: {},
				annotations: []
			}
		},
		computed: {
			activeTab() {
				return Object.values(this.tabs).find(tab => tab.active)
			},
			activeComponent() {
				return this.activeTab.component
			},
		},
		methods: {
			changeTab(name) {
				this.activeTab.active = false;
				this.tabs[name].active = true;
			},
			addAnnotation() {
				this.changeTab('editor');
				this.activeAnnotation = {};
			},
			onAnnotationSelect(annotation) {
				this.activeTab.active = false;
				this.tabs.editor.active = true;
				this.activeAnnotation = annotation;
			},
			onSubmitSuccess(data) {
				this.activeAnnotation = {
					...this.activeAnnotation,
					...data.data
				}
			}
		},
		async mounted() {
			const {data} = await axios.get(getApiUrl('annotations/all'), {
				params: {
					include: 'tags'
				}
			});
			const {included, ...annotations} = data;

			this.annotations = Object.values(annotations).map(annotation => {
				return {
					...annotation,
					tags: (annotation.tags || []).map(tagId => ({
						id: included.tags[tagId].id,
						name: included.tags[tagId].name,
					}))
				}
			})
		},
	}
</script>
