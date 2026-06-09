<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF</title>
    <style>
        table, td, th{
            border:1px solid black;
            padding: 5px;
        }

        table{
            width: 100%;
            margin-top:10px;
        }
    </style>
</head>
<body>
    <div>
       <span>User Name: </span><span>{{$user['name']}}</span><br>
       <span>User Email: </span><span>{{$user['email']}}</span>
    </div>
    <table>
        <tr>
            <th>NO.</th>
            <th>Title</th>
            <th>Caption</th>
            <th>Category</th>
            <th>Complete</th>
            <th>Created_at</th>
            <th>Updated_at</th>
        </tr>
        @php $couter=0; @endphp
        @foreach($tasks as $task)
            <tr>
                <td>{{++$couter}}</td>
                <td>{{$task['title']}}</td>
                <td>{{$task['caption']}}</td>
                <td>{{$task['category']}}</td>
                <td>{{$task['isActive'] ? 'yes' : 'no'}}</td>
                <td>{{$task['created_at']}}</td>
                <td>{{$task['updated_at']}}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>