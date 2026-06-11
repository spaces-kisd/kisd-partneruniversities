<template>
  <div
    v-if="ready && isLoggedIn"
    class="md-card-area users solution-props"
  >
    <b v-if="users.length === 0">No users connected to this school yet.</b>
    <b v-else>Connected to this school:</b>
    <ul class="post-images no-bullet flex padding-left-0">
      <li
        v-for="user in users"
        :key="user.ID"
        class="avatar-container"
      >
        <!-- own avatar: leave button -->
        <button
          v-if="user.ID == currentUserId"
          class="avatar-btn is-me"
          @click="doUserAction('remove_user', user.ID)"
        >
          <img
            alt="You"
            :src="user.user_avatar"
            class="avatar photo"
            height="40"
            width="40"
            loading="lazy"
          >
          <span class="avatar-leave">
            <svg
              class="icon"
              viewBox="0 0 24 24"
              aria-hidden="true"
            ><path
              fill="currentColor"
              d="M19 6.41 17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"
            /></svg>
          </span>
          <span class="avatar-label">{{ user.display_name }}</span>
        </button>

        <!-- super-admin view of other user: remove button -->
        <button
          v-else-if="isSuperAdmin"
          class="avatar-btn is-removable"
          @click="doUserAction('remove_user', user.ID)"
        >
          <img
            alt=""
            :src="user.user_avatar"
            class="avatar photo"
            height="40"
            width="40"
            loading="lazy"
          >
          <span class="avatar-leave">
            <svg
              class="icon"
              viewBox="0 0 24 24"
              aria-hidden="true"
            ><path
              fill="currentColor"
              d="M19 6.41 17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"
            /></svg>
          </span>
          <span class="avatar-label">{{ user.display_name }}</span>
        </button>

        <!-- regular view: profile link -->
        <a
          v-else
          :href="user.user_profile_url"
          class="avatar-btn"
          target="_blank"
        >
          <img
            alt=""
            :src="user.user_avatar"
            class="avatar photo"
            height="40"
            width="40"
            loading="lazy"
          >
          <span class="avatar-label">{{ user.display_name }}</span>
        </a>
      </li>

      <!-- join placeholder -->
      <li
        v-if="currentUserId && !isCurrentUserIn"
        class="avatar-container"
      >
        <button
          class="avatar-btn avatar-join"
          @click="doUserAction('add_user', currentUserId)"
        >
          <svg
            class="icon"
            viewBox="0 0 24 24"
            aria-hidden="true"
          ><path
            fill="currentColor"
            d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-1V8H4v3H1v2h3v3h2v-3h3v-2H6zm9 3c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"
          /></svg>
          <span class="avatar-label">Add me</span>
        </button>
      </li>
    </ul>
    <md-chip
      v-if="error"
      class="md-accent"
      md-deletable
    >
      {{ error }}
    </md-chip>
  </div>
</template>

<script>
const vueWp = window.vueWp || {}

export default {
  props: ['postId'],
  data: () => ({
    ready: false,
    users: [],
    error: ''
  }),
  computed: {
    isLoggedIn () {
      return !!vueWp.isLoggedIn
    },
    currentUserId () {
      return vueWp.currentUserId || 0
    },
    isSuperAdmin () {
      return !!vueWp.isSuperAdmin
    },
    isCurrentUserIn () {
      // eslint-disable-next-line eqeqeq
      return this.users.some(u => u.ID == this.currentUserId)
    }
  },
  mounted () {
    // Student data is private: only fetch/show it to logged-in users.
    if (!this.isLoggedIn) {
      return
    }
    // Only reveal the block once the server actually returns the data.
    // A logged-out (or cached-as-logged-in) request fails and stays hidden.
    this.request('get_all_users')
      .then(users => {
        this.users = users
        this.ready = true
      })
      .catch(() => {
        this.ready = false
      })
  },
  methods: {
    doUserAction (task, userId) {
      this.request(task, userId)
        .then(users => {
          this.users = users
        })
        .catch(() => {})
    },
    request (task, userId = this.currentUserId) {
      const params = new URLSearchParams({
        action: 'users_on_post',
        task,
        post_id: this.postId,
        user_id: userId,
        nonce: vueWp.apiNonce
      })
      return fetch(`${vueWp.ajaxUrl}?${params}`)
        .then(res => res.json())
        .then(data => {
          // admin-ajax returns `0` for an unauthenticated request, and our
          // handler returns { success: false } when not logged in.
          if (!data || !data.success) {
            this.error = (data && data.data) || 'Request failed.'
            throw new Error(this.error)
          }
          this.error = ''
          return data.data
        })
    }
  }
}
</script>

<style scoped>
.users b {
  display: block;
  margin-bottom: 6px;
}

ul {
  gap: 6px;
  align-items: center;
  flex-wrap: wrap;
}

.avatar-container {
  position: relative;
}

.avatar-btn {
  display: block;
  position: relative;
  width: 40px;
  height: 40px;
  border-radius: 6px;
  overflow: visible;
  border: none;
  padding: 0;
  cursor: pointer;
  background: transparent;
}

.avatar-btn img {
  display: block;
  width: 40px;
  height: 40px;
  object-fit: cover;
  border-radius: 6px;
}

/* username tooltip */
.avatar-label {
  position: absolute;
  bottom: calc(100% + 6px);
  left: 50%;
  transform: translateX(-50%);
  background: rgba(0, 0, 0, 0.75);
  color: #fff;
  font-size: 11px;
  line-height: 1.2;
  white-space: nowrap;
  padding: 3px 7px;
  border-radius: 4px;
  pointer-events: none;
  opacity: 0;
  transition: opacity 0.15s;
  z-index: 10;
}

.avatar-btn:hover .avatar-label {
  opacity: 1;
}

.avatar-leave {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(180, 0, 0, 0.75);
  opacity: 0;
  transition: opacity 0.15s;
  border-radius: 6px;
  color: #fff;
}

.avatar-btn.is-me:hover .avatar-leave,
.avatar-btn.is-removable:hover .avatar-leave {
  opacity: 1;
}

.avatar-join {
  width: 40px;
  height: 40px;
  border-radius: 6px;
  border: 2px dashed currentColor;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0.5;
  transition: opacity 0.15s;
  color: inherit;
}

.avatar-join:hover {
  opacity: 1;
}

.icon {
  width: 20px;
  height: 20px;
  display: block;
}

.avatar-join .icon {
  width: 18px;
  height: 18px;
}
</style>
