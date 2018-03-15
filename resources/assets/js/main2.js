/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import Vue from 'vue';
import axios from 'axios';

// import main2 from './components/main2';

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('main2', require('./components/main2.vue'));

const app = new Vue({
    el: '#app',
    components: {
        'main2': require('./components/main2.vue')
    },
    template: '<main2 :list="list"></main2>',
    data: {
        list: []
    },

    methods: {}
});

axios.get('/api/jisuan').then(res => {
    app.list = res.data;
});