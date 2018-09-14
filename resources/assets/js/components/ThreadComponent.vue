<script>
    import Replies from './RepliesComponent.vue';
    import SubscribeButton from './SubscribeComponent.vue';

    export default {
        props: ['thread'],

        components: { Replies, SubscribeButton },

        data() {
            return {
                repliesCount: this.thread.replies_count,
                locked: this.thread.locked,
                slug: this.thread.slug,
                editing: false,
                form: {
                    title: this.thread.title,
                    body: this.thread.body
                },
                title: this.thread.title,
                body: this.thread.body,
            }
        },

        methods: {
            toggleLock() {
                this.locked = ! this.locked;
                axios.post('/locked_thread/' + this.slug);
            },
            updateThread() {
                axios.patch('/threads/' + this.thread.channel.slug + '/' + this.thread.slug, this.form)
                .then(() => {
                    this.title = this.form.title;
                    this.body = this.form.body;
                    this.editing = false;
                    flash('Your thread has been updated');
                });
            },
            cancelThread() {
                this.form = {
                    title: this.title,
                    body: this.body
                }
                this.editing = false;
            }
        }
    }
</script>