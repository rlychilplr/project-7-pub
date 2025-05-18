<?php
if (!isset($_SESSION["floor"])) {
    $_SESSION["floor"] = 1;
} ?>


    <link rel="stylesheet" href="static/css/header.css">
    <header>
        <div class="left">
            <div class="header-player-name">
                <?php echo $_SESSION["username"]; ?>
            </div>
            <div class="header-player-character">
                <?php echo $_SESSION["characterName"]; ?>
            </div>
            <div class="header-player-health">
                <img src="static/images/temp/images/ui/topPanel/panelHeart.png" alt="Health">
                <?php echo $_SESSION["playerHealth"] .
                    "/" .
                    $_SESSION["playerMaxHealth"]; ?>
            </div>
            <div class="header-player-money">
                <img src="static/images/temp/images/ui/topPanel/panelGoldBag.png" alt="Gold Bag">
                <?php echo $_SESSION["money"]; ?>
            </div>
        </div>
        <div class="middle">
            <img src="static/images/temp/images/ui/topPanel/floor.png" alt="stairs">
            <?php echo $_SESSION["floor"]; ?>
        </div>
        <div class="right">
            <div>
             <a href="">
                <img src="static/images/temp/images/ui/topPanel/map.png" alt="map">
            </a>
            </div>
            <div>
                <img src="static/images/temp/images/ui/topPanel/deck.png" alt="deck">
            </div>
            <div>
                <img src="static/images/temp/images/ui/topPanel/settings.png" alt="settings">
            </div>
        </div>
    </header>
