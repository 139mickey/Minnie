<template>
  <div id="app">
    <img src="./assets/logo.png">
    <ul>
      <template v-for="route in routes">
        <router-link :to="route.path">{{route.name}}</router-link>
      </template>
    </ul>
    <transition>
      <router-view></router-view>
    </transition>
  </div>
</template>

<script>
  export default {
    name: 'App',
    computed: {
      routes() {
        console.log(this.$router.options.routes);
        return this.$router.options.routes;
      }
    }
  }
</script>

<style>
  body {
    margin: 0px auto;
    width: 1024px;
    height: 768px;
  }

  #app {
    font-family: 'Avenir', Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;
    margin-top: 60px;
  }
</style>
