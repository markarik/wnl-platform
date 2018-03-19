<template>
	<div class="scrollable-main-container">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Dostępność lekcji
				</div>
			</div>
		</div>
		<div class="groups">
			<ul class="items" v-if="structure">
				<li class="item" v-for="(item, index) in items" @click="toggleGroup({groupIndex, isOpen: !isOpen})">
					{{item.name}} <span class="subitems-count">({{item.subitems.length}})</span>
					<ul v-if="canShowSubitems">
						<li class="subitem" v-for="(subitem, index) in item.subitems">
							{{subitem.name}}
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.scrollable-main-container
		.groups
			.items
				.item
					margin-bottom: $margin-base

</style>

<script>
import { mapGetters } from 'vuex'
import { resource } from 'js/utils/config'

export default {
	name: 'LessonsAvailabilities',
	computed: {
		...mapGetters(['currentUserRoles']),
		...mapGetters('course', [
			'name',
			'groups',
			'structure',
		]),
		items() {
			let course = []
			for (let i = 0, groupsLen = this.groups.length; i < groupsLen; i++) {
				let groupId = this.groups[i],
					group = this.structure[resource('groups')][groupId]

				course.push(group)

				if (!group.hasOwnProperty(resource('lessons'))) {
					continue
				}

				group.subitems = []

				for (let j = 0, lessonsLen = group[resource('lessons')].length; j < lessonsLen; j++) {
					let lessonId = group[resource('lessons')][j],
						lesson = this.structure[resource('lessons')][lessonId]

					group.subitems.push(lesson)
				}
			}
			console.log(course);
			return course
		}
	},
	mathods: {

	},
	mounted() {
		console.log(this.groups, 'groups');
		console.log(this.structure, 'structure');
	}
}
</script>
