<template>
    <button :class="classes" @click="subscribe">Subscribe</button>
</template>

<script>
    export default{
        props: ['active'],

        data(){
            return{
                subscribed: this.active
            }
        },

        computed : {
          classes(){
              return ['btn',this.subscribed ? 'btn-primary' : 'btn-secondary'];
          }
        },

        methods: {
             subscribe(){
                 axios[(this.subscribed ? 'delete' : 'post')](location.pathname + '/subscriptions');

                 this.subscribed = !this.subscribed;

                 if(this.subscribed){
                     flash('Subscribed');
                 }else{
                     flash('Unsubscribed');
                 }
            }
        }
    }
</script>
