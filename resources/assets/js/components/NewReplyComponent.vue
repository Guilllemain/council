<template>
    <div>
       <div v-if="signedIn">
           <div class="form-group">
                <wysiwyg class="form-control" name="body" v-model="body" placeholder="Have something to say ?" :shouldClear="completed"></wysiwyg>
           </div>

           <button type="submit" class="btn btn-default" @click="addReply">Post</button>
       </div>

       <p class="text-center" v-else>Please <a href="/login">sign in</a> to participate in this discussion.</p>
    </div>
</template>

<script>
    import 'at.js';
    import 'jquery.caret';

    export default {
        data() {
            return {
                body: '',
                completed: false
            }
        },

        computed: {
            signedIn() {
                return window.App.signedIn;
            }
        },

        mounted() {
            $('#body').atwho({
            at: "@",
            delay: 750,
            callbacks: {
                remoteFilter: function(query, callback) {
                    $.getJSON("/api/users", {name: query}, function(names) {
                            callback(names)
                        });
                    }
                }
            });
        },

        methods: {
            addReply() {
                axios.post(location.pathname + '/replies', {body: this.body})
                .catch(error => flash(error.message, 'danger'))
                .then(({data}) => {
                    this.body = '';
                    this.completed = true;
                    flash('Your reply has been published');
                    this.$emit('created', data);
                });
            }
        }
    }
</script>