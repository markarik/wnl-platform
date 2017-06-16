<template>
	<div class="content">
		<h2>Pomoc w nauce</h2>
		<p class="strong">Cześć {{currentUserName}}!</p>
		<p>Chętnie odpowiemy na wszystkie Twoje pytania odnośnie nauki podczas kursu "Więcej niż LEK" (i nie tylko)! Jeżeli szukasz informacji na temat efektywnej nauki, wiele przydatnej wiedzy znajdziesz we <router-link :to="introLessonRoute">Wstępie do kursu</router-link>.</p>
		<p>Jeśli jednak macie jakiekolwiek pytania dotyczące nauki - możecie śmiało zadawać je tutaj!</p>
		<wnl-qna :tags="tags" v-if="!loading"></wnl-qna>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import _ from 'lodash'
	import axios from 'axios'
	import {mapGetters} from 'vuex'

	import Qna from 'js/components/qna/Qna'
	import {getApiUrl} from 'js/utils/env'

	export default {
		name: 'LearningHelp',
		components: {
			'wnl-qna': Qna,
		},
		data() {
			return {
				loading: true,
				tags: [],
			}
		},
		computed: {
			...mapGetters(['currentUserName']),
			...mapGetters('course', ['getLessonByName']),
			introLessonRoute() {
				let intro = this.getLessonByName('Wstęp do kursu')

				if (intro.length > 0) {
					return {
						name: 'lessons',
						params: {
							courseId: intro[0].editions,
							lessonId: intro[0].id,
						}
					}
				}
			},
		},
		mounted() {
			axios.post(getApiUrl('tags/.search'), {
				query: { where: [ ['name', '=', 'Pomoc w nauce'] ], }
			})
				.then(response => {
					this.tags = _.values(response.data)
					this.loading = false
				})
				.catch(error => $wnl.logger.error(error))
		},
	}
</script>
