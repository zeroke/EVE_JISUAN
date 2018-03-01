<!doctype html>
<html xmlns:v-bind="http://www.w3.org/1999/xhtml" xmlns:v-on="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    <link href="{{asset('/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" media="screen">
</head>
<body>

<div class="container">
    @foreach($data['Composite'] as $v)
        <div class="row">
            <div class="col-md-2">{{ $v['name'] }}</div>
            <div class="col-md-1">{{ $v['output'] }}</div>
            <div class="col-md-1">{{ $v['price'] }}</div>
            <div class="col-md-1">{{ $v['profit_avg'] }}</div>
            <div class="col-md-4">
                <ul>
                    @foreach($v['item'] as $i)
                        <li>{{ $i['name'] }} : {{ $i['profit'] }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-2">{{ $v['profit'] }} + {{ $v['profit_item'] }}</div>
        </div>
    @endforeach
</div>

</body>
</html>
