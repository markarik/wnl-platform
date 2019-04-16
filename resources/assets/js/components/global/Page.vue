<template>
	<div class="content">
		<div class="page content" v-html="content" />
		<wnl-qna
			v-if="hasQna"
			:context-tags="tags"
			:reactions-disabled="true"
			:discussion-id="discussion_id"
		/>
	</div>
</template>

<script>
import axios from 'axios';
import { each } from 'lodash';
import { mapActions } from 'vuex';

import Qna from 'js/components/qna/Qna';

import { getApiUrl } from 'js/utils/env';
import emits_events from 'js/mixins/emits-events';
import features from 'js/consts/events_map/features.json';
import injectArguments from 'js/utils/injectArguments';

export default {
	name: 'Page',
	components: {
		'wnl-qna': Qna,
	},
	mixins: [emits_events],
	props: {
		slug: {
			required: true,
			type: String,
		},
		arguments: {
			default: () => ({}),
			type: Object,
		},
	},
	data() {
		return {
			id: null,
			content: null,
			name: null,
			created_at: null,
			updated_at: null,
			tags: null,
			discussion_id: 0,
			is_discussable: false
		};
	},
	computed: {
		hasQna() {
			return this.is_discussable && this.discussion_id;
		}
	},
	methods: {
		...mapActions('qna', ['fetchQuestionsForDiscussion']),
		wrapEmbedded() {
			let iframes = this.$el.getElementsByClassName('ql-video'),
				wrapperClass = 'ratio-16-9-wrapper';

			if (iframes.length > 0) {
				each(iframes, (iframe) => {
					let wrapper = document.createElement('div'),
						parent = iframe.parentNode;

					wrapper.className = wrapperClass;
					parent.replaceChild(wrapper, iframe);
					wrapper.appendChild(iframe);
				});
			}
		},
		fetch() {
			const url = getApiUrl(`pages/${this.slug}?include=tags`);

			axios.get(url).then(res => {
				Object.entries(res.data).forEach(([key, value]) => {
					this[key] = value;
				});
			}).then(() => {
				this.wrapEmbedded();
			}).catch.bind($wnl.logger.capture);

			this.emitUserEvent({
				action: features.page.actions.open.value,
				feature: features.page.value,
				subcontext: this.slug
			});
		},
	},
	mounted() {
		this.fetch();
	},
	watch:{
		content(newValue) {
			this.content = injectArguments(newValue, this.arguments);
		},
		discussion_id() {
			this.hasQna && this.fetchQuestionsForDiscussion(this.discussion_id);
		},
		slug: function () {
			this.fetch();
		}
	}
};
</script>
