<template>

    <div>
        <div class="level">
            <img :src="avatar" width="50" height="50" class="mr-2">
                        
            <h3 v-text="name"></h3>
        </div>

        <form v-if="canUpdate" enctype="multipart/form-data">
            <input type="file" name="avatar" accept="image/*" @change="onChange">
            <!-- <button @click.prevent="upload">Upload</button> -->
        </form>

    </div>

</template>

<script>
    export default {
        props: ['user'],

        data() {
            return {
                name: this.user.name,
                avatar: this.user.avatar_path
            }
        },

        computed: {
            canUpdate() {
                return this.authorize(user => user.id === this.user.id);
            }
        },

        methods: {
            upload(avatar) {
                let data = new FormData();
                data.append('avatar', avatar);
                axios.post('/api/users/' + this.user.id + '/avatar', data)
                    .then(() => flash('Image uploaded'));
            },

            onChange(e) {
                if(! e.target.files.length) return;
                let avatar = e.target.files[0];
                let reader = new FileReader();
                reader.readAsDataURL(avatar);
                reader.onload = e => {
                    this.avatar = e.target.result;
                }
                this.upload(avatar);
            }
        }
    };
</script>