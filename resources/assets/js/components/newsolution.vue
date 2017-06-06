<style>
    .nopadding {
        padding: 0 !important;
        margin: 0 !important;
    }

    .col-centered{
        float: none;
        margin: 0 auto;
    }
</style>

<template>
    <div class="col-lg-12 ">
        <div class="panel">
            <div class="col-xs-4 nopadding">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search">
                    <div class="input-group-btn">
                        <button class="btn btn-primary" >
                            <i class="glyphicon glyphicon-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-sm-4 nopadding ">
                <div class="text-center">
                    <div class="btn-group">
                        <button class="btn btn-primary" v-on:click="create_new_solution">
                            New Solution
                        </button>
                    </div>
                </div>

            </div>
            <div class="col-sm-4 nopadding">
                Select tag to filter:
                <div>
                   <span style="margin-left: 2px;" v-for="tag in tags" class="label label-primary">{{tag.name}}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {

        mounted() {
            this.get_tags();
        },

        data: function () {
            return {
                tags: null
            }
        },


        methods: {
            create_new_solution: function() {
                var data = {issue:"New Issue"};
                this.$http.post('/api/solutions', data).then(response => {
                    this.$emit('new_solution', response.data);
                }, response => {
                    console.log(response);
                });
            },

            get_tags: function() {
                this.$http.get('/api/tags').then(response => {
                    this.tags = response.data;
                }, response => {
                    console.log("Failed getting tags.");
                });
            }
        }
    }
</script>