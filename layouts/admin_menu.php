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
            overflow-x: hidden;
            transition: margin-left 0.3s;
        }

        /* Sidebar */
        #sidePanel {
            height: 100%;
            width: 0;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #111;
            overflow-x: hidden;
            padding-top: 60px;
            transition: width 0.3s;
            z-index: 1000; /* Higher z-index to overlay content */
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

        /* Dropdown container */
        .dropdown-container {
            display: none;
            background-color: #222;
            padding-left: 20px;
        }

        #sidePanel .dropdown-btn {
            font-size: 18px;
            padding: 15px 25px;
            text-align: left;
            background: none;
            border: none;
            outline: none;
            color: white;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }

        #sidePanel .dropdown-btn:hover {
            background-color: #333;
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
            z-index: 1001; /* Higher z-index for button to stay on top */
            border-radius: 4px;
            padding: 4px;
        }

        /* Shift content when sidebar is open */
        .content-shift {
            margin-left: 250px; /* Matches sidebar width */
        }
    </style>
</head>
<body>

    <!-- Toggle Button -->
    <button class="menu-btn" onclick="togglePanel()">&#9776;</button>

    <!-- Sidebar -->
    <div id="sidePanel">
        <a href="admin.php"><i class="fas fa-tachometer-alt icon"></i>Dashboard</a>
        <a href="product.php"><i class="fas fa-box icon"></i>Product</a>
        <a href="categorie.php"><i class="fas fa-tags icon"></i>Category</a>
        <a href="users.php"><i class="fas fa-user icon"></i>Users</a>
        <a href="group.php"><i class="fas fa-users icon"></i>Group</a>

        <!-- Dropdown Menu for Sales -->
        <button class="dropdown-btn">
            <i class="fas fa-chart-line icon"></i> Sales <i class="fas fa-caret-down" style="float: right;"></i>
        </button>
        <div class="dropdown-container">
            <a href="sales.php"><i class="fas fa-plus-circle icon"></i>Create Sale</a>
            <a href="sales_report.php"><i class="fas fa-file-alt icon"></i>Sale Report</a>
        </div>
    </div>

    <!-- Main Content -->
    <div id="mainContent" style="padding: 20px;">
    </div>

    <script>
        function togglePanel() {
            const panel = document.getElementById("sidePanel");
            const mainContent = document.getElementById("mainContent");

            if (panel.style.width === "250px") {
                panel.style.width = "0";
                mainContent.classList.remove("content-shift");
            } else {
                panel.style.width = "250px";
                mainContent.classList.add("content-shift");
            }
        }

        // Dropdown Toggle
        const dropdownBtn = document.querySelector(".dropdown-btn");
        const dropdownContainer = document.querySelector(".dropdown-container");

        dropdownBtn.addEventListener("click", function () {
            dropdownContainer.style.display = dropdownContainer.style.display === "block" ? "none" : "block";
        });
    </script>

</body>
</html>
