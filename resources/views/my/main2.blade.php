<!doctype html>
<html xmlns:v-bind="http://www.w3.org/1999/xhtml" xmlns:v-on="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    <link href="{{ asset('/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" media="screen">
    <link href="{{ asset('/js/DataTables-1.10.15/media/css/dataTables.bootstrap.min.css') }}" rel="stylesheet"
          media="screen">
    <script type="text/javascript" src="{{ asset('/js/DataTables-1.10.15/media/js/jquery.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/bootstrap/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript"
            src="{{ asset('/js/DataTables-1.10.15/media/js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript"
            src="{{ asset('/js/DataTables-1.10.15/media/js/dataTables.bootstrap.min.js') }}"></script>
</head>
<body>

<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#feg" aria-controls="feg" role="tab" data-toggle="tab">伏尔戈</a></li>
    <li role="presentation"><a href="#delve" aria-controls="delve" role="tab" data-toggle="tab">绝地</a></li>
</ul>
<div class="tab-content">
@foreach($data as $k => $region)
    <div class="tab-pane" style="width: 95%;" id="{{ $k }}" role="tabpanel">
        <div class="panel-heading">Panel heading</div>
        <table class="table">
            <thead>
            <tr>
                <th>名称</th>
                <th data-sortable="false">输出</th>
                <th data-sortable="false">买单</th>
                <th data-sortable="false">卖单</th>
                <th>s_t_s</th>
                <th>s_t_b</th>
                <th>b_t_s</th>
                <th>b_t_b</th>
                <th>profit</th>
            </tr>
            </thead>
            <tbody>
            @foreach($region as $v)
                <tr>
                    <td>{{ $v['name'] }}</td>
                    <td>{{ $v['output'] * $v['vol'] }} m3 / {{ $v['output'] }}</td>
                    <td>{{ $v['buy'] }} / {{ $v['buy_num'] }}</td>
                    <td>{{ $v['sell'] }}</td>
                    <td>{{ $v['profit_avg_0'] }}</td>
                    <td>{{ $v['profit_avg_1'] }}</td>
                    <td>{{ $v['profit_avg_2'] }}</td>
                    <td>{{ $v['profit_avg_3'] }}</td>
                    <td>{{ $v['profit_0'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endforeach
</div>

<script>
    $(function () {
        $('.table').dataTable({
            'order': [4, 'desc'],
            'pageLength': 50
        });

        $(".tab-pane").eq(0).addClass('active');
        $('.nav-tabs a').click(function (e) {
            e.preventDefault()
            $(this).tab('show')
        })
    })
</script>
</body>
</html>
