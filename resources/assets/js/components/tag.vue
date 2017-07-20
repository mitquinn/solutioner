<template>
    <li v-on:click="toggle" style="margin-left: 2px;" :class="this.class">{{tag_data.name}}</li>
</template>

<script>
    export default {

        props: ['tag'],

        data: function() {
            return {
                tag_data: this.tag,
                active: false,
                class: "label label-success"
            }
        },
        methods:{
            toggle: function() {
                this.active = ! this.active
                if(this.active == true) {
                    this.class = "label label-primary";
                } else {
                    this.class = "label label-success";
                }
                this.$eventHub.$emit("update_tag_filters", this.tag_data.id, this.active);
            }
        }





    }
</script>