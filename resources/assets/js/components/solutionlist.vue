<template>
    <div class="container">
        <div class="row">
            <newsolution v-on:new_solution="update_solution"></newsolution>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <solution v-on:delete_solution="delete_solution" v-for="(single_solution, index) of solutions" :solution="single_solution" :key="single_solution.id"></solution>
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
                solutions: null
            }
        },

        methods: {
            get_solutions: function() {
                this.$http.get('/api/solutions').then((response) => {
                    this.solutions = response.data;
                }, (response) => {
                    console.log("There was an error getting the solutions.");
                });
            },

            update_solution: function(solution_id) {
                this.$http.get('/api/solutions/'+solution_id).then((response) => {
                    this.solutions.unshift(response.data);
                }, (response) => {
                    console.log("There was an error getting the solutions.");
                });
            },

            delete_solution: function(solution) {
                this.$http.delete('/api/solutions/'+solution.id).then((response) => {
                    this.solutions.splice(this.solutions, 1);
                }, (response) => {
                    console.log("There was an error deleting the solutions.");
                });
            }
        }
    }
</script>
