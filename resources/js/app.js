import Vue from 'vue';
require('./bootstrap');
require('./sb-admin');

Vue.component('create-product', require('./components/CreateProduct.vue').default);


const app = new Vue({
    el: '#app',
});
