<template>
    <div id="app">
        <wnl-navbar></wnl-navbar>
        <component :is="currentView"></component>
    </div>
</template>

<script>
    // Import global components
    import Navbar from './global/Navbar.vue'

    // Import main views components
    import Course from './course/Course.vue'

    export default {
        name: 'App',
        components: {
            'wnl-navbar': Navbar,
            'wnl-course': Course
        },
        computed: {
            currentView () {
                return this.$store.getters.currentView
            }
        }
    }
</script>
