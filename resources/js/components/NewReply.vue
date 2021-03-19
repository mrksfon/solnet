<template>
    <div style="padding-top:10px;">
        <div v-if="signedIn">
            <div class="form-group">
                <textarea
                    name="body"
                    id="body"
                    class="form-control"
                    placeholder="Have something to say?"
                    required
                    rows="5"
                    v-model="body">

                </textarea>
            </div>

            <button type="submit" class="btn btn-primary" @click="addReply">Post</button>
        </div>
        <p v-else class="text-center">Please <a href="/login">sign in</a> to participate in this
            discussion
        </p>
    </div>
</template>

<script>
export default {
    props : ['endpoint'],

    data() {
        return {
            body: '',
        }
    },

    computed: {
        signedIn() {
            return window.App.signedIn;
        }
    },

    methods: {
        async addReply() {

            const {data} = await axios.post(this.endpoint, {body: this.body});

            this.body = '';

            flash('Your reply has been posted');

            this.$emit('created',data);
        }
    }
}
</script>

