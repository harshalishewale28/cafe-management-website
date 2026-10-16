<?php

include "config/database.php";

$sql = "SELECT * FROM food_items ORDER BY id DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Cafe - Menu</title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <style>

        body {
            background-color: #f8f9fa;
        }


        .menu-card {

            border: none;

            border-radius: 15px;

            overflow: hidden;

            transition: 0.3s;

        }


        .menu-card:hover {

            transform: translateY(-5px);

            box-shadow: 0 10px 25px rgba(0,0,0,0.15);

        }


        .food-image {

            height: 220px;

            width: 100%;

            object-fit: cover;

        }


        .price {

            font-size: 22px;

            font-weight: bold;

        }


        .search-box {

            max-width: 500px;

            margin: auto;

        }


        .category-btn {

            margin: 5px;

        }

    </style>

</head>


<body>


<!-- ================= NAVBAR ================= -->

<nav class="navbar navbar-dark bg-dark">

    <div class="container">


        <a class="navbar-brand fw-bold"
           href="index.php">

            ☕ My Cafe

        </a>


        <div>

            <a href="index.php"
               class="btn btn-outline-light me-2">

                Home

            </a>


            <a href="menu.php"
               class="btn btn-warning me-2">

                Menu

            </a>


            <a href="cart.php"
               class="btn btn-success">

                🛒 Cart

                <span
                    id="cartCount"
                    class="badge bg-light text-dark">

                    0

                </span>

            </a>

        </div>

    </div>

</nav>



<!-- ================= MENU ================= -->

<div class="container mt-5">


    <h1 class="text-center fw-bold">

        ☕ Our Cafe Menu

    </h1>


    <p class="text-center text-muted">

        Fresh food and delicious drinks made for you

    </p>



    <!-- ================= SEARCH ================= -->

    <div class="search-box mt-4 mb-4">

        <input
            type="text"
            id="searchInput"
            class="form-control form-control-lg"
            placeholder="🔍 Search food or drink..."
        >

    </div>



    <!-- ================= CATEGORY ================= -->

    <div class="text-center mb-4">


        <button
            class="btn btn-dark category-btn"
            onclick="filterCategory('All')">

            All

        </button>


        <button
            class="btn btn-outline-primary category-btn"
            onclick="filterCategory('Food')">

            Food

        </button>


        <button
            class="btn btn-outline-success category-btn"
            onclick="filterCategory('Drink')">

            Drinks

        </button>


        <button
            class="btn btn-outline-warning category-btn"
            onclick="filterCategory('Snacks')">

            Snacks

        </button>


    </div>



    <!-- ================= FOOD ================= -->

    <div class="row"
         id="foodContainer">


        <?php

        if ($result && $result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {

        ?>


        <div
            class="col-lg-4 col-md-6 mb-4 food-item"

            data-name="<?php
                echo htmlspecialchars(
                    strtolower($row['name'])
                );
            ?>"

            data-category="<?php
                echo htmlspecialchars(
                    strtolower($row['category'])
                );
            ?>"
        >


            <div class="card menu-card h-100 shadow-sm">


                <!-- IMAGE -->

                <img
                    src="images/<?php
                        echo htmlspecialchars(
                            $row['image']
                        );
                    ?>"

                    class="food-image"

                    alt="<?php
                        echo htmlspecialchars(
                            $row['name']
                        );
                    ?>"
                >



                <div class="card-body d-flex flex-column">


                    <!-- CATEGORY -->

                    <span
                        class="badge bg-primary align-self-start mb-2">

                        <?php
                        echo htmlspecialchars(
                            $row['category']
                        );
                        ?>

                    </span>



                    <!-- NAME -->

                    <h4 class="card-title fw-bold">

                        <?php
                        echo htmlspecialchars(
                            $row['name']
                        );
                        ?>

                    </h4>



                    <!-- DESCRIPTION -->

                    <p class="card-text text-muted">

                        <?php
                        echo htmlspecialchars(
                            $row['description']
                        );
                        ?>

                    </p>



                    <!-- PRICE -->

                    <p class="price text-success">

                        ₹<?php
                        echo number_format(
                            $row['price'],
                            2
                        );
                        ?>

                    </p>



                    <!-- ADD CART -->

                    <button
                        class="btn btn-primary w-100 mt-auto"

                        onclick="addToCart(
                            <?php echo (int)$row['id']; ?>,

                            '<?php
                            echo htmlspecialchars(
                                $row['name'],
                                ENT_QUOTES
                            );
                            ?>',

                            <?php echo (float)$row['price']; ?>
                        )">

                        🛒 Add to Cart

                    </button>


                </div>

            </div>

        </div>


        <?php

            }

        }

        else {

        ?>

            <div class="col-12">

                <div class="alert alert-warning text-center">

                    No food items available.

                </div>

            </div>

        <?php

        }

        ?>


    </div>

</div>



<!-- ================= FOOTER ================= -->

<footer class="bg-dark text-white text-center mt-5 p-4">

    <p class="mb-0">

        © 2026 My Cafe | Cafe Management System

    </p>

</footer>



<!-- ================= JAVASCRIPT ================= -->

<script>


// ========================================
// GET CART
// ========================================

let cart =
    JSON.parse(
        localStorage.getItem("cafeCart")
    ) || [];



// ========================================
// UPDATE CART COUNT
// ========================================

function updateCartCount() {

    let count = 0;


    cart.forEach(function(item) {

        count += Number(item.quantity);

    });


    document.getElementById(
        "cartCount"
    ).innerText = count;

}



// ========================================
// ADD TO CART
// ========================================

function addToCart(id, name, price) {


    // Check whether item already exists

    let existingItem =
        cart.find(function(item) {

            return item.id === id;

        });



    if (existingItem) {

        // Increase quantity

        existingItem.quantity++;

    }

    else {

        // Add new item

        cart.push({

            id: id,

            name: name,

            price: Number(price),

            quantity: 1

        });

    }



    // Save cart

    localStorage.setItem(
        "cafeCart",
        JSON.stringify(cart)
    );


    updateCartCount();


    alert(
        name + " added to cart!"
    );

}



// ========================================
// SEARCH
// ========================================

document
    .getElementById("searchInput")
    .addEventListener(
        "keyup",
        function() {


            let searchText =
                this.value.toLowerCase();


            let foods =
                document.querySelectorAll(
                    ".food-item"
                );


            foods.forEach(
                function(food) {


                    let foodName =
                        food.getAttribute(
                            "data-name"
                        );


                    if (
                        foodName.includes(
                            searchText
                        )
                    ) {

                        food.style.display = "";

                    }

                    else {

                        food.style.display =
                            "none";

                    }

                }
            );


        }
    );



// ========================================
// CATEGORY FILTER
// ========================================

function filterCategory(category) {


    let foods =
        document.querySelectorAll(
            ".food-item"
        );


    foods.forEach(
        function(food) {


            let foodCategory =
                food.getAttribute(
                    "data-category"
                );


            if (

                category === "All"

                ||

                foodCategory ===
                category.toLowerCase()

            ) {

                food.style.display = "";

            }

            else {

                food.style.display =
                    "none";

            }

        }
    );

}



// ========================================
// INITIAL CART COUNT
// ========================================

updateCartCount();


</script>


</body>

</html>