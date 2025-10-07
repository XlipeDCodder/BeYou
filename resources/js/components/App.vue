<template>
  <Navbar />
  <main>
    <router-view></router-view>
  </main>
</template>

<script setup>
import Navbar from './Navbar.vue';
</script>

<style>
body {
  background-color: #121212;
  margin: 0;
}
</style>