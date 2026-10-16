<?php

session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Cart - My Cafe</title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <style>

        .cart-image {

            width: 80px;

            height: 80px;

            object-fit: cover;

            border-radius: 10px;

        }


        .quantity-btn {

            width: 35px;

            height: 35px;

            padding: 0;

        }

    </style>

</head>


<body class="bg-light">


<!-- ================= NAVBAR ================= -->

<nav class="navbar navbar-dark bg-dark">

    <div class="container">


        <a class="navbar-brand fw-bold"
           href="index.php">

            ☕ My Cafe

        </a>


        <div>

            <a href="menu.php"
               class="btn btn-outline-light me-2">

                ☕ Menu

            </a>


            <?php if (isset($_SESSION["user_id"])) { ?>

                <a href="dashboard.php"
                   class="btn btn-warning">

                    Dashboard

                </a>

            <?php } else { ?>

                <a href="login.php"
                   class="btn btn-warning">

                    Login

                </a>

            <?php } ?>


        </div>

    </div>

</nav>



<!-- ================= CART ================= -->

<div class="container mt-5">


    <h1 class="text-center fw-bold mb-4">

        🛒 My Cart

    </h1>


    <div id="cartContainer">

    </div>


</div>



<script>


// ===========================================
// LOAD CART
// ===========================================

let cart =
    JSON.parse(
        localStorage.getItem("cafeCart")
    ) || [];



// ===========================================
// DISPLAY CART
// ===========================================

function displayCart() {


    let container =
        document.getElementById(
            "cartContainer"
        );


    if (cart.length === 0) {


        container.innerHTML = `

            <div class="alert alert-info text-center">

                <h4>Your cart is empty.</h4>

                <p>
                    Add some delicious food from our menu.
                </p>

                <a
                    href="menu.php"
                    class="btn btn-primary">

                    Browse Menu

                </a>

            </div>

        `;


        return;

    }



    let html = "";

    let total = 0;



    cart.forEach(
        function(item, index) {


            let itemTotal =
                Number(item.price) *
                Number(item.quantity);


            total += itemTotal;



            html += `

                <div
                    class="card mb-3 shadow-sm">

                    <div
                        class="card-body">

                        <div
                            class="row
                                   align-items-center">


                            <!-- NAME -->

                            <div
                                class="col-md-4">

                                <h5
                                    class="mb-1">

                                    ${item.name}

                                </h5>


                                <small
                                    class="text-muted">

                                    ₹${Number(
                                        item.price
                                    ).toFixed(2)}
                                    each

                                </small>

                            </div>



                            <!-- QUANTITY -->

                            <div
                                class="col-md-3">

                                <div
                                    class="d-flex
                                           align-items-center
                                           gap-2">


                                    <button
                                        class="btn
                                               btn-outline-secondary
                                               quantity-btn"

                                        onclick="
                                            decreaseQuantity(
                                                ${index}
                                            )
                                        ">

                                        −

                                    </button>


                                    <span
                                        class="fw-bold">

                                        ${item.quantity}

                                    </span>


                                    <button
                                        class="btn
                                               btn-outline-secondary
                                               quantity-btn"

                                        onclick="
                                            increaseQuantity(
                                                ${index}
                                            )
                                        ">

                                        +

                                    </button>


                                </div>

                            </div>



                            <!-- ITEM TOTAL -->

                            <div
                                class="col-md-3">

                                <strong
                                    class="text-success">

                                    ₹${itemTotal.toFixed(2)}

                                </strong>

                            </div>



                            <!-- REMOVE -->

                            <div
                                class="col-md-2">

                                <button
                                    class="btn
                                           btn-danger
                                           btn-sm"

                                    onclick="
                                        removeItem(
                                            ${index}
                                        )
                                    ">

                                    Remove

                                </button>

                            </div>


                        </div>

                    </div>

                </div>

            `;

        }
    );



    // =====================================
    // TOTAL
    // =====================================

    html += `

        <div class="card shadow mt-4">

            <div class="card-body">


                <div
                    class="d-flex
                           justify-content-between">

                    <h5>

                        Subtotal

                    </h5>


                    <h5>

                        ₹${total.toFixed(2)}

                    </h5>

                </div>


                <hr>


                <div
                    class="d-flex
                           justify-content-between">

                    <h4>

                        Total

                    </h4>


                    <h4
                        class="text-success">

                        ₹${total.toFixed(2)}

                    </h4>

                </div>


                <div
                    class="text-end mt-3">


                    <button
                        class="btn
                               btn-success
                               btn-lg"

                        onclick="checkout()">

                        Proceed to Checkout

                    </button>


                </div>


            </div>

        </div>

    `;



    container.innerHTML = html;

}



// ===========================================
// INCREASE QUANTITY
// ===========================================

function increaseQuantity(index) {


    cart[index].quantity++;


    saveCart();


}



// ===========================================
// DECREASE QUANTITY
// ===========================================

function decreaseQuantity(index) {


    if (cart[index].quantity > 1) {

        cart[index].quantity--;

    }

    else {

        cart.splice(index, 1);

    }


    saveCart();

}



// ===========================================
// REMOVE ITEM
// ===========================================

function removeItem(index) {


    cart.splice(index, 1);


    saveCart();

}



// ===========================================
// SAVE CART
// ===========================================

function saveCart() {


    localStorage.setItem(
        "cafeCart",
        JSON.stringify(cart)
    );


    displayCart();

}



// ===========================================
// CHECKOUT
// ===========================================

function checkout() {


    if (cart.length === 0) {

        alert(
            "Your cart is empty."
        );

        return;

    }


    <?php if (!isset($_SESSION["user_id"])) { ?>


        alert(
            "Please login before checkout."
        );


        window.location.href =
            "login.php";


    <?php } else { ?>


        window.location.href =
            "checkout.php";


    <?php } ?>


}



// ===========================================
// DISPLAY CART
// ===========================================

displayCart();


</script>


</body>

</html>