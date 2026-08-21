<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>DevOps Task API</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }

        .container {
            width: 90%;
            max-width: 700px;
            padding: 50px;
            background: white;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .status {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 16px;
            border-radius: 20px;
            background: #dcfce7;
            color: #166534;
            font-size: 14px;
            font-weight: bold;
        }

        h1 {
            margin-bottom: 15px;
            font-size: 36px;
        }

        p {
            margin-bottom: 10px;
            color: #6b7280;
            line-height: 1.6;
        }

        .version {
            margin-top: 25px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="status">
            ● API ONLINE
        </div>

        <h1>DevOps Task API</h1>

        <p>
            Welcome to the DevOps Task API.
        </p>

        <p>
            Laravel application running with Docker,
            PostgreSQL, Redis and CI/CD.
        </p>

        <div class="version">
            Version: 1.0.0
        </div>

    </div>

</body>
</html>