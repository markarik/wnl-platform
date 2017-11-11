<template lang="html">
	<div class="scrollable-main-container">
		<h1>Notyfikacje</h1>
		<wnl-private-feed />

	</div>
</template>

<script>
	import PrivateFeed from 'js/components/notifications/feeds/stream/PrivateFeed'
	import ActiveFilters from 'js/components/questions/ActiveFilters'
	import QuizWidget from 'js/components/quiz/QuizWidget'
	import QuestionsFilters from 'js/components/questions/QuestionsFilters'
	import QuestionsNavigation from 'js/components/questions/QuestionsNavigation'
	import QuestionsSolving from 'js/components/questions/QuestionsSolving'
	import QuestionsTest from 'js/components/questions/QuestionsTest'
	import SidenavSlot from 'js/components/global/SidenavSlot'
    import { mapGetters } from 'vuex';

	export default {
		components: {
			'wnl-private-feed': PrivateFeed,
			'wnl-active-filters': ActiveFilters,
			'wnl-questions-navigation': QuestionsNavigation,
			'wnl-quiz-widget': QuizWidget,
			'wnl-questions-filters': QuestionsFilters,
			'wnl-sidenav-slot': SidenavSlot,
			'wnl-questions-test': QuestionsTest,
			'wnl-questions-solving': QuestionsSolving,
		},
        data: function () {
		    return {
		    	filters: {
		    		observables: {
		    			message: 'observables',
                        type: 'tags',
                        items: [
                            {
                            	name: 'Slajdy',
                            	count: 0,
                                value: 1
                            },
	                        {
		                        name: 'Pytania Kontrolne',
		                        count: 0,
		                        value: 2
	                        },
	                        {
		                        name: 'Pytania i odpowiedzi',
		                        count: 0,
		                        value: 3
	                        }
                        ]
                    },
				    status: {
					    message: 'status',
					    type: 'tags',
					    items: [
						    {
							    name: 'Nowe',
							    count: 0,
							    value: 4
						    },
						    {
							    name: 'Zarchiwizowane',
							    count: 0,
							    value: 5
						    }
					    ]
				    },
                }
            }
        },
        methods: {
	        onActiveFiltersChanged: function () {}
        },
        computed: {
	        ...mapGetters([
		        'isChatMounted',
		        'isChatToggleVisible',
		        'isChatVisible',
		        'isMobile',
		        'isLargeDesktop',
		        'isSidenavMounted',
		        'isSidenavVisible',
	        ]),
        }
	}
</script>
