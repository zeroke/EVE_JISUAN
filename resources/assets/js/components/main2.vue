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
            <tr v-for="item in list" v-bind:key="item.id">
                <td>{{ item.name }}</td>
                <td>{{ item.output * item.vol }} m3 / {{ item.output }} / {{ item.output * 110 }}</td>
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
        props: ['list'],

        methods: {
            sort(type) {
                this.$emit('sort', type);
            }
        }
    }
</script>
