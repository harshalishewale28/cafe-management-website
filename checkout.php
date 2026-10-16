<?php

session_start();

include "config/database.php";


// ==========================================
// CHECK LOGIN
// ==========================================

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit();

}


// ==========================================
// CHECK USER
// ==========================================

$userId = (int)$_SESSION["user_id"];

$userStmt = $conn->prepare(
    "SELECT id, name, email
     FROM users
     WHERE id = ?
     LIMIT 1"
);

$userStmt->bind_param("i", $userId);

$userStmt->execute();

$userResult = $userStmt->get_result();

if ($userResult->num_rows == 0) {

    die("User not found.");

}

$user = $userResult->fetch_assoc();

$userStmt->close();


// ==========================================
// PLACE ORDER
// ==========================================

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $cartJson = $_POST["cart"] ?? "";

    $cart = json_decode($cartJson, true);


    // Check cart

    if (!is_array($cart) || count($cart) === 0) {

        $message = "Your cart is empty.";
        $messageType = "danger";

    }

    else {

        $total = 0;

        $validItems = [];


        // ======================================
        // VERIFY PRODUCTS FROM DATABASE
        // ======================================

        foreach ($cart as $item) {

            $foodId = isset($item["id"])
                ? (int)$item["id"]
                : 0;

            $quantity = isset($item["quantity"])
                ? (int)$item["quantity"]
                : 0;


            if ($foodId <= 0 || $quantity <= 0) {
                continue;
            }


            $foodStmt = $conn->prepare(
                "SELECT id, name, price
                 FROM food_items
                 WHERE id = ?
                 LIMIT 1"
            );

            $foodStmt->bind_param(
                "i",
                $foodId
            );

            $foodStmt->execute();

            $foodResult =
                $foodStmt->get_result();


            if ($foodResult->num_rows == 1) {

                $food =
                    $foodResult->fetch_assoc();


                $price =
                    (float)$food["price"];


                $itemTotal =
                    $price * $quantity;


                $total += $itemTotal;


                $validItems[] = [

                    "food_id" => $foodId,

                    "name" => $food["name"],

                    "price" => $price,

                    "quantity" => $quantity

                ];

            }


            $foodStmt->close();

        }


        // ======================================
        // CHECK VALID ITEMS
        // ======================================

        if (count($validItems) === 0) {

            $message =
                "No valid food items found.";

            $messageType =
                "danger";

        }

        else {


            // ==================================
            // START TRANSACTION
            // ==================================

            $conn->begin_transaction();


            try {


                // ==================================
                // INSERT ORDER
                // ==================================

                $orderStatus =
                    "Pending";


                $orderStmt =
                    $conn->prepare(
                        "INSERT INTO orders
                        (
                            customer_id,
                            total_amount,
                            order_status,
                            order_date
                        )
                        VALUES (?, ?, ?, NOW())"
                    );


                /*
                 * IMPORTANT:
                 *
                 * This project currently uses users
                 * as the logged-in customer.
                 *
                 * Therefore customer_id stores
                 * the users.id value.
                 */

                $orderStmt->bind_param(
                    "ids",
                    $userId,
                    $total,
                    $orderStatus
                );


                if (!$orderStmt->execute()) {

                    throw new Exception(
                        "Order could not be created: "
                        . $orderStmt->error
                    );

                }


                $orderId =
                    $conn->insert_id;


                $orderStmt->close();


                // ==================================
                // INSERT ORDER ITEMS
                // ==================================

                $itemStmt =
                    $conn->prepare(
                        "INSERT INTO order_items
                        (
                            order_id,
                            food_id,
                            quantity,
                            price
                        )
                        VALUES (?, ?, ?, ?)"
                    );


                foreach ($validItems as $item) {

                    $foodId =
                        $item["food_id"];

                    $quantity =
                        $item["quantity"];

                    $price =
                        $item["price"];


                    $itemStmt->bind_param(
                        "iiid",
                        $orderId,
                        $foodId,
                        $quantity,
                        $price
                    );


                    if (!$itemStmt->execute()) {

                        throw new Exception(
                            "Order item could not be saved: "
                            . $itemStmt->error
                        );

                    }

                }


                $itemStmt->close();


                // ==================================
                // COMMIT
                // ==================================

                $conn->commit();


                // ==================================
                // GO TO BILL
                // ==================================

                header(
                    "Location: bill.php?order_id="
                    . $orderId
                );

                exit();


            }

            catch (Exception $e) {

                $conn->rollback();


                $message =
                    $e->getMessage();

                $messageType =
                    "danger";

            }

        }

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Checkout - My Cafe</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>


<body class="bg-light">


<!-- ==========================================
     NAVBAR
========================================== -->

<nav class="navbar navbar-dark bg-dark">

    <div class="container">

        <a
            href="index.php"
            class="navbar-brand fw-bold">

            ☕ My Cafe

        </a>


        <div>

            <a
                href="menu.php"
                class="btn btn-outline-light me-2">

                Menu

            </a>


            <a
                href="cart.php"
                class="btn btn-warning">

                Cart

            </a>

        </div>

    </div>

</nav>



<!-- ==========================================
     CHECKOUT
========================================== -->

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-7">

            <div class="card shadow border-0">

                <div class="card-body p-4">


                    <h2
                        class="text-center fw-bold mb-4">

                        🧾 Checkout

                    </h2>


                    <?php if (!empty($message)) { ?>

                        <div
                            class="alert alert-<?php
                            echo htmlspecialchars(
                                $messageType
                            );
                            ?>">

                            <?php
                            echo htmlspecialchars(
                                $message
                            );
                            ?>

                        </div>

                    <?php } ?>


                    <!-- CUSTOMER -->

                    <div class="card bg-light mb-4">

                        <div class="card-body">

                            <h5>
                                Customer Details
                            </h5>

                            <p class="mb-1">

                                <strong>Name:</strong>

                                <?php
                                echo htmlspecialchars(
                                    $user["name"]
                                );
                                ?>

                            </p>


                            <p class="mb-0">

                                <strong>Email:</strong>

                                <?php
                                echo htmlspecialchars(
                                    $user["email"]
                                );
                                ?>

                            </p>

                        </div>

                    </div>


                    <!-- ORDER SUMMARY -->

                    <h5 class="mb-3">
                        Order Summary
                    </h5>


                    <div id="checkoutItems">
                    </div>


                    <div
                        class="d-flex justify-content-between
                               border-top pt-3 mt-3">

                        <h4>
                            Total
                        </h4>

                        <h4
                            id="checkoutTotal"
                            class="text-success">

                            ₹0.00

                        </h4>

                    </div>


                    <!-- FORM -->

                    <form
                        method="POST"
                        id="orderForm">

                        <input
                            type="hidden"
                            name="cart"
                            id="cartInput">


                        <button
                            type="submit"
                            class="btn btn-success btn-lg w-100 mt-4">

                            🛒 Place Order

                        </button>


                    </form>


                    <a
                        href="cart.php"
                        class="btn btn-outline-secondary w-100 mt-2">

                        ← Back to Cart

                    </a>


                </div>

            </div>

        </div>

    </div>

</div>



<script>


// ==========================================
// GET CART
// ==========================================

let cart =
    JSON.parse(
        localStorage.getItem("cafeCart")
    ) || [];


// ==========================================
// DISPLAY CART
// ==========================================

function displayCheckout() {

    const container =
        document.getElementById(
            "checkoutItems"
        );


    const totalElement =
        document.getElementById(
            "checkoutTotal"
        );


    const cartInput =
        document.getElementById(
            "cartInput"
        );


    if (cart.length === 0) {

        container.innerHTML = `

            <div class="alert alert-warning">

                Your cart is empty.

            </div>

        `;


        totalElement.innerText =
            "₹0.00";


        cartInput.value =
            "[]";


        return;

    }


    let html = "";

    let total = 0;


    cart.forEach(function(item) {

        let price =
            Number(item.price);

        let quantity =
            Number(item.quantity);


        let itemTotal =
            price * quantity;


        total += itemTotal;


        html += `

            <div
                class="d-flex
                       justify-content-between
                       border-bottom
                       py-3">

                <div>

                    <strong>
                        ${escapeHtml(item.name)}
                    </strong>

                    <br>

                    <small
                        class="text-muted">

                        ₹${price.toFixed(2)}
                        ×
                        ${quantity}

                    </small>

                </div>


                <strong>

                    ₹${itemTotal.toFixed(2)}

                </strong>

            </div>

        `;

    });


    container.innerHTML =
        html;


    totalElement.innerText =
        "₹" + total.toFixed(2);


    cartInput.value =
        JSON.stringify(cart);

}


// ==========================================
// ESCAPE HTML
// ==========================================

function escapeHtml(text) {

    return String(text)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");

}


// ==========================================
// PREVENT EMPTY ORDER
// ==========================================

document
    .getElementById("orderForm")
    .addEventListener(
        "submit",
        function(event) {

            if (cart.length === 0) {

                event.preventDefault();

                alert(
                    "Your cart is empty."
                );

            }

        }
    );


// ==========================================
// LOAD
// ==========================================

displayCheckout();

</script>


</body>

</html>