<template>
  <div v-if="0">
    <md-button
      v-if="vueWp.currentUserId"
      class="md-primary md-raised"
      @click="doUserAction('add_user')"
      >Add user [{{ vueWp.currentUserId }}] to Post [{{ postId }}]</md-button
    >
    <md-button class="md-primary md-raised" @click="doUserAction('remove_user')"
      >Remove user from Post {{ postId }}</md-button
    >
    <ul v-if="users.length">
      <li v-for="user in users" :key="user.user_id">
        {{ user.user_id }}
      </li>
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
    showDialog: false,
    vueWp: {},
    users: {},
    error: ''
  }),
  props: ['postId'],
  mounted () {
    console.log()
    this.vueWp = window.vueWp
    this.doUserAction('get_all_users')
  },
  methods: {
    doUserAction (task) {
      console.log('doUserAction', task, this.vueWp.ajaxUrl)
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
        }

        console.log('avresp', response)
      })
    }
  }
}
</script>

<style scoped>
.md-dialog {
  max-width: 768px;
}
</style>
