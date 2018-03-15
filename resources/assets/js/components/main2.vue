<template>
    <div class="tab-pane" style="width: 95%;">
        <div class="panel-heading">Panel heading</div>
        <table class="table">
            <thead>
            <tr>
                <th>名称</th>
                <th>输出</th>
                <th>平均交易量(30D)</th>
                <th>买单</th>
                <th>卖单</th>
                <th v-on:click="sort('profit_avg_0')">s_t_s</th>
                <th>b_t_s</th>
                <th>s_t_b</th>
                <th>b_t_b</th>
                <th>profit_0</th>
                <th>profit_3</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="item in sortList" v-bind:key="item.id">
                <td>{{ item.name }}</td>
                <td>{{ item.output * item.vol }} m3 / {{ item.output }} / {{ item.output * 780 }}</td>
                <td>{{ item.avg_vol }}</td>
                <td>{{ item.buy }} / {{ item.buy_num }}</td>
                <td>{{ item.sell }}</td>
                <td>{{ item.profit_avg_0 }}</td>
                <td>{{ item.profit_avg_2 }}</td>
                <td>{{ item.profit_avg_1 }}</td>
                <td>{{ item.profit_avg_3 }}</td>
                <td>{{ item.profit_simple_avg_0 }}</td>
                <td>{{ item.profit_simple_avg_3 }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
//        created: function () {
//            console.log('组件被调用')
//        },

        props: ['list'],

        methods: {
            sortDesc(name) {
                return function (o, p) {
                    let a = o[name];
                    let b = p[name];
                    return b - a;
                }
            }
        },
        computed: {
            // 计算属性的 getter
            sortList: function () {
                // `this` 指向 vm 实例
                return this.list.sort(this.sortDesc('profit_avg_0'));
            }
        }
    }
</script>
