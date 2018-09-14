<template> 
    <div :id="'reply-'+id" class="card" :class="isBest ? 'card border-success' : ''">
        <div class="card-header">
            <div class="level">
                <div class="flex">
                    <a href="'/profiles/'+reply.owner.name" v-text="reply.owner.name">
                        
                    </a>
                    said <span v-text="ago"></span>...
                </div>
            
                <favorite :reply="reply" v-if="signedIn"></favorite>
            </div>

        </div>
        <div class="card-body">
            <div v-if="editing">
                <form @submit.prevent="update">
                    <div class="form-group">
                        <wysiwyg name="body" v-model="body"></wysiwyg>
                    </div>

                    <button class="btn btn-primary btn-sm">Update</button>
                    <button class="btn btn-link btn-sm" @click="cancel" type="button">Cancel</button>
                </form>
            </div>
            <div v-else v-html="body"></div>
        </div>
        
        <div class="card-footer level" v-if="authorize('updateReply', reply) || authorize('updateThread', thread)">
            <div v-if="authorize('updateReply', reply)">
                <button class="btn btn-outline-secondary btn-sm mr-2" @click="editing = true">Edit</button>
                <button class="btn btn-outline-danger btn-sm mr-2" @click="destroy">Delete</button>
            </div>
            <button class="btn btn-outline-default btn-sm ml-a" @click="markBestReply" v-if="authorize('updateThread', thread)">Best reply ?</button>
        </div>
    </div>
</template>

<script>
    import Favorite from './FavoriteComponent.vue';
    import moment from 'moment';

    export default {

        props: ['reply'],

        components: { Favorite },

        data() {
            return {
                editing: false,
                id: this.reply.id,
                body: this.reply.body,
                isBest: this.reply.isBest,
                thread: window.thread,
            };
        },

        created() {
            window.events.$on('best-reply-selected', id => {
               this.isBest = (id === this.id);
            });
        },

        computed: {
            ago() {
                return moment(this.reply.created_at).fromNow();
            },
            // signedIn() {
            //     return window.App.signedIn;
            // },
            // canUpdate() {
            //     return this.authorize(user => user.id == this.reply.user_id);
            // }
        },

        methods: {
            update() {
                axios.patch('/replies/' + this.id , {
                    body: this.body
                }).catch(error => flash(error.message, 'danger'));

                this.editing = false;

                flash('Reply updated');
            },

            destroy() {
                axios.delete('/replies/' + this.id);

                this.$emit('deleted');

                flash('Reply deleted');
            },

            markBestReply() {
                console.log(this.reply);
                axios.post('/replies/' + this.id + '/best');

                window.events.$emit('best-reply-selected', this.id);
            },

            cancel() {
                this.body = this.reply.body;
                this.editing = false;
            }
        }
    }
</script>
