<!doctype html>
<html xmlns:v-bind="http://www.w3.org/1999/xhtml" xmlns:v-on="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Laravel</title>


    <script src="https://unpkg.com/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/vue-router/dist/vue-router.js"></script>
</head>
<body>

@verbatim
    <div id="app">
    </div>
@endverbatim

</body>

{{--<script src="{{ asset("js/app.js") }}"></script>--}}

<script>

    const Home = { template: '<div>This is Home</div>' }
    const Foo = { template: '<div>This is Foo</div>' }
    const Bar = { template: '<div>This is Bar @{{ $route.params.id }}</div>' }

    const router = new VueRouter({
        mode: 'history',
        base: '/',
        routes: [
            { path: '/', name: 'home', component: Home },
            { path: '/foo', name: 'foo', component: Foo },
            { path: '/bar/:id', name: 'bar', component: Bar }
        ]
    })

    new Vue({
        router,
        template: `
    <div id="app">
      <h1>Named Routes</h1>
      <p>Current route name: @{{ $route.name }}</p>
      <ul>
        <li><router-link :to="{ name: 'home' }">home</router-link></li>
        <li><router-link :to="{ name: 'foo' }">foo</router-link></li>
        <li><router-link :to="{ name: 'bar', params: { id: 123 }}">bar</router-link></li>
      </ul>
      <router-view class="view"></router-view>
    </div>
  `
    }).$mount('#app')
</script>
</html>
