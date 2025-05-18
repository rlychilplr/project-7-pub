<?php
if (!isset($_SESSION["nodeCompleted"]) || $_SESSION["nodeCompleted"] == 0) {
    if (!isset($_SESSION["nodeid"]) || $_SESSION["nodeid"] == 0) {
        // First-time player, set initial nodes as available
        $_SESSION["nodeid"] = 0; // Node 0 represents "not on a node yet"
    }
} ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map</title>
    <link rel="stylesheet" href="static/css/font.css">
    <link rel="stylesheet" href="static/css/map.css">
</head>

<body>

    <div class="mapContanier">

        <div class="mapImage">
            <div class="map">
                <img src='../public/static/images/rect3.png' class="svgInk" />

                <div class="node1 r11" id="n30"><img
                        src='../public/static/images/temp/images/ui/map/boss/guardian.png' /></div>

                <div class="node rest r10" id="n29" style="top: 470px; left: 820px;"><img
                        src='../public/static/images/temp/images/ui/map/rest.png' /></div>
                <div class="node rest r10" id="n28" style="top: 490px; left: 130px;"><img
                        src='../public/static/images/temp/images/ui/map/rest.png' /></div>

                <div class="node rest r9" id="n27" style="top: 640px; left: 450px;"><img
                        src='../public/static/images/temp/images/ui/map/rest.png' /></div>
                <div class="node monster r9" id="n26" style="top: 625px; left: 15px;"><img
                        src='../public/static/images/temp/images/ui/map/monster.png' /></div>
                <div class="node event r9" id="n25" style="top: 660px; left: 890px;"><img
                        src='../public/static/images/temp/images/ui/map/event.png' /></div>


                <div class="node rest r8" id="n24" style="top: 770px; left: 890px;"><img
                        src='../public/static/images/temp/images/ui/map/rest.png' /></div>
                <div class="node elite r8" id="n23" style="top: 810px; left: 500px;"><img
                        src='../public/static/images/temp/images/ui/map/elite.png' /></div>
                <div class="node monster r8" id="n22" style="top: 770px; left: 70px;"><img
                        src='../public/static/images/temp/images/ui/map/monster.png' /></div>

                <div class="node monster r7" id="n21" style="top: 925px; left: 870px;"><img
                        src='../public/static/images/temp/images/ui/map/monster.png' /></div>
                <div class="node event r7" id="n20" style="top: 950px; left: 380px;"><img
                        src='../public/static/images/temp/images/ui/map/event.png' /></div>
                <div class="node monster r7" id="n19" style="top: 940px; left: 45px;"><img
                        src='../public/static/images/temp/images/ui/map/monster.png' /></div>

                <div class="node chest r6" id="n18" style="top:1110px; left: 865px;"><img
                        src='../public/static/images/temp/images/ui/map/chest.png' /></div>
                <div class="node chest r6" id="n17" style="top: 1070px; left: 610px;"><img
                        src='../public/static/images/temp/images/ui/map/chest.png' /></div>
                <div class="node chest r6" id="n16" style="top: 1148px; left: 360px;"><img
                        src='../public/static/images/temp/images/ui/map/chest.png' /></div>
                <div class="node chest r6" id="n15" style="top: 1146px; left: 20px;"><img
                        src='../public/static/images/temp/images/ui/map/chest.png' /></div>

                <div class="node rest r5" id="n14" style="top: 1295px; left: 830px;"><img
                        src='../public/static/images/temp/images/ui/map/rest.png' /></div>
                <div class="node rest r5" id="n13" style="top: 1280px; left: 430px;"><img
                        src='../public/static/images/temp/images/ui/map/rest.png' /></div>
                <div class="node event r5" id="n12" style="top: 1300px; left: 60px;"><img
                        src='../public/static/images/temp/images/ui/map/event.png' /></div>

                <div class="node monster r4" id="n11" style="top: 1470px; left: 600px;"><img
                        src='../public/static/images/temp/images/ui/map/monster.png' /></div>
                <div class="node monster r4" id="n10" style="top: 1470px; left: 110px;"><img
                        src='../public/static/images/temp/images/ui/map/monster.png' /></div>

                <div class="node monster r3" id="n9" style="bottom: 890px; left: 225px;"><img
                        src='../public/static/images/temp/images/ui/map/monster.png' /></div>
                <div class="node monster r3" id="n8" style="bottom: 890px; left: 830px;"><img
                        src='../public/static/images/temp/images/ui/map/monster.png' /></div>

                <div class="node event r2" id="n7" style="bottom: 750px; left: 880px;"><img
                        src='../public/static/images/temp/images/ui/map/event.png' /></div>
                <div class="node monster r2" id="n6" style="bottom: 765px; left: 730px;"><img
                        src='../public/static/images/temp/images/ui/map/monster.png' /></div>
                <div class="node monster r2" id="n5" style="bottom: 740px; left: 390px;"><img
                        src='../public/static/images/temp/images/ui/map/monster.png' /></div>
                <div class="node monster r2" id="n4" style="bottom: 750px; left: 120px;"><img
                        src='../public/static/images/temp/images/ui/map/monster.png' /></div>

                <div class="node monster r1" id="n3" style="bottom: 600px; left: 470px;"><img
                        src='../public/static/images/temp/images/ui/map/monster.png' /></div>
                <div class="node monster r1" id="n2" style="bottom: 600px; left: 200px;"><img
                        src='../public/static/images/temp/images/ui/map/monster.png' /></div>
                <div class="node monster r1" id="n1" style="bottom: 600px; left: 780px;"><img
                        src='../public/static/images/temp/images/ui/map/monster.png' /></div>
            </div>
        </div>

        <div class="legend2">

            <div class="titel">
                <div class="legendT">
                    <h1>Legend</h1>
                </div>
                <div class="backLegend" data-imageName="event"> <img
                        src='../public/static/images/temp/images/ui/map/event.png' />Unknowen </div>
                <div class="backLegend" data-imageName="shop"> <img
                        src='../public/static/images/temp/images/ui/map/shop.png' />Merchant</div>
                <div class="backLegend" data-imageName="chest"> <img
                        src='../public/static/images/temp/images/ui/map/chest.png' />Treasure</div>

                <div class="backLegend" data-imageName="rest"> <img
                        src='../public/static/images/temp/images/ui/map/rest.png' />Rest</div>
                <div class="backLegend" data-imageName="monster"> <img
                        src='../public/static/images/temp/images/ui/map/monster.png' />Enemy</div>

                <div class="backLegend" data-imageName="elite"> <img
                        src='../public/static/images/temp/images/ui/map/elite.png' />Elite</div>
            </div>
        </div>

        <script>

            const pathConnections = {
                '0': ['1', '2', '3'],
                '1': ['6', '7'],
                '2': ['4'],
                '3': ['5'],
                '4': ['9'],
                '5': ['9'],
                '6': ['8'],
                '7': ['8'],
                '8': ['11'],
                '9': ['10'],
                '10': ['12', '13'],
                '11': ['13', '14'],
                '12': ['15'],
                '13': ['16', '17'],
                '14': ['18'],
                '15': ['19'],
                '16': ['19', '20'],
                '17': ['21'],
                '18': ['21'],
                '19': ['22'],
                '20': ['22', '23'],
                '21': ['23', '24'],
                '22': ['26'],
                '23': ['27'],
                '24': ['25'],
                '25': ['29'],
                '26': ['28'],
                '27': ['30'],
                '28': ['30'],
                '29': ['30']
            };

            // Define node types for special routing
            const nodeTypes = {
                // Boss node
                '30': 'boss',
                // Elite nodes
                '23': 'elite',
                // Rest nodes
                '13': 'rest',
                '14': 'rest',
                '24': 'rest',
                '27': 'rest',
                '28': 'rest',
                '29': 'rest',
                // Treasure (chest) nodes
                '15': 'treasure',
                '16': 'treasure',
                '17': 'treasure',
                '18': 'treasure',
                // Event nodes - we'll just default to game controller for now
                '7': 'event',
                '12': 'event',
                '20': 'event',
                '25': 'event'
            };

            // Function to determine random event outcome
            function getRandomEventOutcome() {
                const randomValue = Math.random() * 100; // Random value between 0 and 100

                if (randomValue < 40) {
                    return 'game';     // 40% chance - regular enemy
                } else if (randomValue < 70) {
                    return 'elite';    // 30% chance - elite encounter
                } else {
                    return 'treasure'; // 30% chance - treasure
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                // Get all backLegend elements
                const legendItems = document.querySelectorAll('.backLegend');

                // Add hover listeners to each legend item
                legendItems.forEach(item => {
                    // Mouse enter (hover start)
                    item.addEventListener('mouseenter', function () {
                        const imageName = this.getAttribute('data-imageName');
                        const matchingNodes = document.querySelectorAll(`.node.${imageName}`);
                        // Highlight matching nodes
                        matchingNodes.forEach(node => {
                            node.classList.add('node-active');
                        });
                    });

                    // Mouse leave (hover end)
                    item.addEventListener('mouseleave', function () {
                        const imageName = this.getAttribute('data-imageName');
                        const matchingNodes = document.querySelectorAll(`.node.${imageName}`);
                        // Highlight matching nodes
                        matchingNodes.forEach(node => {
                            node.classList.remove('node-active');
                        });
                    });
                });

                let availableNodes = [];

                if (<?php echo isset($_SESSION["nodeCompleted"]) &&
                $_SESSION["nodeCompleted"] == 1
                    ? "true"
                    : "false"; ?>) {
                        const currentNodeId = <?php echo isset(
                            $_SESSION["nodeid"]
                        )
                            ? $_SESSION["nodeid"]
                            : 0; ?>;
                        availableNodes = pathConnections[currentNodeId] || [];
                    }
                    // If it's a new game or no node is completed, make starting nodes available
                    else {
                        // Starting nodes are 1, 2, and 3
                        availableNodes = ['1', '2', '3'];
                    }

                console.log('Available nodes:', availableNodes);

                // Make available nodes clickable
                availableNodes.forEach(nodeId => {
                    // Get the node element
                    let nodeElement = document.querySelector('#n' + nodeId);

                    if (nodeElement) {
                        // Add click event with routing based on node type
                        nodeElement.addEventListener('click', function() {
                            let idNum = this.id.replace('n', '');
                            let route = 'game'; // Default route

                            // Check for special node types
                            if (nodeTypes[idNum]) {
                                switch(nodeTypes[idNum]) {
                                    case 'boss':
                                        route = 'boss';
                                        break;
                                    case 'elite':
                                        route = 'elite';
                                        break;
                                    case 'rest':
                                        route = 'rest';
                                        break;
                                    case 'treasure':
                                        route = 'treasure';
                                        break;
                                    case 'event':
                                        // For event nodes, determine random outcome (40/30/30 split)
                                        route = getRandomEventOutcome();
                                        break;
                                }
                            }

                            // Store node ID in URL parameter and redirect
                            window.location.href = route + '?node_id=' + idNum;
                        });

                        // Add visual indicator that node is clickable
                        nodeElement.classList.add('open-node');
                    }
                });
            });
        </script>



</body>

</html>
