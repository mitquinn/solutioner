<template>
    <div class="panel panel-primary">
        <div class="panel-heading clearfix">

            <div class="form-inline">
                <div class="input-group" v-show="!non_editable">
                    <input v-on:keyup.enter="add_tag" v-model="tag_input" type="text" class="form-control input-sm" placeholder="Press Enter to add Tags.">
                    <div class="input-group-btn btn-group-sm" >
                        <button class="btn btn-success btn-sm" v-on:click="add_tag">
                            <i class="glyphicon glyphicon-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="btn-group pull-right">
                    <a v-show="!non_editable" href="#" class="btn btn-danger btn-sm" v-on:click="delete_solution" >Delete</a>
                    <a href="#" class="btn btn-primary btn-sm" v-on:click="view" >{{view_text}}</a>
                    <a href="#" :class="edit_class" v-on:click="edit">{{edit_text}}</a>
                </div>
            </div>

            <span style="margin-left: 2px;" v-for="tag in visable_tags" class="label label-success">{{tag.name}}
                <i v-show="!non_editable" v-on:click="remove_tag(tag.id)" class="glyphicon glyphicon-remove"></i>
            </span>

            <!--Some other options for tags-->
            <!--<div class="btn-group" v-for="tag in data_solution.tags" style="margin-left: 2px;">-->
                <!--<button type="button" class="btn btn-success btn-group-sm">{{tag.name}}</button>-->
                <!--<button type="button" class="btn btn-success btn-group-sm"><i class="glyphicon glyphicon-remove"></i></button>-->
            <!--</div>-->

            <!--<ul class="list-inline">-->
                <!--<li v-for="tag in data_solution.tags">{{tag.name}}  <span v-on:click="remove_tag(tag.id)">X</span></li>-->
            <!--</ul>-->

        </div>
        <table class="table table-condensed">
            <thead>
                <tr>
                    <td class="col-xs-1"><strong>Issue:</strong></td>
                    <td class="col-xs-11"><textarea class="form-control" v-model="data_solution.issue" v-bind:readonly="non_editable" style="height: 100%"></textarea></td>
                </tr>
            </thead>
            <tbody v-show="show_details">
            <tr>
                <td class="col-xs-1"><strong>Cause:</strong></td>
                <td class="col-xs-11"><textarea class="form-control" v-model="data_solution.cause" v-bind:readonly="non_editable" style="height: 100%"></textarea></td>
            </tr>
            <tr>
                <td class="col-xs-1"><strong>Solution:</strong></td>
                <td class="col-xs-11"><textarea class="form-control" v-model="data_solution.solution" v-bind:readonly="non_editable" style="height: 100%"></textarea></td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {

        props: ['solution'],

        data: function () {
            return {
                data_solution: this.solution,

                tag_input: null,

                show_details: false,
                view_text: "View",

                non_editable: true,
                edit_text: "Edit",
                edit_class: "btn btn-primary btn-sm",

                status: "Default"
            }
        },

        computed: {
            visable_tags: {
                get: function() {
                    return this.data_solution.tags;
                }
            },

        },

        created: function() {
            this.$eventHub.$on('editing_mode', this.editing_mode);
        },

        methods: {
            view: function() {
                if(this.status == "Default") {
                    this.status = "Viewing";
                    this.update_status();
                } else {
                    this.status = "Default";
                    this.update_status();
                }
            },

            edit: function() {
                if(this.status == "Default" || this.status == "Viewing") {
                    this.status = "Editing";
                    this.update_status();
                } else {
                    this.save();
                    this.status = "Default";
                    this.update_status();
                }

            },

            update_status: function() {
                if(this.status == "Default") {
                    this.show_details = false;
                    this.non_editable = true;
                    this.view_text = "View";
                    this.edit_text = "Edit";
                    this.edit_class = "btn btn-primary btn-sm";
                }

                if(this.status == "Editing") {
                    this.show_details = true;
                    this.non_editable = false;
                    this.view_text = "Cancel";
                    this.edit_text = "Save";
                    this.edit_class = "btn btn-success btn-sm";
                }

                if(this.status == "Viewing") {
                    this.non_editable = true;
                    this.show_details = true;
                    this.view_text = "Hide";
                    this.edit_text = "Edit";
                    this.edit_class = "btn btn-success btn-sm";
                }
            },

            save: function() {
                var data = this.solution;
                this.$http.patch('/api/solutions/'+this.data_solution.id, data).then(response => {
                    console.log(response);
                }, response => {
                    console.log(response);
                });
            },

            remove_tag: function(id) {
                this.$http.delete('/api/solutions/'+this.data_solution.id+'/tags/'+id).then(response => {
                    this.data_solution.tags = response.data.tags;
                    this.$eventHub.$emit("update_tags");
                }, response => {
                    console.log(response);
                });
            },

            add_tag: function() {
                var data = {name:this.tag_input};
                this.$http.post('/api/solutions/'+this.data_solution.id+'/tags', data).then(response => {
                    this.data_solution.tags = response.data.tags;
                    this.$eventHub.$emit("update_tags");
                    this.tag_input = null;
                }, response => {
                    console.log(response);
                });
            },

            delete_solution: function() {
                this.$eventHub.$emit('delete_solution', this.solution.id);
            },

            editing_mode: function(solution_id) {
                if(this.data_solution.id == solution_id) {
                    this.edit();
                }
            }
        }
    }
</script>