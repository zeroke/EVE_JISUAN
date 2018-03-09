<!doctype html>
<html xmlns:v-bind="http://www.w3.org/1999/xhtml" xmlns:v-on="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Laravel</title>


    <script src="https://unpkg.com/vue/dist/vue.js"></script>
    {{--<script src="https://unpkg.com/vue-router/dist/vue-router.js"></script>--}}
</head>
<body>

@verbatim
    <div id="app-7">
        <div>
            <span v-for="n in 10">{{ n }} </span>
        </div>
        <ul>
            <!--
              现在我们为每个 todo-item 提供 todo 对象
              todo 对象是变量，即其内容可以是动态的。
              我们也需要为每个组件提供一个“key”，稍后再
              作详细解释。
            -->
            <todo-item
                    v-for="item in groceryList"
                    v-bind:todo="item"
                    v-bind:key="item.id">
            </todo-item>
        </ul>
    </div>
@endverbatim

</body>

{{--<script src="{{ asset("js/app.js") }}"></script>--}}

<script>
    Vue.component('todo-item', {
        props: ['todo'],
        template: '<li>@{{ todo.text }}</li>'
    })

    var app7 = new Vue({
        el: '#app-7',
        data: {
            groceryList: [
                { id: 0, text: '蔬菜' },
                { id: 1, text: '奶酪' },
                { id: 2, text: '随便其它什么人吃的东西' }
            ]
        }
    })
</script>
</html>
