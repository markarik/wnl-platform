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
        overflow: auto
        padding-top: 100px
        position: fixed
        width: 100%
        z-index: 60

    .modal-content
        display: block
        margin: auto
        // max-width: 70%
        width: 80%

    #close
        color: #f1f1f1
        font-size: 80px
        font-weight: bold
        position: absolute
        right: 35px
        transition: 0.3
        top: 35px
</style>

<script>
export default {
    name: 'Fullscreen',
    props: ['imagesSources', 'currentImageSource'],
    data() {
        return {
            MutableCurrentImageSource: this.currentImageSource
        }
    },
    methods: {
        // closeModal() {
        //     document.getElementById('myModal').style.display = "none";
        // },
        closeLightbox() {
            document.getElementById('myModal').style.display = "none";
        },
        changeImageSource(value) {
            this.MutableCurrentImageSource = value;
            document.getElementById('myModal').style.display = "block";
            document.querySelector('.modal-content').setAttribute('src', this.MutableCurrentImageSource)
        },
    },
    mounted() {
        this.$parent.$on('updateImageSource', this.changeImageSource)
    },
}
</script>
