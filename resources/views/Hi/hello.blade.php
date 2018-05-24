<html>
<head>
    <title>
        This is hello view!
    </title>
</head>
<body>
        welcome to the hello view!
        <div class="container">
            <div class="panel-heading">上传文件</div>
            <form role="form" class="form-horizontal" method="POST" action="/hi/upload1" enctype="multipart/form-data">
            {{ csrf_field() }}
            <label for="file">选择文件</label>
            <input id="file" type="file" class="form-control" name="source" required>
            {{--<input type="hidden" name="_token" value="{{csrf_token()}}"/>--}}
            <button type="submit" class="btn btn-primary">确定</button>
            </form>
        </div>
</body>
</html>