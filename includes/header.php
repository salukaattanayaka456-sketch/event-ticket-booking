<?php
// Shared page header
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SriLanka Tickets - Online Event Booking</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <div class="header-inner">
        <div class="brand-wrap">
            <img src="assets/logo.png" alt="SriLanka Tickets Logo" class="logo">
            <div class="brand">
                <h1>SriLanka Tickets</h1>
                <p>Online Event Ticket Reservation System</p>
            </div>
        </div>
        <nav class="main-nav" aria-label="Main navigation">
            <a href="index.php">Home</a>
            <a href="#events">Events</a>
            <a href="cancel.php">Check Booking</a>
        </nav>
    </div>
</header>
