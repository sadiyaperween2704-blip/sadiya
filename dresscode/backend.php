<?php
// Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "dresscode_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Get User Inputs
$name = $_POST['name'] ?? '';
$gender = $_POST['gender'] ?? '';
$occasion = $_POST['occasion'] ?? 'casual';
$season = $_POST['season'] ?? 'summer';
$style_pref = $_POST['style_pref'] ?? 'trendy';
$fabric_pref = $_POST['fabric_pref'] ?? 'cotton';
$budget = $_POST['budget'] ?? 'medium';

// Fashion Database
$fashion_db = [
    ["Dress"=>"Elegant Saree with Designer Blouse","Gender"=>"female","Occasion"=>"wedding","Season"=>"summer","Fabric"=>"silk","Style"=>"traditional"],
    ["Dress"=>"Western Bodycon Dress","Gender"=>"female","Occasion"=>"party","Season"=>"summer","Fabric"=>"polyester","Style"=>"modern"],
    ["Dress"=>"Kurti with Palazzo","Gender"=>"female","Occasion"=>"casual","Season"=>"all","Fabric"=>"cotton","Style"=>"traditional"],
    ["Dress"=>"Casual T-shirt with Jeans","Gender"=>"male","Occasion"=>"casual","Season"=>"all","Fabric"=>"cotton","Style"=>"trendy"],
    ["Dress"=>"Formal Suit with Tie","Gender"=>"male","Occasion"=>"business","Season"=>"winter","Fabric"=>"wool","Style"=>"classic"],
    ["Dress"=>"Maxi Dress with Shrug","Gender"=>"female","Occasion"=>"dinner","Season"=>"spring","Fabric"=>"chiffon","Style"=>"elegant"]
];

// Makeup, Hairstyles, Accessories, Footwear
$makeup = [
    ["Look"=>"Smokey Eyes & Nude Lips","Gender"=>"female"],
    ["Look"=>"Glossy Pink Look","Gender"=>"female"],
    ["Look"=>"Natural Grooming","Gender"=>"male"]
];

$hairstyles = [
    ["Style"=>"Soft Curls","Gender"=>"female"],
    ["Style"=>"High Ponytail","Gender"=>"female"],
    ["Style"=>"Fade Cut","Gender"=>"male"]
];

$accessories = [
    ["Item"=>"Diamond Earrings","Gender"=>"female"],
    ["Item"=>"Luxury Watch","Gender"=>"male"]
];

$footwear = [
    ["Item"=>"High Heels","Gender"=>"female"],
    ["Item"=>"Sneakers","Gender"=>"male"]
];

// Filter outfits
$suggestions = array_filter($fashion_db, function($item) use ($gender, $occasion, $season, $style_pref, $fabric_pref) {
    return ($item['Gender'] == $gender &&
           ($item['Occasion'] == $occasion || $item['Occasion'] == 'casual') &&
           ($item['Season'] == $season || $item['Season'] == 'all') &&
           ($item['Style'] == $style_pref || $style_pref == 'trendy') &&
           ($item['Fabric'] == $fabric_pref || $fabric_pref == 'cotton'));
});
if(empty($suggestions)) {
    $suggestions = $fashion_db;
}
shuffle($suggestions);
$suggestions = array_slice($suggestions, 0, 5);

$selected_makeup = array_values(array_filter($makeup, fn($m) => $m['Gender']==$gender));
$selected_hairstyle = array_values(array_filter($hairstyles, fn($h) => $h['Gender']==$gender));
$selected_accessories = array_values(array_filter($accessories, fn($a) => $a['Gender']==$gender));
$selected_footwear = array_values(array_filter($footwear, fn($f) => $f['Gender']==$gender));

// Insert Data
$stmt = $conn->prepare("INSERT INTO users (name, gender, occasion, season, style_pref, fabric_pref, budget) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $name, $gender, $occasion, $season, $style_pref, $fabric_pref, $budget);
$stmt->execute();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>✨ Your Personalized Style Guide ✨</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #ffdde1, #ee9ca7, #f6d365, #fda085);
    margin: 0;
    padding: 20px;
    color: #fff;
    text-align: center;
}

h2 {
    color: #ffeb3b;
    margin-bottom: 15px;
}

.slider {
    display: flex;
    gap: 20px;
    overflow-x: auto;
    padding: 20px;
    scroll-behavior: smooth;
}

.card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(8px);
    border-radius: 12px;
    min-width: 250px;
    padding: 20px;
    transition: 0.4s;
    cursor: pointer;
    animation: slideUp 0.7s ease-in-out;
}
.card:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.3);
}
p {
    font-size: 16px;
    font-weight: 500;
}

.section-title {
    font-size: 20px;
    margin-top: 20px;
    color: #fff;
}

@keyframes slideUp {
    from {opacity: 0; transform: translateY(20px);}
    to {opacity: 1; transform: translateY(0);}
}
</style>
</head>
<body>

<h2>Hi <?php echo $name; ?>! 🌟 Here's your premium style guide:</h2>

<h3 class="section-title">👗 Outfit Recommendations</h3>
<div class="slider">
    <?php foreach ($suggestions as $item) { ?>
        <div class="card"><p>• <?php echo $item['Dress']; ?></p></div>
    <?php } ?>
</div>

<h3 class="section-title">💄 Makeup Suggestions</h3>
<div class="slider">
    <?php foreach ($selected_makeup as $m) { ?>
        <div class="card"><p>• <?php echo $m['Look']; ?></p></div>
    <?php } ?>
</div>

<h3 class="section-title">💇 Hairstyles</h3>
<div class="slider">
    <?php foreach ($selected_hairstyle as $h) { ?>
        <div class="card"><p>• <?php echo $h['Style']; ?></p></div>
    <?php } ?>
</div>

<h3 class="section-title">💍 Accessories</h3>
<div class="slider">
    <?php foreach ($selected_accessories as $a) { ?>
        <div class="card"><p>• <?php echo $a['Item']; ?></p></div>
    <?php } ?>
</div>

<h3 class="section-title">👟 Footwear</h3>
<div class="slider">
    <?php foreach ($selected_footwear as $f) { ?>
        <div class="card"><p>• <?php echo $f['Item']; ?></p></div>
    <?php } ?>
</div>

</body>
</html>
