<template>
  <form @submit.prevent="onSubmit">
    <span class="help is-danger" v-text="errors"></span>

    <div class="field">
      <div class="control">
        <input class="input" type="title" placeholder="enter movie title..." v-model="title" @keydown="errors = ''">
      </div>
    </div>

    <button class="button is-primary" v-bind:class="{ 'is-loading' : isLoading }">Add Movie</button>
  </form>
</template>

<script>
  import axios from 'axios'
  import http from '@/utils/http'

  export default {
    data() {
      return {
        title: '',
        errors: '',
        isLoading: false
      }
    },
    methods: {
      onSubmit() {
        this.isLoading = true
        this.postMovie()
      },
      async postMovie() {
        //axios.defaults.headers.common['Authorization'] = `Bearer ${await this.$auth.getAccessToken()}`
        //axios.defaults.baseURL = process.env.API_HOST;
        //console.log(axios.defaults.baseURL);
        try {
          const response = await http.post('/movies', this.$data);

          if (response.success) {
            this.title = ''
            this.isLoading = false
            this.$emit('completed', response.data)
          } else {
            this.errors = result.message;
            this.isLoading = false
          }
        } catch (e) {
          // handle authentication and validation errors here
          // handle authentication and validation errors here
          //this.errors = response.message
          this.isLoading = false
        }
      }
    }
  }
</script>
