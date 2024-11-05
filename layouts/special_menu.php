<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Sidebar Menu</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            transition: margin-left 0.3s;
            overflow-x: hidden;
        }

        /* Sidebar */
        #sidePanel {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #111;
            padding-top: 60px;
            transition: width 0.3s;
        }

        #sidePanel a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 18px;
            color: #fff;
            display: block;
            transition: background-color 0.3s;
        }

        #sidePanel a:hover {
            background-color: #333;
        }

        #sidePanel .icon {
            margin-right: 10px;
        }

        /* Toggle Button */
        .menu-btn {
            font-size: 26px;
            cursor: pointer;
            background-color: #111;
            color: white;
            border: none;
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1001;
        }
    </style>
</head>
<body>

    <!-- Toggle Button -->
    <button class="menu-btn" onclick="togglePanel()">&#9776;</button>

    <!-- Sidebar -->
    <div id="sidePanel">
        <a href="#dashboard"><i class="fas fa-tachometer-alt icon"></i>Dashboard</a>
        <a href="#product"><i class="fas fa-box icon"></i>Product</a>
        <a href="#category"><i class="fas fa-tags icon"></i>Category</a>
        <a href="#users"><i class="fas fa-user icon"></i>Users</a>
        <a href="#group"><i class="fas fa-users icon"></i>Group</a>
        <a href="#sales"><i class="fas fa-chart-line icon"></i>Sales</a>
    </div>

    <script>
        function togglePanel() {
            const panel = document.getElementById("sidePanel");
            if (panel.style.width === "250px") {
                panel.style.width = "0";
            } else {
                panel.style.width = "250px";
            }
        }
    </script>

</body>
</html>
