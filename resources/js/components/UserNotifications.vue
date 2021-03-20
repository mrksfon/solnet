<template>
    <li class="nav-item dropdown" v-if="notifications.length" v-cloak>
        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
            <i class="fa fa-bell"></i>
        </a>

        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
            <div v-for="notification in notifications">
                <a :href="notification.data.link" class="dropdown-item" v-text="notification.data.message" @click="markAsRead(notification)"></a>
            </div>
        </div>
    </li>

</template>

<script>
export default {
    data() {
        return {
            notifications: false
        }
    },

    created(){
        this.fetchData();
    },

    methods : {
        async fetchData(){
            const {data} = await axios.get('/profiles/' + window.App.user.name + '/notifications');

            this.notifications = data;
        },
        async markAsRead(notification){
            await axios.delete('/profiles/' + window.App.user.name + '/notifications/' + notification.id);
        }
    }
}
</script>
