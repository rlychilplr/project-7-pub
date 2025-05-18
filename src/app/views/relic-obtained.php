<?php
/**
 * @var Relic $relic
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Relic Obtained | Slay the Spire Recreation</title>
    <link rel="stylesheet" href="static/css/font.css">
    <link rel="stylesheet" href="static/css/reward.css">
    <style>
        .relic-container {
            text-align: center;
            max-width: 300px;
            margin: 0 auto;
        }

        .relic-image {
            width: 128px;
            height: 128px;
            margin: 0 auto;
            display: block;
        }

        .relic-name {
            font-size: 24px;
            color: gold;
            margin: 10px 0;
        }

        .relic-description {
            font-size: 16px;
            margin: 10px 0 30px;
        }
    </style>
</head>
<body>
    <div class="reward-container">
        <h1>Relic Obtained!</h1>

        <div class="relic-container">
            <img src="<?php echo $relic->getImage(); ?>" alt="<?php echo $relic->getName(); ?>" class="relic-image">
            <div class="relic-name"><?php echo $relic->getName(); ?></div>
            <div class="relic-description"><?php echo $relic->getDescription(); ?></div>
        </div>

        <button onclick="window.location.href='map'">Continue</button>
    </div>
    <div class="background-2"></div>
    <div class="background"></div>
</body>
</html>
