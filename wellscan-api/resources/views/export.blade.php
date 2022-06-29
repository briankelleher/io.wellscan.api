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

        input, select, select option {
            color: black;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1>WellSCAN Export</h1>
        
        <hr>

        <div class="row">
            <div class="col">
                <form action="{{ route('export-her') }}" method="POST" class="mb-5">
                    @csrf
                    <div class="form-contain mt-4 mb-4">
                        <h3>Export by HER Category</h3>
                        <div class="form-group">
                            <label for="herSelect">SWAP / HER Category</label>
                            <select name="her" id="herSelect" class="form-control">
                                @foreach ($hers as $her)
                                    @if ($her)
                                        <option value="{{ $her['her'] }}">{{ $her['her'] }}</li>
                                    @endif
                                @endforeach  
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Export</button>
                    </div>
                    
                </form>
            </div>
            <div class="col">
                <form action="{{ route('export-tag') }}" method="POST" class="mb-5">
                    @csrf
                    <div class="form-contain mt-4 mb-4">
                        <h3>Export by Tag</h3>
                        <div class="form-group">
                            <label for="tagInput">Tag</label>
                            <input name="tag" id="tagInput" class="form-control" placeholder="stew">
                        </div>
                    </div>
                    <button class="btn btn-primary">Export</button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <form action="{{ route('export-complex') }}" method="POST" class="mb-5">
                    @csrf
                    <div class="form-contain mt-4 mb-4">
                        <h3>Export Complex</h3>
                        <p>All fields filled in here will be combined in a query.</p>
                        <div class="form-group">
                            <label for="herMultiSelect">SWAP / HER Category</label>
                            <select name="her[]" id="herMultiSelect" multiple class="form-control">
                                @foreach ($hers as $her)
                                    @if ($her)
                                        <option value="{{ $her['her'] }}">{{ $her['her'] }}</li>
                                    @endif
                                @endforeach  
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tagInput1">AND Tag 1</label>
                            <input name="tag[]" id="tagInput1" placeholder="stew" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="tagInput2">AND Tag 2</label>
                            <input name="tag[]" id="tagInput2" placeholder="stew" class="form-control">
                        </div>
                    </div>
                    <button class="btn btn-primary">Export</button>
                </form>
            </div>
            <div class="col"></div>
        </div>
    </div>
    
</body>
</html>