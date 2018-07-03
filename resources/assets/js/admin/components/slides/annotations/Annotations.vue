<template>
	<div>
		<div class="tabs">
			<ul>
				<li :class="{'is-active': tab.active}" @click="changeTab(name)" v-for="(tab, name) in tabs" :key=name>
					<a>{{tab.text}}</a>
				</li>
			</ul>
		</div>
		<component :is="activeComponent" @annotationSelect="onAnnotationSelect"/>
	</div>
</template>
<script>
	import AnnotationsList from "./AnnotationsList";
	import AnnotationsAdd from "./AnnotationsAdd";

	export default {
		components: {AnnotationsList, AnnotationsAdd},
		data() {
			return {
				tabs: {
					list: {
						component: AnnotationsList,
						active: true,
						text: 'Lista'
					},
					editor: {
						component: AnnotationsAdd,
						active: false,
						text: 'Edytor'
					}
				},
				activeAnnotation: {}
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
			onAnnotationSelect(annotation) {
				this.activeTab.active = false;
				this.tabs.editor.active = true;
				this.activeAnnotation = annotation;
			}
		}
	}
</script>
