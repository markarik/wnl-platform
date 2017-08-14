<template>

    <div id="myModal" class="modal">

        <span id="close" v-on:click="closeLightbox">&times;</span>

        <img class="modal-content" src="this.currentImageSource">

    </div>

</template>

<style lang="sass">
    .modal
        background-color: rgba(0,0,0,0.9)
        display: none
        height: 100%
        left: 0
        overflow: auto
        padding-top: 100px
        position: fixed
        top: 0
        width: 100%
        z-index: 60

    .modal-content
        display: block
        margin: auto
        max-width: 700px
        width: 80%

    #close
        color: #f1f1f1
        font-size: 40px
        font-weight: bold
        position: absolute
        right: 35px
        transition: 0.3
        top: 15px
</style>

<script>
export default {
    name: 'Fullscreen',
    props: ['imagesSources', 'currentImageSource'],
    data() {
        return {
            mutableCurrentImageSource: this.currentImageSource
        }
    },
    methods: {
        closeLightbox() {
            document.getElementById('myModal').style.display = "none";
        },
        changeImageSource(value) {
            this.currentImageSource = value;
            document.getElementById('myModal').style.display = "block";
            document.querySelector('.modal-content').setAttribute('src', this.currentImageSource)
        },
    },
    mounted() {
        this.$parent.$on('updateImageSource', this.changeImageSource)
    },
}
</script>
