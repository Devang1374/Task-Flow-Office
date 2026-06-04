<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body>
    <div>
        <flux:table>
    <flux:table.columns>
        <flux:table.column sortable>User_id</flux:table.column>
        <flux:table.column sortable>Title</flux:table.column>
        <flux:table.column>Catpion</flux:table.column>
        <flux:table.column>IA Active</flux:table.column>
        <flux:table.column>Created_at</flux:table.column>
        <flux:table.column>Updated_at</flux:table.column>
    </flux:table.columns>

    <flux:table.rows>
        @foreach($tasks as $task)
        <flux:table.row>
            <flux:table.cell>{{$task['user_id']}}</flux:table.cell>
            <flux:table.cell>{{$task['title']}}</flux:table.cell>
            <flux:table.cell>{{$task['caption']}}</flux:table.cell>
            <flux:table.cell>{{$task['isActive']}}</flux:table.cell>
            <flux:table.cell>{{$task['created_at']}}</flux:table.cell>
            <flux:table.cell>{{$task['updated_at']}}</flux:table.cell>
        </flux:table.row>
        @endforeach
    </flux:table.rows>
</flux:table>    

        <div class="flex w-full align-center justify-center">
                
        {{$tasks->links()}}
        </div>
    </div>
    {{$fake}}
    @fluxScripts
</body>
</html>