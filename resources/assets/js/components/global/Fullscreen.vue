<template>

    <div id="myModal" class="modal">

        <span class="close">&times;</span>


        <img class="modal-content" id=" dostań się do indexu zdjęcia ">

    </div>

</template>

<style lang="sass">
    .modal
        background-color: blue
        height: 100px
        width: 100px

</style>

<script>
export default {
    name: 'Fullscreen',
    props: ['imagesSources', 'currentImageSource'],
    computed: {

    },
    methods: {

    },
    mounted() {
        
    }
}
</script>
