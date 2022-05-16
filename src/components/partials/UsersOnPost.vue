<template>
  <div  v-if="this.loading" :key="componentKey" class="md-card-area users solution-props" >
      <b v-if="this.emtpyUsers"> No users  connected to this school yet:</b>
     <b v-else>User/s connected to this school:</b>
      <ul class ="post-images small no-bullet flex padding-left-0">
      <li class ="avatar-container" v-for="user in users" :key="user.ID" :title = "user.display_name">
        <a :href = "user.user_profile_url"  class="clipped square" target=_blank>
          <img alt="" :src= "user.user_avatar"  class="avatar avatar-36 photo" height="36" width="36" loading="lazy">
        </a>
      </li>
       <a href="#" class="alert user md-button" v-if="showRemoveButton" @click="doUserAction('remove_user')"  title="remove me from the list"> 
        <i  class="md-icon md-icon-font md-theme-z">person_remove</i> Remove me</a>
      <a href="#" class="success md-button" v-else @click="doUserAction('add_user')" title="Add me to the list">
         <i  class="md-icon  md-icon-font md-theme-z">person_add</i> Add me
     </a>
    </ul>
    <md-chip v-if="this.error" class="md-accent" md-deletable>{{
      this.error
    }}</md-chip>
  </div>
</template>
<script>
import axios from 'axios'
// https://vuematerial.io/components/dialog/
export default {
  data: () => ({
    componentKey: '',
    showRemoveButton: false,
    emtpyUsers:false,
    loading:false,
    vueWp: {},
    users: {},
    error: ''
  }),
  props: ['postId'],
  mounted () {
    this.vueWp = window.vueWp
    this.doUserAction('get_all_users')
   
  },
  methods: {
    checkCurrentUserIn() {
      if (!this.users || !this.users.length ) {
          this.showRemoveButton = false
          this.emtpyUsers=true
          return
        }
      for (var i = 0; i < this.users.length; i++) { 
         if (this.vueWp.currentUserId == this.users[i].ID) {
            this.showRemoveButton = true
            this.emtpyUsers=false
            return
         }
         else {
            this.showRemoveButton = false
            this.emtpyUsers=false
         }
        }
    },
    doUserAction (task) {
      const params = {
        action: 'users_on_post',
        task: task,
        post_id: this.postId,
        user_id: this.vueWp.currentUserId,
        nonce: this.vueWp.apiNonce
      }
      axios.get(this.vueWp.ajaxUrl, { params }).then((response) => {
        if (!response.data.success) {
          this.error = response.data.data
        } else {
          this.error = ''
         
          if (task === 'get_all_users') {
            this.users = response.data.data
          }
          if (task === 'add_user' || task === 'remove_user') {
               this.doUserAction('get_all_users')
          }
           this.checkCurrentUserIn()
           this.loading = true
        }
      })
    }
  }
}
</script>

<style scoped>
.users a.md-button {
  min-width: auto;
  height: auto;
  padding:7px 7px 5px 8px;
  margin:0;
}
</style>
