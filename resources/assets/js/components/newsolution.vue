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
    <div>
        <div class="col-lg-12 ">
            <div class="input-group input-group-lg">
                <div class="input-group-btn">
                    <button class="btn btn-success" v-on:click="new_solution">
                        <i class="glyphicon glyphicon-plus"></i>
                    </button>
                </div>
                <input type="text" class="form-control" placeholder="Search" v-model="search" v-on:keyup="search_solutions">
                <div class="input-group-btn">
                    <button class="btn btn-primary" >
                        <i class="glyphicon glyphicon-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-12 ">
            <div >
                <ul class="list-inline" style="margin-left: 2px;">
                    <li>Filter by tag: </li>
                    <tag v-for="tag in visible_tags" :tag="tag" :key="tag.id"></tag>

                    <!--<li v-for="tag in tags" style="margin-left: 2px;" class="label label-success" v-on:click="filter_by_tag(tag)" :key="tag.id" >{{tag.name}}</li>-->
                </ul>
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
                tags: null,
                search: null
            }
        },

        computed: {
            visible_tags: {
                get: function() {
                    return this.tags;
                }
            },
        },

        created: function() {
            this.$eventHub.$on('update_tags', this.update_tags);

        },

        methods: {
            new_solution: function() {
                var data = {issue:"New Issue"};
                this.$http.post('/api/solutions', data).then(response => {
                    console.log(response.data);
                    this.$eventHub.$emit('new_solution', response.data);
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
            },

            update_tags: function() {
                this.get_tags();
            },

            search_solutions: function() {
                if(this.search.length > 4) {
                    this.$eventHub.$emit('search_solutions', this.search);
                } else {
                    this.$eventHub.$emit('search_solutions_clear');
                }
            }

        }
    }
</script>