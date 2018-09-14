<template>
    <button type="submit" :class="classes" @click="toggle">
        <i class="fa fa-heart" aria-hidden="true"></i>
        <span v-text="count"></span>
    </button>
</template>

<script>
    export default {
        props: ['reply'],

        data() {
            return {
                count: this.reply.favoritesCount,
                isFavorited: this.reply.isFavorited,
            }
        },

        computed: {
            classes() {
                return ['btn', this.isFavorited ? 'btn-primary' : 'btn-default'];
            }
        },

        methods: {
            toggle() {
                this.isFavorited ? this.destroy() : this.create();
            },

            create() {
                axios.post('/replies/' + this.reply.id + '/favorites');
                this.isFavorited = true;
                this.count++;
            },

            destroy() {
                axios.delete('/replies/' + this.reply.id + '/favorites');
                this.isFavorited = false;
                this.count--;
            },
        }
    };
</script>