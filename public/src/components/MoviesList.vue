<template>
  <div>
    <span class="help is-info" v-if="isLoading">Loading...</span>
    <table class="table" v-else>
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Bad Puns Count</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <template v-for="movie in movies">
          <tr v-bind:key="movie.id">
            <td>{{ movie.id }}</td>
            <td>{{ movie.title }}</td>
            <td>{{ movie.count }}</td>
            <td>
              <form @submit.prevent="onSubmit(movie)">
                <button class="button is-primary" v-bind:class="{ 'is-loading' : isCountUpdating(movie.id) }">Increase
                  Count</button>
              </form>
            </td>
          </tr>
        </template>
      </tbody>
    </table>
    <movie-form @completed="addMovie"></movie-form>
  </div>
</template>

<script>
  import axios from 'axios'
  import Vue from 'vue'
  import MovieForm from './MovieForm.vue'
  import http from '@/utils/http';
  import {
    setCookie,
    getCookie
  } from '../utils/cookie';

  export default {
    components: {
      MovieForm
    },
    data() {
      return {
        movies: [
          /*
          {"id":1,"title":"zhangbing","count":5},
          {"id":2,"title":"zhangruixin","count":3},
          */
        ],
        isLoading: true,
        countUpdatingTable: []
      }
    },
    async created() {
      //axios.defaults.headers.common['Authorization'] = `Bearer ${await this.$auth.getAccessToken()}`
      //axios.defaults.baseURL = process.env.API_HOST;
      //console.log(axios.defaults.baseURL);
      try {
        //const response = await axios.get('/movies')
        console.log("in created.");
        //setCookie("XDEBUG_SESSION", "PHPSTORM");
        const response = await http.get('/movies');
        console.log(response);
        if (response.success) {
          this.movies = response.data;
          this.isLoading = false;
        }
      } catch (e) {
        //TODO handle the exception
        this.isLoading = true;
        console.error(e);
      }
    },
    methods: {
      onSubmit(movie) {
        Vue.set(this.countUpdatingTable, movie.id, true)
        this.increaseCount(movie)
      },
      async increaseCount(movie) {
        try {
          //axios.defaults.headers.common['Authorization'] = `Bearer ${await this.$auth.getAccessToken()}`
          //axios.defaults.baseURL = process.env.API_HOST;
          console.log(axios.defaults.baseURL);
          console.log("increaseCount");
          let turl = '/movies/' + movie.id + '/count';

          const response = await http.post(turl);

          if (response.success) {
            movie.count = response.data.count
            this.countUpdatingTable[movie.id] = false
          } else {
            this.$message.error(result.message)
          }
        } catch (e) {
          // handle authentication and validation errors here
          this.countUpdatingTable[movie.id] = false
        }
      },
      isCountUpdating(id) {
        return this.countUpdatingTable[id]
      },
      addMovie(movie) {
        this.movies.push(movie)
      }
    }
  }
</script>

<style scoped>

</style>
