<template>
    <div class="container">
        <div class="row">
            <newsolution></newsolution>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <solution v-for="single_solution in visible_solutions" :solution="single_solution" :key="single_solution.id"></solution>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.get_solutions();
        },

        props: ['user'],

        data: function () {
            return {
                solutions: null,
                active_tags: []
            }
        },

        computed: {
            visible_solutions: {
                get: function() {
                    return this.solutions;
                }
            },

        },

        created: function() {
            this.$eventHub.$on('delete_solution', this.delete_solution);
            this.$eventHub.$on('new_solution', this.new_solution);
            this.$eventHub.$on('update_tag_filters', this.update_active_tags);
            this.$eventHub.$on('search_solutions', this.search_solutions);
            this.$eventHub.$on('search_solutions_clear', this.search_solutions_clear);

        },

        methods: {
            get_solutions: function() {
                this.$http.get('/api/solutions').then((response) => {
                    this.solutions = response.data;
                }, (response) => {
                    console.log("There was an error getting the solutions.");
                });
            },

            new_solution: function(solution_id) {
                this.$http.get('/api/solutions/'+solution_id).then((response) => {
                    this.solutions.unshift(response.data);
                    this.$nextTick(() => {
                        this.$eventHub.$emit("editing_mode", solution_id);
                    });
                }, (response) => {
                    console.log("There was an error getting the solutions.");
                });
            },

            delete_solution: function(solution_id) {
                this.$http.delete('/api/solutions/'+solution_id).then((response) => {
                    this.get_solutions();
                    this.$eventHub.$emit("update_tags");
                    this.$eventHub.$emit('update_tag_filer_list');
                }, (response) => {
                    console.log("There was an error deleting the solutions.");
                });
            },

            update_active_tags: function(tag_id, state) {
                if(state == true) {
                    this.active_tags.push(tag_id);
                } else {
                    this.active_tags.splice(this.active_tags.indexOf('tag_id'), 1);
                }

                if (this.active_tags.length == 0) {
                    this.get_solutions();
                } else {
                    console.log(this.active_tags);
                    var data = this.active_tags;
                    this.$http.post('/api/solutions/filterbytags', data).then(response => {
                    console.log(response);
                        this.solutions = response.data;
                    }, response => {
                        console.log(response);
                    });
                }
            },

            search_solutions: function(string) {
                this.$http.post('/api/solutions/search', [string]).then(response => {
                    console.log(response);
                    this.solutions = response.data;
                }, response => {
                    console.log(response);
                });
            },

            search_solutions_clear: function() {
                this.get_solutions();
            }
        }
    }
</script>
