<template>
    <div>
        <div v-for="(reply, index) in items" :key="reply.id">
            <reply :reply="reply" @deleted="remove(index)"></reply>
            <br>
        </div>

        <paginator :dataSet="dataSet" @changed="fetch"></paginator>

        <p v-if="$parent.locked">
            This thread has been locked. No more replies allowed.
        </p>

        <new-reply @created="add" v-if="! $parent.locked"></new-reply>
    </div>
</template>

<script>
    import Reply from './ReplyComponent.vue';
    import NewReply from './NewReplyComponent.vue';
    import Paginator from './PaginatorComponent.vue';
    import collection from '../mixins/collection';

    export default {
        components: { Reply, NewReply, Paginator },

        mixins: [collection],

        data() {
            return {
                dataSet: false,
            }
        },

        created() {
            this.fetch();
        },

        methods: {
            fetch(page) {
                axios.get(this.url(page))
                    .then(this.refresh);
            },

            refresh(response) {
                this.dataSet = response.data;
                this.items = response.data.data;

                window.scrollTo(0, 0);
            },

            url(page) {
                if(! page) {
                    let query = location.search.match(/page=(\d+)/);

                    page = query ? query[1] : '1';
                }
                return location.pathname + '?page=' + page;
            },
        }
    }
</script>