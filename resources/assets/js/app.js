import Vue from 'vue';
import TicketCheckout from './components/TicketCheckout.vue';

require('./bootstrap');

var app = new Vue({
    el: '#app',
    components: {
        TicketCheckout,
    }
});
