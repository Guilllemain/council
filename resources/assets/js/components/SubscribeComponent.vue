<template>
    <button :class="classes" @click="subscribe" v-text="following"></button>
</template>

<script>
    export default {
        props: ['active'],

        computed: {
            classes() {
                return ['btn', this.active ? 'btn-primary' : 'btn-outline-info'];
            },

            following() {
                return this.active ? 'Following' : 'Follow';
            }
        },

        methods: {
            subscribe() {
                axios[(this.active ? 'delete' : 'post')](location.pathname + '/subscriptions');

                this.active = ! this.active;
            }
        }
    }
</script>