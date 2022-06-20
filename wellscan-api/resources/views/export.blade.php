<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Arvo:ital,wght@0,400;0,700;1,400;1,700&family=Roboto:wght@400;500;700&display=swap">
    <title>WellSCAN Export</title>
    <style>
        html, body {
            background-color: #414143;
        }
        
        * {
            color: white;
            font-family: 'Roboto', sans-serif;
        }

        h1,h2,h3,h4,h5,h6 {
            font-family: 'Arvo', serif;
        }

        hr {
            background-color: white;
        }

        input, select {
            color: black;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1>WellSCAN Export</h1>
        
        <hr>
        <form action="{{ route('export-fano') }}" method="POST" >
        @csrf
            <div class="form-group mt-4 mb-4">
                <h3>Export By FANO</h3>
                <select name="fano" id="fanoSelect">
                    @foreach ($fanos as $fano)
                        <option value="{{ $fano['fano'] }}">{{ $fano['fano'] }}</li>
                    @endforeach  
                </select>
                <button class="btn btn-primary">Export</button>
            </div>
        </form>

        <form action="{{ route('export-her') }}" method="POST">
            @csrf
            <div class="form-group mt-4 mb-4">
                <h3>Export by HER Category</h3>
                <select name="her" id="herSelect">
                    @foreach ($hers as $her)
                        <option value="{{ $her['her'] }}">{{ $her['her'] }}</li>
                    @endforeach  
                </select>
                <button class="btn btn-primary">Export</button>
            </div>
        </form>

        <form action="{{ route('export-tag') }}" method="POST">
            @csrf
            <div class="form-group mt-4 mb-4">
                <h3>Export by Tag</h3>
                <input name="tag" id="tagInput" placeholder="stew">
                <button class="btn btn-primary">Export</button>
            </div>
        </form>
    </div>
    
</body>
</html>