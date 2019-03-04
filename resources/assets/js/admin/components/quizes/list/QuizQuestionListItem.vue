<template>
	<div class="margin bottom">
		<router-link :to="to" class="link">
			<span class="id">{{id}}. {{stripHtml(text)}}</span>
		</router-link>
	</div>
</template>

<script>
export default {
	name: 'QuizQuestionListItem',
	props: {
		id: {
			type: Number,
			required: true
		},
		text: {
			type: String,
			required: true
		}
	},
	computed: {
		to() {
			return {
				name: 'quiz-editor',
				params: {
					quizQuestionId: this.id,
				},
			};
		},
	},
	methods: {
		stripHtml(text) {
			const tmp = document.createElement('div');
			tmp.innerHTML = text;
			return tmp.innerText || tmp.innerHTML || '';
		}
	}
};
</script>
